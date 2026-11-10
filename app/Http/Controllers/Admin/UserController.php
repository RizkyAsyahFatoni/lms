<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\RolePermission;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\ExternalTraining;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ProfileChangeRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\EncryptionService;

class UserController extends Controller
{
    private function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'bkpsdm'])) {
            abort(403, 'Akses ditolak. Khusus Super Admin / BKPSDM.');
        }
    }

    /**
     * Daftar Pengguna & Pengelolaan Role (KAK Fitur: registrasi/login, pengelolaan role, aktivasi/nonaktif akun)
     */
    public function index(Request $request): View
    {
        $this->checkAdmin();

        $query = User::withCount(['enrollments', 'certificates', 'externalTrainings'])->latest();

        // Pencarian (Nama, Email, NIP, OPD, Jabatan)
        // CATATAN KEAMANAN: NIP/NIK sekarang terenkripsi di database.
        // Pencarian NIP menggunakan blind index (nip_hash) - exact match saja.
        // Pencarian NIK dihilangkan dari search (harus dekripsi satu per satu).
        if ($request->filled('q')) {
            $q = $request->q;

            // Hitung hash dari input pencarian untuk blind index lookup
            $enc = app(EncryptionService::class);
            $nipHash = $enc->hashNip($q);  // Jika user mengetik NIP lengkap

            $query->where(function ($sq) use ($q, $nipHash) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('nip_hash', $nipHash)  // Exact match via blind index
                   ->orWhere('opd_name', 'like', "%{$q}%")
                   ->orWhere('bagian', 'like', "%{$q}%")
                   ->orWhere('jabatan', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        // Filter Role
        if ($request->filled('role')) {
            $roleFilter = $request->role;
            if ($roleFilter === 'super_admin') {
                $query->whereIn('role', ['admin', 'super_admin', 'bkpsdm']);
            } elseif ($roleFilter === 'asn_learner') {
                $query->whereIn('role', ['student', 'asn_learner']);
            } elseif ($roleFilter === 'narasumber') {
                $query->whereIn('role', ['instructor', 'narasumber']);
            } else {
                $query->where('role', $roleFilter);
            }
        }

        // Filter Status Aktivasi (Aktif / Nonaktif)
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter OPD / Instansi
        if ($request->filled('opd')) {
            $query->where('opd_name', $request->opd);
        }

        $users = $query->paginate(100)->withQueryString();

        // Hitung statistik per role
        $stats = [
            'total'           => User::count(),
            'active'          => User::where('is_active', true)->count(),
            'inactive'        => User::where('is_active', false)->count(),
            'super_admin'     => User::whereIn('role', ['admin', 'super_admin', 'bkpsdm'])->count(),
            'admin_opd'       => User::where('role', 'admin_opd')->count(),
            'asn_learner'     => User::whereIn('role', ['student', 'asn_learner'])->count(),
            'narasumber'      => User::whereIn('role', ['instructor', 'narasumber'])->count(),
            'pimpinan'        => User::where('role', 'pimpinan')->count(),
            'reviewer_talent' => User::where('role', 'reviewer_talent')->count(),
        ];
        $roleCounts = $stats;

        // Hitung pengajuan verifikasi mandiri ASN yang berstatus pending
        $pendingVerificationsCount = ProfileChangeRequest::where('status', 'pending')->count();

        // Daftar unik OPD untuk filter
        $opdList = User::whereNotNull('opd_name')->where('opd_name', '!=', '')->distinct()->pluck('opd_name')->sort();
        $rolesDefinition = RolePermission::getRolesDefinition();

        return view('admin.users.index', compact('users', 'stats', 'roleCounts', 'opdList', 'rolesDefinition', 'pendingVerificationsCount'));
    }

    /**
     * Tambah Pengguna Baru oleh Admin
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'name'             => 'required|string|max:150',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|string|min:6',
            'role'             => 'required|in:super_admin,admin_opd,asn_learner,narasumber,pimpinan,reviewer_talent',
            'nip'              => 'nullable|string|max:30',
            'nik'              => 'nullable|string|max:20',
            'opd_name'         => 'nullable|string|max:255',
            'bagian'           => 'nullable|string|max:255',
            'jabatan'          => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'headline'         => 'nullable|string|max:255',
            'bio'              => 'nullable|string',
            'is_active'        => 'nullable|boolean',
        ]);

        $user = User::create([
            'name'             => strip_tags(trim($request->name)),
            'email'            => strtolower(trim($request->email)),
            'password'         => Hash::make($request->password),
            'role'             => $request->role,
            'nip'              => $request->filled('nip') ? preg_replace('/[^0-9]/', '', $request->nip) : null,
            'nik'              => $request->filled('nik') ? preg_replace('/[^0-9]/', '', $request->nik) : null,
            'opd_name'         => $request->opd_name,
            'bagian'           => $request->bagian,
            'jabatan'          => $request->jabatan,
            'pangkat_golongan' => $request->pangkat_golongan,
            'phone'            => $request->phone,
            'headline'         => $request->headline ?: ($request->jabatan ? "{$request->jabatan} di {$request->opd_name}" : null),
            'bio'              => $request->bio,
            'is_active'        => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(
            'create_user',
            "Menambahkan pengguna baru: {$user->name} ({$user->email}) dengan peran {$user->role_name}",
            auth()->id(),
            ['user_id' => $user->id, 'role' => $user->role]
        );

        return back()->with('success', "Akun [{$user->name}] berhasil dibuat dengan peran {$user->role_name}.");
    }

    /**
     * Detail Pengguna & Rekam Jejak
     */
    public function show(User $user): View
    {
        $this->checkAdmin();

        $appSetting = AppSetting::getSettings();
        $currentYear = (int) date('Y');

        $enrollments = Enrollment::with(['course.category', 'course.instructor', 'course.lessons'])
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        $completedCount = $enrollments->where('progress_percent', 100)->count();
        $inProgressCount = $enrollments->where('progress_percent', '<', 100)->count();
        $totalLearningHours = $enrollments->sum(function ($e) {
            return ($e->progress_percent / 100) * ($e->course->duration_hours ?? 2);
        });

        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $jpData = $user->getJpByYear($currentYear);
        $jpYearLabel = $appSetting->getJpYearLabel();

        $externalTrainings = ExternalTraining::where('user_id', $user->id)
            ->orderByDesc('training_date')
            ->get();

        $pendingExternalCount = $externalTrainings->where('status', 'pending')->count();

        // Aktivitas terbaru pengguna ini
        $userActivityLogs = ActivityLog::where('user_id', $user->id)->latest()->take(15)->get();
        $rolesDefinition = RolePermission::getRolesDefinition();

        return view('admin.users.show', compact(
            'user',
            'enrollments',
            'completedCount',
            'inProgressCount',
            'totalLearningHours',
            'certificates',
            'jpData',
            'jpYearLabel',
            'currentYear',
            'externalTrainings',
            'pendingExternalCount',
            'userActivityLogs',
            'rolesDefinition'
        ));
    }

    /**
     * Update Profil & Peran Pengguna (KAK: pengelolaan profil & role)
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'name'             => 'required|string|max:150',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'             => 'required|in:super_admin,admin_opd,asn_learner,narasumber,pimpinan,reviewer_talent,admin,instructor,student',
            'nip'              => 'nullable|string|max:30',
            'nik'              => 'nullable|string|max:20',
            'opd_name'         => 'nullable|string|max:255',
            'bagian'           => 'nullable|string|max:255',
            'jabatan'          => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'headline'         => 'nullable|string|max:255',
            'bio'              => 'nullable|string',
            'is_active'        => 'nullable|boolean',
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;

        $user->update([
            'name'             => strip_tags(trim($request->name)),
            'email'            => strtolower(trim($request->email)),
            'role'             => $newRole,
            'nip'              => $request->filled('nip') ? preg_replace('/[^0-9]/', '', $request->nip) : null,
            'nik'              => $request->filled('nik') ? preg_replace('/[^0-9]/', '', $request->nik) : null,
            'opd_name'         => $request->opd_name,
            'bagian'           => $request->bagian,
            'jabatan'          => $request->jabatan,
            'pangkat_golongan' => $request->pangkat_golongan,
            'phone'            => $request->phone,
            'headline'         => $request->headline,
            'bio'              => $request->bio,
            'is_active'        => $request->boolean('is_active', true),
        ]);

        // Catat aktivitas
        if ($oldRole !== $newRole) {
            ActivityLog::record(
                'change_role',
                "Mengubah peran [{$user->name}] dari '{$oldRole}' menjadi '{$newRole}'",
                auth()->id(),
                ['user_id' => $user->id, 'old_role' => $oldRole, 'new_role' => $newRole]
            );
        } else {
            ActivityLog::record(
                'update_user',
                "Memperbarui profil akun [{$user->name}]",
                auth()->id(),
                ['user_id' => $user->id]
            );
        }

        return back()->with('success', "Profil dan peran akun [{$user->name}] berhasil diperbarui!");
    }

    /**
     * Aktivasi / Nonaktifkan Akun Pengguna (KAK: aktivasi/nonaktif akun)
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        $this->checkAdmin();

        // Lindungi akun sendiri agar tidak menonaktifkan dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusText = $user->is_active ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';

        ActivityLog::record(
            'toggle_status',
            "Status akun [{$user->name}] telah {$statusText}",
            auth()->id(),
            ['user_id' => $user->id, 'is_active' => $user->is_active]
        );

        return back()->with('success', "Akun [{$user->name}] berhasil {$statusText}.");
    }

    /**
     * Reset Password Pengguna (KAK: reset password)
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'password' => 'nullable|string|min:6',
        ]);

        $newPassword = $request->filled('password') ? $request->password : 'Bulan' . rand(1000, 9999) . '!';
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        ActivityLog::record(
            'reset_password',
            "Mereset kata sandi akun [{$user->name}]",
            auth()->id(),
            ['user_id' => $user->id]
        );

        return back()->with('success', "Kata sandi untuk [{$user->name}] berhasil direset ke: {$newPassword}");
    }

    /**
     * Halaman Pengelolaan & Deskripsi Role (KAK: pengelolaan role)
     */
    public function roles(): View
    {
        $this->checkAdmin();

        $rolesDefinition = RolePermission::getRolesDefinition();
        $userCounts = [
            'super_admin'     => User::whereIn('role', ['admin', 'super_admin', 'bkpsdm'])->count(),
            'admin_opd'       => User::where('role', 'admin_opd')->count(),
            'asn_learner'     => User::whereIn('role', ['student', 'asn_learner'])->count(),
            'narasumber'      => User::whereIn('role', ['instructor', 'narasumber'])->count(),
            'pimpinan'        => User::where('role', 'pimpinan')->count(),
            'reviewer_talent' => User::where('role', 'reviewer_talent')->count(),
        ];

        $roleCounts = $userCounts;

        return view('admin.users.roles', compact('rolesDefinition', 'userCounts', 'roleCounts'));
    }

    /**
     * Matriks Pengaturan Hak Akses (KAK: pengaturan hak akses)
     */
    public function permissions(): View
    {
        $this->checkAdmin();

        $rolesDefinition = RolePermission::getRolesDefinition();
        $groupedPermissions = RolePermission::getAllPermissions();
        $permissionsByGroup = $groupedPermissions;

        // Ambil semua mapping role_permissions saat ini
        $rolePermissions = RolePermission::all()->groupBy('role')->map(function ($items) {
            return $items->where('is_granted', true)->pluck('permission_key')->toArray();
        });

        return view('admin.users.permissions', compact('rolesDefinition', 'groupedPermissions', 'permissionsByGroup', 'rolePermissions'));
    }

    /**
     * Simpan Pengaturan Matriks Hak Akses
     */
    public function updatePermissions(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $submitted = $request->input('permissions', []); // format: [role => [perm1 => 1, perm2 => 1]]
        $allPermissions = RolePermission::getAllPermissions();
        $rolesDefinition = RolePermission::getRolesDefinition();

        foreach ($rolesDefinition as $roleKey => $roleDef) {
            // Super Admin always has all granted
            if ($roleKey === 'super_admin') {
                continue;
            }

            foreach ($allPermissions as $group => $perms) {
                foreach ($perms as $permKey => $permLabel) {
                    $isGranted = isset($submitted[$roleKey][$permKey]);

                    RolePermission::updateOrCreate(
                        ['role' => $roleKey, 'permission_key' => $permKey],
                        ['is_granted' => $isGranted]
                    );
                }
            }
        }

        ActivityLog::record(
            'permissions',
            'Memperbarui konfigurasi matriks hak akses (role permissions)',
            auth()->id()
        );

        return back()->with('success', 'Matriks hak akses berhasil diperbarui!');
    }

    /**
     * Log Aktivitas Pengguna & Audit Trail (KAK: pencatatan aktivitas pengguna)
     */
    public function activityLogs(Request $request): View
    {
        $this->checkAdmin();

        $query = ActivityLog::with('user')->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('description', 'like', "%{$q}%")
                   ->orWhere('action', 'like', "%{$q}%")
                   ->orWhere('ip_address', 'like', "%{$q}%")
                   ->orWhereHas('user', function ($uq) use ($q) {
                       $uq->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                   });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();
        $actions = ActivityLog::distinct()->pluck('action');
        $actionTypes = $actions;

        return view('admin.users.activity_logs', compact('logs', 'actions', 'actionTypes'));
    }

    /**
     * Download Template CSV untuk Migrasi Data ASN (Sederhana, Header Bahasa Indonesia, Tanpa NIK)
     */
    public function downloadTemplate(): StreamedResponse
    {
        $this->checkAdmin();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_migrasi_data_asn.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar Excel di Windows membukanya rapi tanpa karakter berantakan
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header Sederhana Bahasa Indonesia (Tanpa NIK sesuai arahan BKPSDM)
            fputcsv($handle, [
                'nip',
                'nama_lengkap',
                'email',
                'nama_opd',
                'bagian',
                'jabatan',
                'pangkat_golongan',
                'nomor_hp',
                'peran',
                'password'
            ]);

            // Baris Contoh 1: ASN Pelaksana / Pejabat Fungsional
            fputcsv($handle, [
                '198503152010011002',
                'Budi Santoso, S.Kom, M.Eng',
                'budi.santoso@kabupaten.go.id',
                'Dinas Komunikasi dan Informatika',
                'Bidang Aplikasi dan Informatika',
                'Pranata Komputer Ahli Muda',
                'Penata Tingkat I / III/d',
                '081234567890',
                'asn_learner',
                'Asn12345!'
            ]);

            // Baris Contoh 2: ASN di BKPSDM (Password dikosongkan -> otomatis diset Asn12345!)
            fputcsv($handle, [
                '199008202014022001',
                'Siti Rahmawati, S.AP',
                'siti.rahma@kabupaten.go.id',
                'Badan Kepegawaian dan Pengembangan SDM',
                'Bidang Pengembangan Kompetensi ASN',
                'Analis SDM Aparatur Ahli Pertama',
                'Penata Muda / III/a',
                '082198765432',
                'asn_learner',
                ''
            ]);

            // Baris Contoh 3: Pimpinan / Kepala / Sekretaris
            fputcsv($handle, [
                '197812052003121004',
                'Drs. Hendra Setiawan, M.Si',
                'hendra.setiawan@kabupaten.go.id',
                'Dinas Pendidikan dan Kebudayaan',
                'Sekretariat Dinas',
                'Sekretaris Dinas',
                'Pembina Tingkat I / IV/b',
                '081377889900',
                'pimpinan',
                ''
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import Massal Data ASN dari File CSV
     * Mendukung Upsert: Jika NIP sudah ada, otomatis memperbarui data mutasi (OPD, Bagian, Jabatan, Pangkat)
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ], [
            'csv_file.required' => 'Pilih file CSV yang akan diimpor.',
            'csv_file.mimes'    => 'File harus berformat CSV (.csv).',
            'csv_file.max'      => 'Ukuran file CSV maksimal 10MB.',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        
        // Deteksi delimiter (koma atau titik koma khas Excel Indonesia)
        $firstLine = fgets(fopen($path, 'r'));
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->with('error', 'Gagal membaca file CSV yang diunggah.');
        }

        // Ambil header row
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (!$rawHeaders) {
            fclose($handle);
            return back()->with('error', 'Format file CSV tidak valid atau file kosong.');
        }

        // Normalisasi header (hapus BOM, lowercase, trim)
        $headers = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[\x{FEFF}]/u', '', $h)));
        }, $rawHeaders);

        // Buat mapping index kolom
        $colMap = [];
        foreach ($headers as $idx => $name) {
            if (in_array($name, ['nip', 'nomor_induk_pegawai'])) $colMap['nip'] = $idx;
            if (in_array($name, ['nama_lengkap', 'nama', 'name', 'nama_pegawai'])) $colMap['name'] = $idx;
            if (in_array($name, ['email', 'surel', 'email_resmi'])) $colMap['email'] = $idx;
            if (in_array($name, ['nama_opd', 'opd', 'instansi', 'perangkat_daerah', 'opd_name'])) $colMap['opd_name'] = $idx;
            if (in_array($name, ['bagian', 'bidang', 'subbag', 'seksi', 'unit_kerja'])) $colMap['bagian'] = $idx;
            if (in_array($name, ['jabatan', 'nama_jabatan', 'posisi'])) $colMap['jabatan'] = $idx;
            if (in_array($name, ['pangkat_golongan', 'golongan', 'pangkat', 'golongan_ruang'])) $colMap['pangkat_golongan'] = $idx;
            if (in_array($name, ['nomor_hp', 'no_hp', 'telepon', 'phone', 'hp', 'wa'])) $colMap['phone'] = $idx;
            if (in_array($name, ['peran', 'role', 'hak_akses'])) $colMap['role'] = $idx;
            if (in_array($name, ['password', 'kata_sandi', 'pass'])) $colMap['password'] = $idx;
        }

        if (!isset($colMap['nip']) && !isset($colMap['email'])) {
            fclose($handle);
            return back()->with('error', 'File CSV harus memiliki minimal kolom "nip" atau "email".');
        }

        $encService = app(EncryptionService::class);
        $created = 0;
        $updated = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Abaikan baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            $nipRaw = isset($colMap['nip']) && isset($row[$colMap['nip']]) ? trim($row[$colMap['nip']]) : null;
            $nipClean = $nipRaw ? preg_replace('/[^0-9]/', '', $nipRaw) : null;
            $email = isset($colMap['email']) && isset($row[$colMap['email']]) ? strtolower(trim($row[$colMap['email']])) : null;

            // Jika email kosong tapi NIP ada, generate email default
            if (empty($email) && !empty($nipClean)) {
                $email = "{$nipClean}@lms.local";
            }

            if (empty($email) && empty($nipClean)) {
                $skipped++;
                continue;
            }

            $name = isset($colMap['name']) && isset($row[$colMap['name']]) ? strip_tags(trim($row[$colMap['name']])) : ($email ?: 'ASN');
            $opdName = isset($colMap['opd_name']) && isset($row[$colMap['opd_name']]) ? trim($row[$colMap['opd_name']]) : null;
            $bagian = isset($colMap['bagian']) && isset($row[$colMap['bagian']]) ? trim($row[$colMap['bagian']]) : null;
            $jabatan = isset($colMap['jabatan']) && isset($row[$colMap['jabatan']]) ? trim($row[$colMap['jabatan']]) : null;
            $pangkatGol = isset($colMap['pangkat_golongan']) && isset($row[$colMap['pangkat_golongan']]) ? trim($row[$colMap['pangkat_golongan']]) : null;
            $phone = isset($colMap['phone']) && isset($row[$colMap['phone']]) ? trim($row[$colMap['phone']]) : null;
            $rawRole = isset($colMap['role']) && isset($row[$colMap['role']]) ? strtolower(trim($row[$colMap['role']])) : 'asn_learner';
            $passwordRaw = isset($colMap['password']) && isset($row[$colMap['password']]) && !empty(trim($row[$colMap['password']])) ? trim($row[$colMap['password']]) : 'Asn12345!';

            // Normalisasi role
            $role = match ($rawRole) {
                'admin', 'superadmin', 'bkpsdm' => 'super_admin',
                'admin_opd', 'opd'              => 'admin_opd',
                'pimpinan', 'kepala'            => 'pimpinan',
                'narasumber', 'trainer', 'guru' => 'narasumber',
                'reviewer', 'reviewer_talent'   => 'reviewer_talent',
                default                         => 'asn_learner',
            };

            // Cari user yang sudah ada berdasarkan Blind Index NIP atau Email
            $existingUser = null;
            if (!empty($nipClean)) {
                $nipHash = $encService->hashNip($nipClean);
                $existingUser = User::where('nip_hash', $nipHash)->first();
            }
            if (!$existingUser && !empty($email)) {
                $existingUser = User::where('email', $email)->first();
            }

            if ($existingUser) {
                // UPDATE / SINKRONISASI (ASN Mutasi atau pindah jabatan/OPD/bagian)
                $existingUser->update([
                    'name'             => $name ?: $existingUser->name,
                    'opd_name'         => $opdName ?: $existingUser->opd_name,
                    'bagian'           => $bagian ?: $existingUser->bagian,
                    'jabatan'          => $jabatan ?: $existingUser->jabatan,
                    'pangkat_golongan' => $pangkatGol ?: $existingUser->pangkat_golongan,
                    'phone'            => $phone ?: $existingUser->phone,
                    'headline'         => $jabatan ? "{$jabatan} di " . ($opdName ?: $existingUser->opd_name) : $existingUser->headline,
                ]);
                $updated++;
            } else {
                // BUAT ASN BARU
                User::create([
                    'name'             => $name,
                    'email'            => $email,
                    'password'         => Hash::make($passwordRaw),
                    'role'             => $role,
                    'nip'              => $nipClean,
                    'opd_name'         => $opdName,
                    'bagian'           => $bagian,
                    'jabatan'          => $jabatan,
                    'pangkat_golongan' => $pangkatGol,
                    'phone'            => $phone,
                    'headline'         => $jabatan ? "{$jabatan} di {$opdName}" : null,
                    'is_active'        => true,
                ]);
                $created++;
            }
        }

        fclose($handle);

        ActivityLog::record(
            'import_users',
            "Mengimpor data ASN dari CSV: {$created} akun baru, {$updated} data diperbarui (mutasi/jabatan)",
            auth()->id(),
            ['created' => $created, 'updated' => $updated, 'skipped' => $skipped]
        );

        $msg = "✅ Migrasi Data ASN Berhasil! {$created} ASN baru terdaftar, {$updated} ASN diperbarui data kepegawaiannya (OPD/Bagian/Jabatan).";
        if ($skipped > 0) {
            $msg .= " ({$skipped} baris dilewati karena format tidak valid).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Halaman Verifikasi Pengajuan Perubahan Data Mandiri oleh ASN (BKPSDM)
     */
    public function verifications(Request $request): View
    {
        $this->checkAdmin();

        $query = ProfileChangeRequest::with(['user', 'reviewer'])->latest();

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        } else {
            // Default tampilkan yang pending
            $query->where('status', 'pending');
        }

        $verifications = $query->paginate(15)->withQueryString();
        $counts = [
            'pending'  => ProfileChangeRequest::where('status', 'pending')->count(),
            'approved' => ProfileChangeRequest::where('status', 'approved')->count(),
            'rejected' => ProfileChangeRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.users.verifications', compact('verifications', 'counts'));
    }

    /**
     * Setujui Perubahan Data Kepegawaian ASN
     */
    public function approveVerification(ProfileChangeRequest $changeRequest): RedirectResponse
    {
        $this->checkAdmin();

        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah pernah diproses sebelumnya.');
        }

        $user = $changeRequest->user;
        if (!$user) {
            return back()->with('error', 'Akun pengguna tidak ditemukan.');
        }

        $proposed = $changeRequest->proposed_changes ?? [];

        // Terapkan perubahan data resmi kepegawaian
        $updateData = [];
        if (isset($proposed['name'])) $updateData['name'] = strip_tags(trim($proposed['name']));
        if (isset($proposed['opd_name'])) $updateData['opd_name'] = $proposed['opd_name'];
        if (isset($proposed['bagian'])) $updateData['bagian'] = $proposed['bagian'];
        if (isset($proposed['jabatan'])) $updateData['jabatan'] = $proposed['jabatan'];
        if (isset($proposed['pangkat_golongan'])) $updateData['pangkat_golongan'] = $proposed['pangkat_golongan'];
        if (isset($proposed['nip']) && !empty($proposed['nip'])) {
            $updateData['nip'] = preg_replace('/[^0-9]/', '', $proposed['nip']);
        }

        if (!empty($updateData)) {
            $updateData['headline'] = ($updateData['jabatan'] ?? $user->jabatan) ? 
                (($updateData['jabatan'] ?? $user->jabatan) . ' di ' . ($updateData['opd_name'] ?? $user->opd_name)) : $user->headline;
            $user->update($updateData);
        }

        $changeRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        ActivityLog::record(
            'approve_profile_change',
            "Menyetujui perubahan data kepegawaian untuk ASN [{$user->name}]",
            auth()->id(),
            ['user_id' => $user->id, 'changes' => $proposed]
        );

        return back()->with('success', "Permohonan perubahan data untuk [{$user->name}] berhasil DISETUJUI dan data resmi telah diperbarui!");
    }

    /**
     * Tolak Perubahan Data Kepegawaian ASN
     */
    public function rejectVerification(Request $request, ProfileChangeRequest $changeRequest): RedirectResponse
    {
        $this->checkAdmin();

        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah pernah diproses sebelumnya.');
        }

        $notes = $request->input('admin_notes') ?: 'Data yang diajukan tidak sesuai dengan arsip dokumen kepegawaian BKPSDM.';

        $changeRequest->update([
            'status'      => 'rejected',
            'admin_notes' => $notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        ActivityLog::record(
            'reject_profile_change',
            "Menolak perubahan data kepegawaian untuk ASN [{$changeRequest->user->name}]",
            auth()->id(),
            ['user_id' => $changeRequest->user_id, 'reason' => $notes]
        );

        return back()->with('success', "Permohonan perubahan data untuk [{$changeRequest->user->name}] telah DITOLAK.");
    }

    /**
     * Export data pengguna ke format CSV / Excel berdasarkan peran (role) atau seluruh pengguna
     */
    public function export(Request $request): StreamedResponse
    {
        $this->checkAdmin();

        $role = $request->query('role', 'all');
        $query = User::withCount(['enrollments', 'certificates', 'externalTrainings'])->latest();

        if ($role && $role !== 'all') {
            if ($role === 'super_admin') {
                $query->whereIn('role', ['admin', 'super_admin', 'bkpsdm']);
            } elseif ($role === 'asn_learner') {
                $query->whereIn('role', ['student', 'asn_learner']);
            } elseif ($role === 'narasumber') {
                $query->whereIn('role', ['instructor', 'narasumber']);
            } else {
                $query->where('role', $role);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('opd')) {
            $query->where('opd_name', $request->opd);
        }

        $roleSlug = Str::slug($role ?: 'semua');
        $timestamp = date('Ymd_His');
        $filename = "export_pengguna_{$roleSlug}_{$timestamp}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM untuk Excel

            fputcsv($handle, [
                'NIP',
                'Nama Lengkap',
                'Email Kedinasan',
                'Peran (Role)',
                'OPD / Perangkat Daerah',
                'Bagian / Bidang',
                'Jabatan',
                'Nomor Telepon',
                'Status Akun',
                'Total Kursus Diikuti',
                'Total Sertifikat Diperoleh',
                'Tanggal Terdaftar',
            ]);

            $query->chunk(250, function ($users) use ($handle) {
                foreach ($users as $u) {
                    fputcsv($handle, [
                        $u->nip ? "'".$u->nip : '-',
                        $u->name,
                        $u->email,
                        $u->role_name,
                        $u->opd_name ?: '-',
                        $u->bagian ?: '-',
                        $u->jabatan ?: '-',
                        $u->phone ? "'".$u->phone : '-',
                        $u->is_active ? 'Aktif' : 'Nonaktif',
                        $u->enrollments_count,
                        $u->certificates_count,
                        $u->created_at ? $u->created_at->format('Y-m-d H:i') : '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
