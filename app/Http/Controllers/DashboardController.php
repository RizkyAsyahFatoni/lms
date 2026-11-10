<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\ExternalTraining;
use App\Models\IdpPlan;
use App\Models\WebinarRegistration;
use App\Models\WebinarLearningRecord;
use App\Models\Webinar;
use App\Models\User;
use App\Models\OpdActivityRequest;
use App\Models\AssignmentSubmission;
use App\Models\QuestionBank;
use App\Services\JpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Dispatcher utama: Mengarahkan pengguna ke dashboard spesifik sesuai role.
     * Jika ada parameter ?view_as=learner, selalu tampilkan portal belajar pribadi ASN.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Mode pembelajar pribadi (bisa diakses oleh semua role ASN)
        if ($request->query('view_as') === 'learner' || $request->query('mode') === 'learner') {
            return $this->learnerDashboard();
        }

        // 1. Pimpinan (Sekda / Bupati / Kepala OPD) -> Dashboard Eksekutif Makro KorpU
        if ($user->isPimpinan() || ($user->hasPermission('view_dashboard_reports') && !$user->isSuperAdmin() && !$user->isAdminOpd())) {
            return $this->pimpinanDashboard($request);
        }

        // 2. Admin OPD -> Dashboard Pengelola Pelatihan Unit Kerja
        if ($user->isAdminOpd()) {
            return $this->adminOpdDashboard($request);
        }

        // 3. Narasumber / Instruktur -> Panel Pengajar & Evaluasi
        if ($user->isNarasumber()) {
            return $this->narasumberDashboard($request);
        }

        // 4. Reviewer Talenta / Admin Talent -> Dashboard Pemetaan Kompetensi & IDP
        if ($user->isReviewerTalent()) {
            return $this->reviewerTalentDashboard($request);
        }

        // 5. Super Admin / BKPSDM -> Redirect ke Management Console Admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // 6. Default ASN Learner -> Portal Pembelajaran ASN
        return $this->learnerDashboard();
    }

    /**
     * 1. DASHBOARD EKSEKUTIF PIMPINAN (Bupati / Sekda / Kepala BKPSDM)
     * High-level macro metrics, ranking kepatuhan OPD, serapan JP se-Kabupaten Berau.
     */
    public function pimpinanDashboard(Request $request): View
    {
        $user = Auth::user();
        $currentYear = (int) $request->input('year', date('Y'));
        $appSetting = AppSetting::getSettings();
        $jpService = app(JpService::class);

        // Rekapitulasi Makro seluruh OPD
        $opdReport = $jpService->getOpdJpReport(['year' => $currentYear]);
        $opdRows = $opdReport['rows'];

        // Total ASN aktif di Kabupaten
        $totalAsnKabupaten = User::whereIn('role', ['student', 'asn_learner', 'admin_opd', 'instructor', 'narasumber', 'pimpinan'])->count();
        if ($totalAsnKabupaten === 0) {
            $totalAsnKabupaten = $opdReport['grand_total_asn'];
        }

        // Agregasi metode pembelajaran se-Kabupaten
        $typeSums = [
            'elearning' => 0,
            'webinar'   => 0,
            'in_class'  => 0,
            'hybrid'    => 0,
            'external'  => 0,
        ];
        foreach ($opdRows as $row) {
            if (isset($row['type_sums'])) {
                $typeSums['elearning'] += $row['type_sums']['elearning'] ?? 0;
                $typeSums['webinar']   += $row['type_sums']['webinar'] ?? 0;
                $typeSums['in_class']  += $row['type_sums']['in_class'] ?? 0;
                $typeSums['hybrid']    += $row['type_sums']['hybrid'] ?? 0;
                $typeSums['external']  += $row['type_sums']['external'] ?? 0;
            }
        }

        // Top 5 Kursus terpopuler / paling banyak diselesaikan
        $topCourses = Course::with(['category', 'instructor'])
            ->withCount(['enrollments as completions_count' => function ($q) {
                $q->where('progress_percent', 100);
            }])
            ->orderByDesc('completions_count')
            ->take(5)
            ->get();

        // Top 5 ASN Berprestasi (Leaderboard JP tertinggi tahun berjalan)
        $asnReport = $jpService->getAsnJpReport(['year' => $currentYear]);
        $topLearners = $asnReport['rows']->sortByDesc('total_jp')->take(5);

        // Ringkasan kegiatan pelatihan terlaksana tahun ini
        $totalWebinarsYear = Webinar::whereYear('event_date', $currentYear)->count();
        $totalCoursesActive = Course::where('is_published', true)->count();
        $totalApprovedExt = ExternalTraining::where('status', 'approved')->where('year', $currentYear)->count();

        // Available years for filter
        $availableYears = [date('Y'), date('Y') - 1, date('Y') - 2];

        return view('dashboard.pimpinan', compact(
            'user',
            'currentYear',
            'appSetting',
            'opdReport',
            'opdRows',
            'totalAsnKabupaten',
            'typeSums',
            'topCourses',
            'topLearners',
            'totalWebinarsYear',
            'totalCoursesActive',
            'totalApprovedExt',
            'availableYears'
        ));
    }

    /**
     * 2. DASHBOARD ADMIN OPD (Pusat Kendali Pelatihan Unit Kerja)
     * Usulan kegiatan mandiri ke BKPSDM, kepatuhan 20 JP pegawai OPD, kalender OPD.
     */
    public function adminOpdDashboard(Request $request): View
    {
        $user = Auth::user();
        $currentYear = (int) $request->input('year', date('Y'));
        $appSetting = AppSetting::getSettings();
        $jpService = app(JpService::class);

        $opdName = $user->opd_name ?: 'Dinas Komunikasi dan Informatika';

        // Laporan capaian JP seluruh pegawai di OPD ini
        $opdAsnReport = $jpService->getAsnJpReport([
            'opd_name' => $opdName,
            'year'     => $currentYear,
        ]);

        // Daftar usulan kegiatan mandiri yang diajukan oleh OPD ini (KAK VII)
        $opdRequests = OpdActivityRequest::where('opd_name', $opdName)
            ->latest()
            ->get();

        $requestStats = [
            'total'    => $opdRequests->count(),
            'pending'  => $opdRequests->where('status', 'pending')->count(),
            'approved' => $opdRequests->where('status', 'approved')->count(),
            'revision' => $opdRequests->where('status', 'revision')->count(),
            'rejected' => $opdRequests->where('status', 'rejected')->count(),
            'draft'    => $opdRequests->where('status', 'draft')->count(),
        ];

        // Kegiatan mendatang yang relevan untuk OPD
        $upcomingEvents = Webinar::where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(3)
            ->get();

        return view('dashboard.admin_opd', compact(
            'user',
            'currentYear',
            'appSetting',
            'opdName',
            'opdAsnReport',
            'opdRequests',
            'requestStats',
            'upcomingEvents'
        ));
    }

    /**
     * 3. DASHBOARD NARASUMBER / INSTRUKTUR
     * Manajemen modul yang diampu, antrian penilaian tugas peserta, jadwal webinar.
     */
    public function narasumberDashboard(Request $request): View
    {
        $user = Auth::user();
        $appSetting = AppSetting::getSettings();

        // 1. Kursus yang diampu
        $assignedCourses = Course::with(['category', 'lessons'])
            ->withCount(['students', 'lessons'])
            ->where('instructor_id', $user->id)
            ->get();

        // 2. Webinar / Sesi Live yang difasilitasi
        $assignedWebinars = Webinar::where(function ($q) use ($user) {
            $q->where('speaker_name', 'like', "%{$user->name}%")
              ->orWhereIn('course_id', Course::where('instructor_id', $user->id)->pluck('id'));
        })
        ->orderByDesc('event_date')
        ->get();

        // 3. Antrian tugas peserta yang menunggu penilaian instruktur
        $courseIds = $assignedCourses->pluck('id')->toArray();
        $pendingSubmissions = AssignmentSubmission::with(['user', 'assignment.lesson.course'])
            ->where('status', 'submitted')
            ->whereHas('assignment.lesson.course', function ($q) use ($courseIds) {
                if (!empty($courseIds)) {
                    $q->whereIn('id', $courseIds);
                }
            })
            ->latest()
            ->take(15)
            ->get();

        // Total murid terdaftar di kelas pengajar
        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');

        // Bank soal yang dapat diakses narasumber
        $questionBanks = QuestionBank::withCount('questions')
            ->latest()
            ->take(4)
            ->get();

        return view('dashboard.narasumber', compact(
            'user',
            'appSetting',
            'assignedCourses',
            'assignedWebinars',
            'pendingSubmissions',
            'totalStudents',
            'questionBanks'
        ));
    }

    /**
     * 4. DASHBOARD REVIEWER TALENTA / ADMIN TALENT
     * Manajemen rencana pengembangan IDP, evaluasi gap kompetensi ASN.
     */
    public function reviewerTalentDashboard(Request $request): View
    {
        $user = Auth::user();
        $currentYear = (int) $request->input('year', date('Y'));
        $appSetting = AppSetting::getSettings();

        // Statistik IDP
        $totalIdp = IdpPlan::count();
        $totalAsnWithIdp = IdpPlan::distinct('nip')->count('nip');

        // Ambil data IDP terbaru
        $idpPlans = IdpPlan::latest()->paginate(15);

        // IDP per tahun
        $idpByYear = IdpPlan::selectRaw('tahun, count(*) as count')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->get();

        // Kursus yang sudah diterbitkan untuk matching
        $publishedCourses = Course::where('is_published', true)
            ->select('id', 'title', 'slug', 'jp', 'course_type')
            ->get();

        // Rekapitulasi per OPD dari ASN yang memiliki IDP
        $nips = IdpPlan::distinct()->pluck('nip')->toArray();
        $asns = User::whereIn('nip', $nips)->get();
        $opdIdpSummary = $asns->groupBy('opd_name')->map(function ($users, $opd) {
            $userNips = $users->pluck('nip')->toArray();
            $itemCount = IdpPlan::whereIn('nip', $userNips)->count();
            return [
                'opd_name'   => $opd ?: 'Belum Terdata',
                'asn_count'  => $users->count(),
                'item_count' => $itemCount,
            ];
        })->values();

        return view('dashboard.reviewer_talent', compact(
            'user',
            'currentYear',
            'appSetting',
            'totalIdp',
            'totalAsnWithIdp',
            'idpPlans',
            'idpByYear',
            'publishedCourses',
            'opdIdpSummary'
        ));
    }

    /**
     * 5. PORTAL PEMBELAJARAN ASN (Mode Pelajar Pribadi)
     * Menampilkan progres kursus, sertifikat, jadwal webinar, dan perolehan 20 JP pribadi.
     */
    public function learnerDashboard(): View
    {
        $user = Auth::user();
        $appSetting = AppSetting::getSettings();
        $currentYear = (int) date('Y');

        // Core enrollment data
        $enrollments = Enrollment::with(['course.category', 'course.instructor', 'course.lessons'])
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        $completedCount = $enrollments->where('progress_percent', 100)->count();
        $inProgressCount = $enrollments->where('progress_percent', '<', 100)->count();
        $totalLearningHours = $enrollments->sum(function ($e) {
            return ($e->progress_percent / 100) * ($e->course->duration_hours ?? 2);
        });

        // Certificates
        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // JP Tracker Data (current year)
        $jpData = $user->getJpByYear($currentYear);
        $jpYearLabel = $appSetting->getJpYearLabel();

        // External trainings (latest 10 for dashboard preview)
        $externalTrainings = ExternalTraining::where('user_id', $user->id)
            ->orderByDesc('training_date')
            ->limit(10)
            ->get();

        $pendingExternalCount = ExternalTraining::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Training Timeline (combine internal completed + external approved)
        $trainingHistory = collect();
        $certMap = $certificates->keyBy('course_id');

        // Add completed internal courses
        $completedEnrollments = $enrollments->where('progress_percent', 100);
        foreach ($completedEnrollments as $enrollment) {
            $trainingHistory->push([
                'type'               => 'internal',
                'title'              => $enrollment->course->title,
                'institution'        => $appSetting->app_name ?? 'LMS KAB Berau',
                'date'               => $enrollment->completed_at ?? $enrollment->updated_at,
                'jp'                 => $enrollment->course->jp ?? 0,
                'status'             => 'completed',
                'category'           => $enrollment->course->category->name ?? '-',
                'delivery_mode'      => $enrollment->course->course_type,
                'delivery_mode_label'=> $enrollment->course->course_type_label,
                'attended_at'        => $enrollment->attended_at,
                'slug'               => $enrollment->course->slug,
                'certificate_number' => $certMap->get($enrollment->course_id)?->certificate_number,
            ]);
        }

        // Add external trainings
        foreach ($externalTrainings as $ext) {
            $trainingHistory->push([
                'type'        => 'external',
                'title'       => $ext->training_name,
                'institution' => $ext->institution_name,
                'date'        => $ext->training_date,
                'jp'          => $ext->jp_count,
                'status'      => $ext->status,
                'category'    => 'Eksternal',
            ]);
        }

        // Webinars & Live Sessions (KAK Poin 8, 9, 13, 14)
        $userWebinars = WebinarRegistration::with(['webinar.course'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $webinarRecords = WebinarLearningRecord::with('webinar')
            ->where('user_id', $user->id)
            ->get();

        foreach ($webinarRecords->where('certificate_issued', true) as $wRec) {
            if ($wRec->webinar) {
                $trainingHistory->push([
                    'type'                => 'webinar',
                    'title'               => $wRec->webinar->title,
                    'institution'         => $wRec->webinar->speaker_institution ?? 'LMS Berau Webinar',
                    'date'                => $wRec->completed_at ?? $wRec->webinar->event_date,
                    'jp'                  => $wRec->jp_earned,
                    'status'              => 'completed',
                    'category'            => 'Webinar / In-Class',
                    'delivery_mode'       => $wRec->webinar->delivery_mode,
                    'delivery_mode_label' => ucfirst($wRec->webinar->delivery_mode),
                    'slug'                => $wRec->webinar->slug,
                    'certificate_number'  => $wRec->certificate_number,
                ]);
            }
        }

        // Sort by date descending
        $trainingHistory = $trainingHistory->sortByDesc('date')->values();

        // Yearly JP stats (last 3 years)
        $yearlyStats = collect();
        for ($y = $currentYear - 2; $y <= $currentYear; $y++) {
            $yearlyStats->push($user->getJpByYear($y) + ['year' => $y]);
        }

        // IDP Recommendations
        $idpRecommendations = collect();
        $publishedCourses = Course::with('category')
            ->where('is_published', true)
            ->get();

        $enrolledMap = $enrollments->keyBy('course_id');

        if ($user->nip) {
            $idpPlans = IdpPlan::forNip($user->nip)
                ->orderBy('tahun')
                ->orderBy('judul_materi')
                ->get();

            if ($idpPlans->isNotEmpty()) {
                $idpRecommendations = $idpPlans->groupBy('tahun')->map(function ($items, $tahun) use ($publishedCourses, $enrolledMap) {
                    return [
                        'tahun'     => $tahun,
                        'is_custom' => true,
                        'materi'    => $items->map(function ($plan) use ($publishedCourses, $enrolledMap) {
                            $matched  = null;
                            $keywords = collect(explode(' ', $plan->judul_materi))
                                ->filter(fn($w) => mb_strlen($w) > 3)
                                ->take(4);

                            foreach ($publishedCourses as $course) {
                                foreach ($keywords as $keyword) {
                                    if (mb_stripos($course->title, $keyword) !== false
                                        || ($course->sub_category && mb_stripos($course->sub_category, $keyword) !== false)
                                        || ($course->tags && mb_stripos($course->tags, $keyword) !== false)
                                        || ($course->category && mb_stripos($course->category->name, $keyword) !== false)) {
                                        $matched = $course;
                                        break 2;
                                    }
                                }
                            }

                            $enrollment = $matched ? $enrolledMap->get($matched->id) : null;

                            // Jalur eksternal jika materi belum ada di LMS internal
                            $externalRecommendation = null;
                            if (!$matched) {
                                $lowerJudul = strtolower($plan->judul_materi);
                                if (str_contains($lowerJudul, 'kepemimpinan') || str_contains($lowerJudul, 'pkp') || str_contains($lowerJudul, 'pka') || str_contains($lowerJudul, 'lan')) {
                                    $externalRecommendation = [
                                        'provider' => 'Lembaga Administrasi Negara (LAN RI) / BPSDMD',
                                        'type'     => 'Diklat Kepemimpinan & Manajerial ASN',
                                        'badge'    => 'Rekomendasi Jalur: LAN RI / BPSDMD',
                                        'icon'     => '🏛️',
                                    ];
                                } elseif (str_contains($lowerJudul, 'sertifikasi') || str_contains($lowerJudul, 'scrum') || str_contains($lowerJudul, 'agile') || str_contains($lowerJudul, 'cloud') || str_contains($lowerJudul, 'cyber')) {
                                    $externalRecommendation = [
                                        'provider' => 'Provider Swasta Terakreditasi / LSP BNSP',
                                        'type'     => 'Sertifikasi Keahlian Khusus / Global',
                                        'badge'    => 'Rekomendasi Jalur: Provider Swasta / LSP',
                                        'icon'     => '🏢',
                                    ];
                                } else {
                                    $externalRecommendation = [
                                        'provider' => 'Pusdiklat K/L / Mitra Eksternal',
                                        'type'     => 'Bimtek / Pelatihan Teknis Eksternal',
                                        'badge'    => 'Rekomendasi Jalur: Eksternal / Mitra',
                                        'icon'     => '🌐',
                                    ];
                                }
                            }

                            return [
                                'judul_materi'            => $plan->judul_materi,
                                'matched_course'          => $matched,
                                'enrollment'              => $enrollment,
                                'is_enrolled'             => $enrollment !== null,
                                'is_completed'            => $enrollment ? ($enrollment->progress_percent >= 100 || $enrollment->attended_at) : false,
                                'progress'                => $enrollment ? $enrollment->progress_percent : 0,
                                'gap_type'                => str_contains(strtolower($plan->judul_materi), 'manajemen') || str_contains(strtolower($plan->judul_materi), 'kepemimpinan') 
                                                            ? 'Kompetensi Manajerial' 
                                                            : (str_contains(strtolower($plan->judul_materi), 'kompetensi') ? 'Standar Kompetensi ASN' : 'Kompetensi Teknis'),
                                'external_recommendation' => $externalRecommendation,
                            ];
                        }),
                    ];
                })->values();
            }
        }

        // Jika user belum memiliki entri IDP khusus di DB, sediakan kurikulum rekomendasi standar kompetensi ASN (IDP Umum)
        if ($idpRecommendations->isEmpty()) {
            $unenrolledCourses = $publishedCourses->filter(fn($c) => !$enrolledMap->has($c->id))->take(4);
            if ($unenrolledCourses->isEmpty()) {
                $unenrolledCourses = $publishedCourses->take(4);
            }

            $idpRecommendations = collect([
                [
                    'tahun'     => $currentYear,
                    'is_custom' => false,
                    'materi'    => $unenrolledCourses->map(function ($c) use ($enrolledMap) {
                        $enrollment = $enrolledMap->get($c->id);
                        return [
                            'judul_materi'   => $c->title,
                            'matched_course' => $c,
                            'enrollment'     => $enrollment,
                            'is_enrolled'    => $enrollment !== null,
                            'is_completed'   => $enrollment ? ($enrollment->progress_percent >= 100 || $enrollment->attended_at) : false,
                            'progress'       => $enrollment ? $enrollment->progress_percent : 0,
                            'gap_type'       => $c->category->name ?? 'Standar Kompetensi ASN',
                        ];
                    }),
                ]
            ]);
        }

        return view('dashboard.index', compact(
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
            'trainingHistory',
            'yearlyStats',
            'idpRecommendations',
            'userWebinars',
            'webinarRecords'
        ));
    }
}
