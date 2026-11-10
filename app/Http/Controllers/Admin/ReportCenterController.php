<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Webinar;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Certificate;
use App\Models\ExternalTraining;
use App\Models\IdpPlan;
use App\Models\AppSetting;
use App\Models\ActivityLog;
use App\Services\JpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportCenterController extends Controller
{
    protected JpService $jpService;

    public function __construct(JpService $jpService)
    {
        $this->jpService = $jpService;
    }

    /**
     * Memastikan hak akses pengguna untuk modul Laporan & Analytics
     */
    protected function checkAuthorizedUser(): User
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $allowedRoles = ['admin', 'super_admin', 'bkpsdm', 'admin_opd', 'pimpinan'];
        if (!in_array($user->role, $allowedRoles) && !$user->hasPermission('view_reports')) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka modul Laporan & Analytics.');
        }

        return $user;
    }

    /**
     * Terapkan batasan unit kerja bagi Admin OPD
     */
    protected function applyOpdScope(Request $request, array &$filters): void
    {
        $user = Auth::user();
        if ($user && $user->role === 'admin_opd' && !empty($user->opd_name)) {
            $filters['opd_name'] = $user->opd_name;
        }
    }

    /**
     * 1. PUSAT PELAPORAN & REKAPITULASI TERPADU (KAK XXII)
     * Menampilkan katalog 10 jenis laporan resmi KAK beserta statistik ringkas
     */
    public function index(Request $request)
    {
        $user = $this->checkAuthorizedUser();
        $currentYear = (int) $request->input('year', date('Y'));
        $appSetting = AppSetting::getSettings();

        // Agregasi metrik cepat untuk 10 jenis laporan
        $stats = [
            'total_courses'    => Course::count(),
            'total_webinars'   => Webinar::count(),
            'total_activities' => Course::count() + Webinar::count(),
            'total_asns'       => User::whereIn('role', ['learner', 'asn'])->count(),
            'total_attendances'=> Attendance::count(),
            'total_certs'      => Certificate::count(),
            'total_externals'  => ExternalTraining::count(),
            'total_idp'        => IdpPlan::count(),
            'total_opds'       => User::whereNotNull('opd_name')->where('opd_name', '!=', '')->distinct('opd_name')->count('opd_name'),
        ];

        // Daftar OPD untuk filter
        $availableOpds = User::whereNotNull('opd_name')
            ->where('opd_name', '!=', '')
            ->distinct()
            ->pluck('opd_name')
            ->sort()
            ->values();

        return view('admin.reports.index', compact('stats', 'availableOpds', 'currentYear', 'appSetting', 'user'));
    }

    /**
     * 2. REPORT BUILDER SEDERHANA (KAK XXII & XXXI)
     * Form dinamis untuk memilih entitas data, centang parameter/kolom, dan filter
     */
    public function builder(Request $request)
    {
        $user = $this->checkAuthorizedUser();
        $appSetting = AppSetting::getSettings();

        // Entitas default yang dipilih
        $entity = $request->input('entity', 'peserta');
        $filters = $request->all();
        $this->applyOpdScope($request, $filters);

        // Ambil konfigurasi kolom per entitas
        $entityDefinitions = $this->getEntityDefinitions();

        // Kolom yang dipilih oleh user
        $selectedColumns = $request->input('columns', []);
        if (empty($selectedColumns) && isset($entityDefinitions[$entity])) {
            $selectedColumns = $entityDefinitions[$entity]['default_columns'];
        }

        // Jalankan query data jika ada permintaan preview atau load builder
        $dataResult = $this->fetchEntityData($entity, $selectedColumns, $filters);

        $availableOpds = User::whereNotNull('opd_name')
            ->where('opd_name', '!=', '')
            ->distinct()
            ->pluck('opd_name')
            ->sort()
            ->values();

        return view('admin.reports.builder', compact(
            'entity',
            'entityDefinitions',
            'selectedColumns',
            'dataResult',
            'filters',
            'availableOpds',
            'appSetting',
            'user'
        ));
    }

    /**
     * 3. EKSPOR EXCEL / CSV DARI REPORT BUILDER
     */
    public function exportExcel(Request $request)
    {
        $user = $this->checkAuthorizedUser();
        $entity = $request->input('entity', 'peserta');
        $filters = $request->all();
        $this->applyOpdScope($request, $filters);

        $entityDefinitions = $this->getEntityDefinitions();
        if (!isset($entityDefinitions[$entity])) {
            abort(400, 'Entitas laporan tidak valid.');
        }

        $allCols = $entityDefinitions[$entity]['columns'];
        $selectedColumns = $request->input('columns', $entityDefinitions[$entity]['default_columns']);

        // Pastikan hanya kolom valid yang diambil
        $selectedColumns = array_values(array_intersect($selectedColumns, array_keys($allCols)));
        if (empty($selectedColumns)) {
            $selectedColumns = $entityDefinitions[$entity]['default_columns'];
        }

        // Ambil semua data tanpa paginasi untuk ekspor
        $filters['limit'] = 'all';
        $dataResult = $this->fetchEntityData($entity, $selectedColumns, $filters);
        $rows = $dataResult['rows'];

        $filename = 'laporan_' . $entity . '_' . date('Ymd_His') . '.csv';

        ActivityLog::record(
            'Ekspor Laporan Report Builder',
            'Ekspor laporan entitas: ' . $entityDefinitions[$entity]['label'] . ' (' . count($rows) . ' baris)'
        );

        return new StreamedResponse(function () use ($selectedColumns, $allCols, $rows, $entityDefinitions, $entity) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk kompatibilitas sempurna dengan Microsoft Excel di Windows
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Judul Dokumen di Baris Atas
            fputcsv($handle, ['PEMERINTAH KABUPATEN BERAU - BADAN KEPEGAWAIAN, PENDIDIKAN DAN PELATIHAN']);
            fputcsv($handle, ['LAPORAN: ' . strtoupper($entityDefinitions[$entity]['label'])]);
            fputcsv($handle, ['WAKTU EKSPOR: ' . now()->format('d/m/Y H:i:s') . ' WITA']);
            fputcsv($handle, []); // Baris kosong

            // Header Tabel
            $headers = [];
            foreach ($selectedColumns as $colKey) {
                $headers[] = $allCols[$colKey] ?? $colKey;
            }
            fputcsv($handle, $headers);

            // Data Baris
            foreach ($rows as $row) {
                $rowData = [];
                foreach ($selectedColumns as $colKey) {
                    $val = $row[$colKey] ?? '-';
                    // Format NIP sebagai string teks agar tidak dikonversi ke scientific notation di Excel
                    if ($colKey === 'nip' && !empty($val)) {
                        $val = "'" . $val;
                    }
                    $rowData[] = $val;
                }
                fputcsv($handle, $rowData);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
        ]);
    }

    /**
     * 4. CETAK RESMI PDF / PRINT REPORT BUILDER (A4 Landscape)
     */
    public function printReport(Request $request)
    {
        $user = $this->checkAuthorizedUser();
        $appSetting = AppSetting::getSettings();
        $entity = $request->input('entity', 'peserta');
        $filters = $request->all();
        $this->applyOpdScope($request, $filters);

        $entityDefinitions = $this->getEntityDefinitions();
        if (!isset($entityDefinitions[$entity])) {
            abort(400, 'Entitas laporan tidak valid.');
        }

        $allCols = $entityDefinitions[$entity]['columns'];
        $selectedColumns = $request->input('columns', $entityDefinitions[$entity]['default_columns']);
        if (is_string($selectedColumns)) {
            $selectedColumns = explode(',', $selectedColumns);
        }
        $selectedColumns = array_values(array_intersect($selectedColumns, array_keys($allCols)));

        $filters['limit'] = 'all';
        $dataResult = $this->fetchEntityData($entity, $selectedColumns, $filters);

        return view('admin.reports.print', [
            'entity'            => $entity,
            'entityMeta'        => $entityDefinitions[$entity],
            'selectedColumns'   => $selectedColumns,
            'allColumns'        => $allCols,
            'rows'              => $dataResult['rows'],
            'filters'           => $filters,
            'appSetting'        => $appSetting,
            'user'              => $user,
        ]);
    }

    /**
     * 5. CETAK TRANSKRIP LEARNING RECORD RESMI PER PEGAWAI ASN (KAK XIV)
     */
    public function learningRecordTranscript(User $targetUser, Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            abort(401);
        }

        // Hak akses: hanya admin/pimpinan/admin OPD yang relevan, atau ASN yang bersangkutan sendiri
        if ($currentUser->id !== $targetUser->id && !$currentUser->isAdmin() && !$currentUser->isPimpinan() && !$currentUser->isAdminOpd()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak transkrip pegawai lain.');
        }

        if ($currentUser->isAdminOpd() && $currentUser->opd_name !== $targetUser->opd_name && !$currentUser->isAdmin()) {
            abort(403, 'Anda hanya dapat mengakses transkrip pegawai di OPD Anda.');
        }

        $appSetting = AppSetting::getSettings();
        $currentYear = (int) $request->input('year', date('Y'));

        // Ambil riwayat kursus internal
        $internalEnrollments = Enrollment::with(['course.category', 'course.instructor'])
            ->where('user_id', $targetUser->id)
            ->where('progress_percent', 100)
            ->get();

        // Ambil sertifikat
        $certificates = Certificate::with('course')
            ->where('user_id', $targetUser->id)
            ->get()
            ->keyBy('course_id');

        // Ambil riwayat webinar
        $webinarAttendances = Attendance::with(['session.webinar', 'session.course'])
            ->where('user_id', $targetUser->id)
            ->where('status', 'hadir')
            ->get();

        // Ambil riwayat diklat eksternal yang approved
        $externalTrainings = ExternalTraining::where('user_id', $targetUser->id)
            ->where('status', 'approved')
            ->get();

        // JP perolehan tahun ini
        $jpStats = $targetUser->getJpByYear($currentYear);

        return view('admin.reports.transcript', compact(
            'targetUser',
            'currentYear',
            'appSetting',
            'internalEnrollments',
            'certificates',
            'webinarAttendances',
            'externalTrainings',
            'jpStats'
        ));
    }

    /**
     * Definisi entitas dan kamus kolom yang dapat dipilih di Report Builder
     */
    protected function getEntityDefinitions(): array
    {
        return [
            'peserta' => [
                'label'           => 'Data Peserta & Pembelajar ASN',
                'description'     => 'Profil pegawai ASN, penempatan OPD, jabatan, capaian jam pelajaran (JP), dan progres.',
                'icon'            => '👥',
                'default_columns' => ['nip', 'name', 'opd_name', 'jabatan', 'total_jp', 'jp_status', 'courses_count'],
                'columns'         => [
                    'nip'           => 'NIP Pegawai',
                    'name'          => 'Nama Lengkap ASN',
                    'opd_name'      => 'Perangkat Daerah (OPD)',
                    'bagian'        => 'Bagian / Bidang',
                    'jabatan'       => 'Jabatan',
                    'email'         => 'Alamat Email',
                    'phone'         => 'No. Handphone / WA',
                    'total_jp'      => 'Total Jam Pelajaran (JP)',
                    'jp_status'     => 'Kepatuhan 20 JP',
                    'courses_count' => 'Pelatihan Diikuti',
                    'certs_count'   => 'Sertifikat Diperoleh',
                    'status'        => 'Status Akun',
                ],
            ],

            'kegiatan' => [
                'label'           => 'Kegiatan Pembelajaran (Kursus & Webinar)',
                'description'     => 'Katalog program pelatihan digital, workshop live, kategori, narasumber, dan peserta.',
                'icon'            => '📚',
                'default_columns' => ['title', 'type', 'category', 'instructor', 'jp', 'participants_count', 'status'],
                'columns'         => [
                    'title'              => 'Judul Kegiatan',
                    'type'               => 'Tipe (Kursus/Webinar/In-Class)',
                    'category'           => 'Kategori Kompetensi',
                    'instructor'         => 'Instruktur / Narasumber',
                    'jp'                 => 'Bobot Jam Pelajaran (JP)',
                    'duration'           => 'Durasi Jam / Waktu',
                    'participants_count' => 'Jumlah Pendaftar',
                    'quota'              => 'Kuota Peserta',
                    'date'               => 'Jadwal Pelaksanaan',
                    'status'             => 'Status Publikasi',
                ],
            ],

            'kehadiran' => [
                'label'           => 'Presensi & Kehadiran Digital',
                'description'     => 'Catatan presensi per sesi pertemuan melalui QR Code atau tombol kehadiran resmi.',
                'icon'            => '📱',
                'default_columns' => ['session_title', 'user_name', 'user_nip', 'user_opd', 'attendance_time', 'status', 'duration_minutes'],
                'columns'         => [
                    'session_title'    => 'Nama Sesi Pertemuan',
                    'activity_title'   => 'Kegiatan Induk',
                    'user_name'        => 'Nama Peserta',
                    'user_nip'         => 'NIP',
                    'user_opd'         => 'Perangkat Daerah (OPD)',
                    'method'           => 'Metode Presensi (QR/Tombol)',
                    'attendance_time'  => 'Waktu Check-In',
                    'checkout_time'    => 'Waktu Check-Out',
                    'duration_minutes' => 'Durasi Hadir (Menit)',
                    'status'           => 'Status Kehadiran',
                ],
            ],

            'sertifikat' => [
                'label'           => 'Penerbitan E-Sertifikat Digital',
                'description'     => 'Daftar nomor sertifikat resmi, penerima, nama pelatihan, tanggal terbit, dan verifikasi.',
                'icon'            => '🏅',
                'default_columns' => ['certificate_number', 'user_name', 'user_nip', 'course_title', 'jp', 'issue_date', 'sign_by'],
                'columns'         => [
                    'certificate_number' => 'Nomor Sertifikat',
                    'user_name'          => 'Nama Penerima',
                    'user_nip'           => 'NIP ASN',
                    'user_opd'           => 'Perangkat Daerah (OPD)',
                    'course_title'       => 'Nama Pelatihan',
                    'jp'                 => 'Bobot JP',
                    'issue_date'         => 'Tanggal Diterbitkan',
                    'sign_by'            => 'Pejabat Penandatangan',
                    'verify_url'         => 'Tautan Verifikasi Online',
                ],
            ],

            'eksternal' => [
                'label'           => 'Pembelajaran Eksternal (Diklat Luar)',
                'description'     => 'Usulan rekognisi sertifikat pelatihan luar daerah oleh pegawai dan hasil verifikasi BKPSDM.',
                'icon'            => '🏛️',
                'default_columns' => ['title', 'organizer', 'user_name', 'user_nip', 'user_opd', 'jp', 'status', 'training_date'],
                'columns'         => [
                    'title'         => 'Nama Pelatihan Eksternal',
                    'organizer'     => 'Lembaga Penyelenggara',
                    'user_name'     => 'Nama Pegawai',
                    'user_nip'      => 'NIP Pegawai',
                    'user_opd'      => 'Perangkat Daerah (OPD)',
                    'jp'            => 'Jumlah JP',
                    'training_date' => 'Tanggal Pelaksanaan',
                    'status'        => 'Status Verifikasi',
                    'submitted_at'  => 'Waktu Pengajuan',
                    'verified_by'   => 'Verifikator BKPSDM',
                ],
            ],

            'idp' => [
                'label'           => 'Rencana Pengembangan Individu (IDP)',
                'description'     => 'Data master pemetaan kebutuhan kompetensi tahunan per pegawai ASN Kabupaten Berau.',
                'icon'            => '🧭',
                'default_columns' => ['nip', 'nama_asn', 'opd_name', 'tahun', 'judul_materi', 'batch_id', 'created_at'],
                'columns'         => [
                    'nip'          => 'NIP Pegawai',
                    'nama_asn'     => 'Nama ASN',
                    'opd_name'     => 'Perangkat Daerah (OPD)',
                    'tahun'        => 'Target Tahun IDP',
                    'judul_materi' => 'Judul Materi Kompetensi IDP',
                    'batch_id'     => 'ID Batch Impor',
                    'created_at'   => 'Tanggal Ditetapkan',
                ],
            ],

            'opd_summary' => [
                'label'           => 'Rekapitulasi Kepatuhan per OPD',
                'description'     => 'Agregasi tingkat pemenuhan kewajiban 20 JP per Perangkat Daerah di Kabupaten Berau.',
                'icon'            => '📊',
                'default_columns' => ['opd_name', 'total_asn', 'total_jp', 'avg_jp', 'compliant_asn', 'compliance_rate'],
                'columns'         => [
                    'opd_name'        => 'Nama Perangkat Daerah (OPD)',
                    'total_asn'       => 'Total Pegawai ASN',
                    'total_jp'        => 'Total Akumulasi JP OPD',
                    'avg_jp'          => 'Rata-rata JP per ASN',
                    'compliant_asn'   => 'ASN Memenuhi 20 JP',
                    'compliance_rate' => 'Tingkat Kepatuhan (%)',
                ],
            ],
        ];
    }

    /**
     * Query data dinamis sesuai entitas, kolom, dan filter yang diberikan
     */
    protected function fetchEntityData(string $entity, array $selectedColumns, array $filters): array
    {
        $currentYear = (int) ($filters['year'] ?? date('Y'));
        $search = trim($filters['search'] ?? '');
        $opd = $filters['opd_name'] ?? 'all';
        $limit = $filters['limit'] ?? 100;

        $rows = [];

        switch ($entity) {
            case 'peserta':
                $q = User::whereIn('role', ['learner', 'asn']);
                if ($opd && $opd !== 'all') {
                    $q->where('opd_name', $opd);
                }
                if ($search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%")
                            ->orWhere('jabatan', 'like', "%{$search}%");
                    });
                }
                if ($limit !== 'all') {
                    $q->limit((int) $limit);
                }
                $users = $q->withCount(['enrollments', 'certificates'])->get();

                foreach ($users as $u) {
                    $jp = $u->getJpByYear($currentYear);
                    $rows[] = [
                        'nip'           => $u->nip ?: '-',
                        'name'          => $u->name,
                        'opd_name'      => $u->opd_name ?: 'Belum Terdata',
                        'bagian'        => $u->bagian ?: '-',
                        'jabatan'       => $u->jabatan ?: '-',
                        'email'         => $u->email,
                        'phone'         => $u->phone ?: '-',
                        'total_jp'      => $jp['total_jp'] ?? 0,
                        'jp_status'     => ($jp['total_jp'] ?? 0) >= 20 ? 'Memenuhi 20 JP' : 'Belum Memenuhi',
                        'courses_count' => $u->enrollments_count,
                        'certs_count'   => $u->certificates_count,
                        'status'        => $u->is_active ? 'Aktif' : 'Non-Aktif',
                    ];
                }
                break;

            case 'kegiatan':
                // Gabungkan Course & Webinar
                $coursesQ = Course::with(['category', 'instructor'])->withCount('enrollments');
                $webinarsQ = Webinar::withCount('registrations');

                if ($search) {
                    $coursesQ->where('title', 'like', "%{$search}%");
                    $webinarsQ->where('title', 'like', "%{$search}%");
                }

                $courses = $coursesQ->get();
                $webinars = $webinarsQ->get();

                foreach ($courses as $c) {
                    $rows[] = [
                        'title'              => $c->title,
                        'type'               => 'Kursus e-Learning',
                        'category'           => $c->category?->name ?: 'Umum',
                        'instructor'         => $c->instructor?->name ?: 'Tim Instruktur',
                        'jp'                 => $c->jp ?? 2,
                        'duration'           => ($c->duration_hours ?? 2) . ' Jam',
                        'participants_count' => $c->enrollments_count,
                        'quota'              => $c->quota ?: 'Tidak Terbatas',
                        'date'               => $c->created_at->format('d/m/Y'),
                        'status'             => $c->is_published ? 'Terbit' : 'Draf',
                    ];
                }

                foreach ($webinars as $w) {
                    $rows[] = [
                        'title'              => $w->title,
                        'type'               => 'Webinar Live',
                        'category'           => 'Workshop / Seminar',
                        'instructor'         => $w->speaker_name ?: '-',
                        'jp'                 => $w->jp ?? 2,
                        'duration'           => ($w->time_start ?? '09:00') . ' - ' . ($w->time_end ?? '12:00'),
                        'participants_count' => $w->registrations_count,
                        'quota'              => $w->quota ?: 'Terbuka',
                        'date'               => $w->event_date ? $w->event_date->format('d/m/Y') : '-',
                        'status'             => 'Terjadwal',
                    ];
                }

                if ($limit !== 'all') {
                    $rows = array_slice($rows, 0, (int) $limit);
                }
                break;

            case 'kehadiran':
                $attQ = Attendance::with(['user', 'session.webinar', 'session.course']);
                if ($opd && $opd !== 'all') {
                    $attQ->whereHas('user', function ($u) use ($opd) {
                        $u->where('opd_name', $opd);
                    });
                }
                if ($search) {
                    $attQ->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%");
                    });
                }
                if ($limit !== 'all') {
                    $attQ->limit((int) $limit);
                }
                $attendances = $attQ->latest('checkin_time')->get();

                foreach ($attendances as $a) {
                    $rows[] = [
                        'session_title'    => $a->session?->title ?: 'Sesi Presensi',
                        'activity_title'   => $a->session?->webinar?->title ?: ($a->session?->course?->title ?: 'Kegiatan Mandiri'),
                        'user_name'        => $a->user?->name ?: '-',
                        'user_nip'         => $a->user?->nip ?: '-',
                        'user_opd'         => $a->user?->opd_name ?: '-',
                        'method'           => strtoupper($a->method ?: 'QR'),
                        'attendance_time'  => $a->checkin_time ? $a->checkin_time->format('d/m/Y H:i:s') : '-',
                        'checkout_time'    => $a->checkout_time ? $a->checkout_time->format('d/m/Y H:i:s') : '-',
                        'duration_minutes' => $a->duration_minutes ?: 0,
                        'status'           => ucfirst($a->status ?: 'hadir'),
                    ];
                }
                break;

            case 'sertifikat':
                $certQ = Certificate::with(['user', 'course']);
                if ($opd && $opd !== 'all') {
                    $certQ->whereHas('user', function ($u) use ($opd) {
                        $u->where('opd_name', $opd);
                    });
                }
                if ($search) {
                    $certQ->where(function ($q) use ($search) {
                        $q->where('certificate_number', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($u) use ($search) {
                              $u->where('name', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%");
                          });
                    });
                }
                if ($limit !== 'all') {
                    $certQ->limit((int) $limit);
                }
                $certs = $certQ->latest('issue_date')->get();

                foreach ($certs as $c) {
                    $rows[] = [
                        'certificate_number' => $c->certificate_number ?: ('CERT-' . str_pad($c->id, 6, '0', STR_PAD_LEFT)),
                        'user_name'          => $c->user?->name ?: '-',
                        'user_nip'           => $c->user?->nip ?: '-',
                        'user_opd'           => $c->user?->opd_name ?: '-',
                        'course_title'       => $c->course?->title ?: 'Pelatihan ASN',
                        'jp'                 => $c->course?->jp ?? 2,
                        'issue_date'         => $c->issue_date ? $c->issue_date->format('d/m/Y') : $c->created_at->format('d/m/Y'),
                        'sign_by'            => 'Kepala BKPSDM Kab. Berau',
                        'verify_url'         => route('certificates.show', $c->certificate_number ?: $c->id),
                    ];
                }
                break;

            case 'eksternal':
                $extQ = ExternalTraining::with('user');
                if ($opd && $opd !== 'all') {
                    $extQ->whereHas('user', function ($u) use ($opd) {
                        $u->where('opd_name', $opd);
                    });
                }
                if ($search) {
                    $extQ->where(function ($q) use ($search) {
                        $q->where('training_name', 'like', "%{$search}%")
                          ->orWhere('organizer', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($u) use ($search) {
                              $u->where('name', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%");
                          });
                    });
                }
                if ($limit !== 'all') {
                    $extQ->limit((int) $limit);
                }
                $externals = $extQ->latest('training_date')->get();

                foreach ($externals as $ext) {
                    $rows[] = [
                        'title'         => $ext->training_name,
                        'organizer'     => $ext->organizer,
                        'user_name'     => $ext->user?->name ?: '-',
                        'user_nip'      => $ext->user?->nip ?: '-',
                        'user_opd'      => $ext->user?->opd_name ?: '-',
                        'jp'            => $ext->jp,
                        'training_date' => $ext->training_date ? $ext->training_date->format('d/m/Y') : '-',
                        'status'        => strtoupper($ext->status),
                        'submitted_at'  => $ext->created_at->format('d/m/Y H:i'),
                        'verified_by'   => $ext->status === 'approved' ? 'Tim Verifikator BKPSDM' : '-',
                    ];
                }
                break;

            case 'idp':
                $idpQ = IdpPlan::query();
                if ($search) {
                    $idpQ->where(function ($q) use ($search) {
                        $q->where('judul_materi', 'like', "%{$search}%")
                          ->orWhere('nip', 'like', "%{$search}%")
                          ->orWhere('nama_asn', 'like', "%{$search}%");
                    });
                }
                if ($limit !== 'all') {
                    $idpQ->limit((int) $limit);
                }
                $plans = $idpQ->orderBy('nip')->orderBy('tahun')->get();

                // Dapatkan OPD user berdasarkan NIP
                $nips = $plans->pluck('nip')->unique()->filter()->toArray();
                $userMap = User::whereIn('nip', $nips)->get()->keyBy('nip');

                foreach ($plans as $p) {
                    $matchedUser = $userMap[$p->nip] ?? null;
                    $userOpd = $matchedUser ? $matchedUser->opd_name : 'Belum Terdata';

                    if ($opd && $opd !== 'all' && $userOpd !== $opd) {
                        continue;
                    }

                    $rows[] = [
                        'nip'          => $p->nip,
                        'nama_asn'     => $p->nama_asn ?: ($matchedUser?->name ?: '-'),
                        'opd_name'     => $userOpd,
                        'tahun'        => 'Tahun ' . $p->tahun,
                        'judul_materi' => $p->judul_materi,
                        'batch_id'     => $p->batch_id ?: '-',
                        'created_at'   => $p->created_at ? $p->created_at->format('d/m/Y') : '-',
                    ];
                }
                break;

            case 'opd_summary':
                $opdReport = $this->jpService->getOpdJpReport(['year' => $currentYear]);
                $rows = [];
                foreach ($opdReport['rows'] as $r) {
                    if ($opd && $opd !== 'all' && $r['opd_name'] !== $opd) {
                        continue;
                    }
                    $rows[] = [
                        'opd_name'        => $r['opd_name'],
                        'total_asn'       => $r['total_asn'],
                        'total_jp'        => $r['total_jp'],
                        'avg_jp'          => $r['avg_jp'],
                        'compliant_asn'   => $r['compliant_asn'],
                        'compliance_rate' => $r['compliance_rate'] . '%',
                    ];
                }
                break;
        }

        return [
            'rows'        => $rows,
            'total_count' => count($rows),
        ];
    }
}
