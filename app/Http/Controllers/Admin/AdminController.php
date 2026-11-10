<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AppSetting;
use App\Models\Certificate;
use App\Models\ExternalTraining;
use App\Models\Slider;
use App\Models\Webinar;
use App\Models\Popup;
use App\Models\CourseEvaluation;
use App\Models\WebinarRegistration;
use App\Models\WebinarMaterial;
use App\Models\WebinarEvaluation;
use App\Models\WebinarLearningRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use App\Services\HtmlPurifierService;

class AdminController extends Controller
{
    private function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'bkpsdm', 'admin_opd', 'narasumber', 'instructor'])) {
            abort(403, 'Akses ditolak. Khusus Administrator dan Instruktur.');
        }
    }

    public function dashboard(): View|\Illuminate\Http\RedirectResponse
    {
        $this->checkAdmin();

        if (auth()->user()->isAdminOpd()) {
            return redirect()->route('dashboard.admin_opd');
        }

        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_lessons' => Lesson::count(),
            'total_submissions' => AssignmentSubmission::count(),
            'pending_submissions' => AssignmentSubmission::where('status', 'submitted')->count(),
            'total_webinars' => Webinar::count(),
            'active_sliders' => Slider::where('is_active', true)->count(),
        ];

        $recentCourses = Course::with(['category', 'instructor'])->latest()->take(5)->get();
        $recentEnrollments = Enrollment::with(['user', 'course'])->latest()->take(5)->get();
        $pendingAssignments = AssignmentSubmission::with(['user', 'assignment.lesson.course'])
            ->where('status', 'submitted')
            ->latest()
            ->take(5)
            ->get();
        $upcomingWebinars = Webinar::where('event_date', '>=', now()->toDateString())->orderBy('event_date')->take(3)->get();

        return view('admin.dashboard', compact('stats', 'recentCourses', 'recentEnrollments', 'pendingAssignments', 'upcomingWebinars'));
    }

    // ==========================================
    // 1. COURSES & LESSONS
    // ==========================================
    public function courses(): View
    {
        $this->checkAdmin();
        $courses = Course::with(['category', 'instructor', 'lessons'])->withCount(['students'])->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function createCourse(): View
    {
        $this->checkAdmin();
        $mainCategories = Category::whereNull('parent_id')
            ->with(['subCategories' => fn($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();
        $categories = $mainCategories;
        $instructors = User::whereIn('role', ['instructor', 'admin'])->get();
        return view('admin.courses.create', compact('categories', 'mainCategories', 'instructors'));
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'sub_category' => 'nullable|string|max:150',
            'tags' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'level' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|url',
            'learning_objectives' => 'nullable|string',
            'learning_method' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule_time' => 'nullable|string|max:10',
            'schedule_location' => 'nullable|string',
            'registration_deadline' => 'nullable|date',
            'target_audience_description' => 'nullable|string',
        ]);

        $slug = Str::slug($request->title);
        $count = Course::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $subCategoryName = $request->sub_category;
        if ($request->filled('sub_category_id')) {
            $selectedSub = Category::find($request->sub_category_id);
            if ($selectedSub) {
                $subCategoryName = $selectedSub->name;
            }
        }

        $normalizedTags = $request->filled('tags')
            ? collect(preg_split('/[,;\n]+/', $request->tags))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique()
                ->implode(', ')
            : null;

        $course = Course::create([
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'sub_category' => $subCategoryName,
            'tags' => $normalizedTags,
            'instructor_id' => $request->instructor_id,
            'title' => $request->title,
            'slug' => $slug,
            'subtitle' => $request->subtitle,
            'description' => $request->description ? app(HtmlPurifierService::class)->purify($request->description) : null,
            'thumbnail' => $request->thumbnail ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80',
            'level' => $request->level,
            'price' => $request->price,
            'duration_hours' => $request->duration_hours,
            'certificate_available' => $request->boolean('certificate_available', true),
            'is_featured' => $request->boolean('is_featured', false),
            'is_published' => true,
            'cert_title' => 'SERTIFIKAT KOMPETENSI RESMI',
            'cert_institution_name' => 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA',
            'cert_institution_sub' => 'Pemerintah Kabupaten Berau',
            'cert_theme_color' => 'teal',
            'approval_status' => 'approved',
            'course_type' => $request->course_type ?? 'e-course',
            'learning_method' => $request->learning_method,
            'target_audience_type' => $request->target_audience_type ?? 'umum',
            'target_audience_detail' => $request->target_audience_detail,
            'target_audience_description' => $request->target_audience_description,
            'jp' => $request->jp ?? 2,
            'learning_objectives' => $request->learning_objectives,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'schedule_time' => $request->schedule_time,
            'schedule_location' => $request->schedule_location,
            'registration_deadline' => $request->registration_deadline,
        ]);

        return redirect()->route('admin.courses.edit', $course->id)
            ->with('success', 'Kursus berhasil dibuat! Anda dapat melengkapi informasi materi, silabus, dan sertifikat di halaman edit ini.');
    }

    public function editCourse(Course $course): View
    {
        $this->checkAdmin();
        $course->load(['category', 'subCategory', 'instructor', 'lessons.quiz', 'lessons.assignment']);
        $mainCategories = Category::whereNull('parent_id')
            ->with(['subCategories' => fn($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();
        $categories = $mainCategories;
        $instructors = User::whereIn('role', ['instructor', 'admin'])->get();
        return view('admin.courses.edit', compact('course', 'categories', 'mainCategories', 'instructors'));
    }

    public function updateCourse(Request $request, Course $course): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'sub_category' => 'nullable|string|max:150',
            'tags' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'level' => 'required|string',
            'course_type' => 'required|in:e-course,live-webinar,live_webinar,in-class,in_class,bimtek,workshop,seminar,hybrid',
            'target_audience_type' => 'required|in:umum,asn-opd,jabatan-tertentu',
            'target_audience_detail' => 'nullable|string|max:255',
            'target_audience_description' => 'nullable|string',
            'jp' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'learning_method' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule_time' => 'nullable|string|max:10',
            'schedule_location' => 'nullable|string',
            'registration_deadline' => 'nullable|date',
            'thumbnail' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Certificate fields
            'cert_title' => 'nullable|string|max:255',
            'cert_institution_name' => 'nullable|string|max:255',
            'cert_institution_sub' => 'nullable|string|max:255',
            'cert_signatory_name' => 'nullable|string|max:255',
            'cert_signatory_role' => 'nullable|string|max:255',
            'cert_signatory_nip' => 'nullable|string|max:255',
            'cert_theme_color' => 'nullable|string|max:20',
            'cert_statement' => 'nullable|string',
            'cert_template_mode' => 'nullable|in:default,custom_bg',
            'cert_bg_image' => 'nullable|string',
            'cert_bg_image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'cert_signature_image' => 'nullable|string',
            'cert_signature_file' => 'nullable|image|mimes:png,webp|max:4096',
            'cert_font_family' => 'nullable|string|max:50',
            'cert_font_size' => 'nullable|string|max:30',
            'cert_name_color' => 'nullable|string|max:30',
            'cert_name_vertical_pos' => 'nullable|integer|min:10|max:90',
            'cert_seal_text' => 'nullable|string|max:50',
        ]);

        $subCategoryName = $request->sub_category;
        if ($request->filled('sub_category_id')) {
            $selectedSub = Category::find($request->sub_category_id);
            if ($selectedSub) {
                $subCategoryName = $selectedSub->name;
            }
        }

        $normalizedTags = $request->filled('tags')
            ? collect(preg_split('/[,;\n]+/', $request->tags))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique()
                ->implode(', ')
            : null;

        $thumbnail = $course->thumbnail;
        if ($request->hasFile('thumbnail_file')) {
            $file = $request->file('thumbnail_file');
            $name = 'thumb_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/courses'), $name);
            $thumbnail = asset('uploads/courses/' . $name);
        } elseif ($request->filled('thumbnail')) {
            $thumbnail = $request->thumbnail;
        }

        $certBgImage = $course->cert_bg_image;
        if ($request->hasFile('cert_bg_image_file')) {
            $file = $request->file('cert_bg_image_file');
            $name = 'cert_bg_' . $course->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/certificates'), $name);
            $certBgImage = asset('uploads/certificates/' . $name);
        } elseif ($request->filled('cert_bg_image')) {
            $certBgImage = $request->cert_bg_image;
        }

        $certSignature = $course->cert_signature_image;
        if ($request->hasFile('cert_signature_file')) {
            $file = $request->file('cert_signature_file');
            $name = 'sig_' . $course->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/certificates/signatures'), $name);
            $certSignature = asset('uploads/certificates/signatures/' . $name);
        } elseif ($request->filled('cert_signature_image')) {
            $certSignature = $request->cert_signature_image;
        }

        $certLogo = $course->cert_logo_url;
        if ($request->has('remove_cert_logo') && $request->boolean('remove_cert_logo')) {
            $certLogo = null;
        } elseif ($request->hasFile('cert_logo_file')) {
            $file = $request->file('cert_logo_file');
            $name = 'logo_' . $course->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/certificates/logos'), $name);
            $certLogo = asset('uploads/certificates/logos/' . $name);
        } elseif ($request->filled('cert_logo_url')) {
            $certLogo = $request->cert_logo_url;
        }

        $course->update([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'sub_category' => $subCategoryName,
            'tags' => $normalizedTags,
            'instructor_id' => $request->instructor_id,
            'level' => $request->level,
            'course_type' => $request->course_type,
            'learning_method' => $request->learning_method,
            'target_audience_type' => $request->target_audience_type,
            'target_audience_detail' => $request->target_audience_detail,
            'target_audience_description' => $request->target_audience_description,
            'jp' => $request->jp ?? 2,
            'price' => $request->price,
            'duration_hours' => $request->duration_hours,
            'subtitle' => $request->subtitle,
            'description' => $request->description ? app(HtmlPurifierService::class)->purify($request->description) : null,

            'learning_objectives' => $request->learning_objectives,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'schedule_time' => $request->schedule_time,
            'schedule_location' => $request->schedule_location,
            'registration_deadline' => $request->registration_deadline,
            'thumbnail' => $thumbnail,
            'is_published' => $request->boolean('is_published', true),
            'is_featured' => $request->boolean('is_featured', false),
            'approval_status' => $request->approval_status ?? $course->approval_status ?? 'approved',
            // Certificate settings
            'certificate_available' => $request->boolean('certificate_available', true),
            'cert_title' => $request->cert_title ?: $course->cert_title,
            'cert_institution_name' => $request->cert_institution_name ?: $course->cert_institution_name,
            'cert_institution_sub' => $request->cert_institution_sub ?: $course->cert_institution_sub,
            'cert_signatory_name' => $request->cert_signatory_name ?: $course->cert_signatory_name,
            'cert_signatory_role' => $request->cert_signatory_role ?: $course->cert_signatory_role,
            'cert_signatory_nip' => $request->cert_signatory_nip ?: $course->cert_signatory_nip,
            'cert_logo_url' => $certLogo,
            'cert_theme_color' => $request->cert_theme_color ?: $course->cert_theme_color ?: 'teal',
            'cert_statement' => $request->cert_statement ?: $course->cert_statement,
            'cert_template_mode' => $request->cert_template_mode ?: ($course->cert_template_mode ?? 'default'),
            'cert_bg_image' => $certBgImage,
            'cert_signature_image' => $certSignature,
            'cert_font_family' => $request->cert_font_family ?: ($course->cert_font_family ?? 'playfair'),
            'cert_font_size' => $request->cert_font_size ?: ($course->cert_font_size ?? 'large'),
            'cert_name_color' => $request->cert_name_color ?: ($course->cert_name_color ?? '#0f172a'),
            'cert_name_vertical_pos' => $request->filled('cert_name_vertical_pos') ? (int)$request->cert_name_vertical_pos : ($course->cert_name_vertical_pos ?? 46),
            'cert_show_qr' => $request->has('cert_show_qr') ? $request->boolean('cert_show_qr') : true,
            'cert_show_number' => $request->has('cert_show_number') ? $request->boolean('cert_show_number') : true,
            'cert_show_date' => $request->has('cert_show_date') ? $request->boolean('cert_show_date') : true,
            'cert_show_seal' => $request->has('cert_show_seal') ? $request->boolean('cert_show_seal') : true,
            'cert_show_jp' => $request->has('cert_show_jp') ? $request->boolean('cert_show_jp') : true,
            'cert_seal_text' => $request->cert_seal_text ?: ($course->cert_seal_text ?? 'LMS BERAU VALID'),
            'cert_show_header' => $request->has('cert_show_header') ? $request->boolean('cert_show_header') : ($course->cert_show_header ?? true),
            'cert_elements_layout' => $request->filled('cert_elements_layout') ? (is_array($request->cert_elements_layout) ? $request->cert_elements_layout : json_decode($request->cert_elements_layout, true)) : $course->cert_elements_layout,
        ]);

        return redirect()->back()
            ->with('success', 'Seluruh informasi materi kursus, silabus, dan sertifikat berhasil diperbarui!');
    }

    public function approveCourse(Course $course): RedirectResponse
    {
        $this->checkAdmin();
        $course->update([
            'approval_status' => 'approved',
            'is_published' => true,
        ]);

        return redirect()->back()
            ->with('success', "Kursus '{$course->title}' telah berhasil disetujui dan kini tayang di halaman publik LMS!");
    }

    public function lessons(Course $course): View
    {
        $this->checkAdmin();
        $course->load(['category', 'lessons.quiz', 'lessons.assignment']);
        return view('admin.courses.lessons', compact('course'));
    }

    public function storeLesson(Request $request, Course $course): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'section_title' => 'required|string|max:255',
            'type' => 'required|in:video,pdf,link,pre_test,post_test,assignment,reading',
            'duration_minutes' => 'required|integer|min:1',
            'video_url' => 'nullable|url',
            'pdf_url' => 'nullable|url',
            'external_url' => 'nullable|url',
            'content' => 'nullable|string',
        ]);

        $slug = Str::slug($request->title);
        $order = $course->lessons()->max('order') + 1;

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_title' => $request->section_title,
            'title' => $request->title,
            'slug' => $slug . '-' . $order,
            'order' => $order,
            'type' => $request->type,
            'video_url' => $request->video_url,
            'pdf_url' => $request->pdf_url,
            'external_url' => $request->external_url,
            'content' => $request->content,
            'duration_minutes' => $request->duration_minutes,
            'is_free_preview' => $request->boolean('is_free_preview', false),
        ]);

        if (in_array($request->type, ['pre_test', 'post_test'])) {
            $quiz = Quiz::create([
                'lesson_id' => $lesson->id,
                'title' => $request->type === 'pre_test' ? 'Evaluasi Pemahaman Awal (Pre-Test)' : 'Uji Akhir Kelulusan (Post-Test)',
                'description' => 'Kerjakan seluruh soal pilihan ganda berikut untuk verifikasi kompetensi.',
                'passing_score' => 70,
                'duration_minutes' => $request->duration_minutes,
            ]);

            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => 'Pertanyaan 1: Apakah tujuan utama implementasi materi modul ini?',
                'options' => [
                    'A' => 'Meningkatkan efisiensi dan kepatuhan standar',
                    'B' => 'Menambah beban dokumentasi faskes',
                    'C' => 'Mengabaikan keamanan data pribadi',
                    'D' => 'Tidak ada dampak yang signifikan',
                ],
                'correct_answer' => 'A',
                'explanation' => 'Tujuan utama dari standarisasi adalah peningkatan efisiensi operasional dan kepatuhan sistem.',
                'order' => 1,
            ]);
        }

        if ($request->type === 'assignment') {
            Assignment::create([
                'lesson_id' => $lesson->id,
                'title' => 'Tugas Praktik: ' . $lesson->title,
                'instructions' => "Silakan pelajari studi kasus yang diberikan. Unduh berkas panduan jika tersedia, susun analisis atau kode penyelesaian, dan unggah hasil pekerjaan Anda melalui form di bawah.",
                'max_score' => 100,
            ]);
        }

        return back()->with('success', "Modul materi baru bertipe [{$request->type}] berhasil ditambahkan ke kurikulum!");
    }

    public function destroyLesson(Lesson $lesson): RedirectResponse
    {
        $this->checkAdmin();

        // Hapus kuis & soal kuis jika ada
        if ($lesson->quiz) {
            $lesson->quiz->questions()->delete();
            $lesson->quiz->delete();
        }

        // Hapus tugas & submission jika ada
        if ($lesson->assignment) {
            $lesson->assignment->submissions()->delete();
            $lesson->assignment->delete();
        }

        $lesson->delete();

        return back()->with('success', 'Modul materi pembelajaran berhasil dihapus dari kurikulum.');
    }

    // ==========================================
    // 2. CERTIFICATE BUILDER PER COURSE
    // ==========================================
    public function certificateBuilder(Course $course): View
    {
        $this->checkAdmin();
        $course->load('instructor');
        return view('admin.courses.certificate-builder', compact('course'));
    }

    public function updateCertificateBuilder(Request $request, Course $course): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'cert_title' => 'required|string|max:255',
            'cert_institution_name' => 'required|string|max:255',
            'cert_institution_sub' => 'nullable|string|max:255',
            'cert_signatory_name' => 'nullable|string|max:255',
            'cert_signatory_role' => 'nullable|string|max:255',
            'cert_signatory_nip' => 'nullable|string|max:255',
            'cert_signature_image' => 'nullable|string',
            'cert_signature_file' => 'nullable|image|mimes:png,webp|max:4096',
            'cert_logo_url' => 'nullable|url',
            'cert_theme_color' => 'required|in:teal,indigo,blue,slate,emerald',
            'cert_statement' => 'nullable|string',
            'certificate_available' => 'nullable|boolean',
            'cert_template_mode' => 'nullable|in:default,custom_bg',
            'cert_bg_image' => 'nullable|string',
            'cert_bg_image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'cert_font_family' => 'nullable|string|max:50',
            'cert_font_size' => 'nullable|string|max:30',
            'cert_name_color' => 'nullable|string|max:30',
            'cert_name_vertical_pos' => 'nullable|integer|min:10|max:90',
            'cert_seal_text' => 'nullable|string|max:50',
        ]);

        $certBgImage = $course->cert_bg_image;
        if ($request->hasFile('cert_bg_image_file')) {
            $file = $request->file('cert_bg_image_file');
            $name = 'cert_bg_' . $course->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/certificates'), $name);
            $certBgImage = asset('uploads/certificates/' . $name);
        } elseif ($request->filled('cert_bg_image')) {
            $certBgImage = $request->cert_bg_image;
        }

        $certSignature = $course->cert_signature_image;
        if ($request->hasFile('cert_signature_file')) {
            $file = $request->file('cert_signature_file');
            $name = 'sig_' . $course->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/certificates/signatures'), $name);
            $certSignature = asset('uploads/certificates/signatures/' . $name);
        } elseif ($request->filled('cert_signature_image')) {
            $certSignature = $request->cert_signature_image;
        }

        $certLogo = $course->cert_logo_url;
        if ($request->has('remove_cert_logo') && $request->boolean('remove_cert_logo')) {
            $certLogo = null;
        } elseif ($request->hasFile('cert_logo_file')) {
            $file = $request->file('cert_logo_file');
            $name = 'logo_' . $course->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/certificates/logos'), $name);
            $certLogo = asset('uploads/certificates/logos/' . $name);
        } elseif ($request->filled('cert_logo_url')) {
            $certLogo = $request->cert_logo_url;
        }

        $course->update([
            'cert_title' => $request->cert_title,
            'cert_institution_name' => $request->cert_institution_name,
            'cert_institution_sub' => $request->cert_institution_sub,
            'cert_signatory_name' => $request->cert_signatory_name,
            'cert_signatory_role' => $request->cert_signatory_role,
            'cert_signatory_nip' => $request->cert_signatory_nip,
            'cert_signature_image' => $certSignature,
            'cert_logo_url' => $certLogo,
            'cert_theme_color' => $request->cert_theme_color,
            'cert_statement' => $request->cert_statement,
            'certificate_available' => $request->boolean('certificate_available', true),
            'cert_template_mode' => $request->cert_template_mode ?: ($course->cert_template_mode ?? 'default'),
            'cert_bg_image' => $certBgImage,
            'cert_font_family' => $request->cert_font_family ?: ($course->cert_font_family ?? 'playfair'),
            'cert_font_size' => $request->cert_font_size ?: ($course->cert_font_size ?? 'large'),
            'cert_name_color' => $request->cert_name_color ?: ($course->cert_name_color ?? '#0f172a'),
            'cert_name_vertical_pos' => $request->filled('cert_name_vertical_pos') ? (int)$request->cert_name_vertical_pos : ($course->cert_name_vertical_pos ?? 46),
            'cert_show_qr' => $request->has('cert_show_qr') ? $request->boolean('cert_show_qr') : true,
            'cert_show_number' => $request->has('cert_show_number') ? $request->boolean('cert_show_number') : true,
            'cert_show_date' => $request->has('cert_show_date') ? $request->boolean('cert_show_date') : true,
            'cert_show_seal' => $request->has('cert_show_seal') ? $request->boolean('cert_show_seal') : true,
            'cert_show_jp' => $request->has('cert_show_jp') ? $request->boolean('cert_show_jp') : true,
            'cert_seal_text' => $request->cert_seal_text ?: ($course->cert_seal_text ?? 'LMS BERAU VALID'),
            'cert_show_header' => $request->has('cert_show_header') ? $request->boolean('cert_show_header') : ($course->cert_show_header ?? true),
            'cert_elements_layout' => $request->filled('cert_elements_layout') ? (is_array($request->cert_elements_layout) ? $request->cert_elements_layout : json_decode($request->cert_elements_layout, true)) : $course->cert_elements_layout,
        ]);

        return back()->with('success', 'Desain sertifikat kelulusan kursus berhasil diperbarui!');
    }

    // ==========================================
    // 3. STUDENT & USER MANAGEMENT
    // ==========================================
    public function users(Request $request): View
    {
        $this->checkAdmin();

        $query = User::withCount([
            'enrollments',
            'certificates',
            'externalTrainings',
            'externalTrainings as pending_external_trainings_count' => function ($q) {
                $q->where('status', 'pending');
            }
        ])->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $nipHash = app(EncryptionService::class)->hashNip($q);
            $query->where(function ($sq) use ($q, $nipHash) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('nip_hash', $nipHash)  // Blind index: exact match NIP
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('headline', 'like', "%{$q}%");
            });
        }


        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(12)->withQueryString();
        $totalStudents = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $pendingExternalTotal = \App\Models\ExternalTraining::where('status', 'pending')->count();

        return view('admin.users.index', compact('users', 'totalStudents', 'totalInstructors', 'totalAdmins', 'pendingExternalTotal'));
    }

    public function showUser(User $user): View
    {
        $this->checkAdmin();

        $appSetting = AppSetting::getSettings();
        $currentYear = (int) date('Y');

        // Enrollments with course relations
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

        // External trainings
        $externalTrainings = ExternalTraining::where('user_id', $user->id)
            ->orderByDesc('training_date')
            ->get();

        $pendingExternalCount = $externalTrainings->where('status', 'pending')->count();

        // Combined training history
        $trainingHistory = collect();

        foreach ($enrollments->where('progress_percent', 100) as $enrollment) {
            $trainingHistory->push([
                'type' => 'internal',
                'title' => $enrollment->course->title,
                'institution' => $appSetting->app_name ?? 'LMS KAB Berau',
                'date' => $enrollment->completed_at ?? $enrollment->updated_at,
                'jp' => $enrollment->course->jp ?? 0,
                'status' => 'completed',
                'category' => $enrollment->course->category->name ?? '-',
            ]);
        }

        foreach ($externalTrainings as $ext) {
            $trainingHistory->push([
                'type' => 'external',
                'title' => $ext->training_name,
                'institution' => $ext->institution_name,
                'date' => $ext->training_date,
                'jp' => $ext->jp_count,
                'status' => $ext->status,
                'category' => 'Eksternal',
                'id' => $ext->id,
            ]);
        }

        $trainingHistory = $trainingHistory->sortByDesc('date')->values();

        // Yearly stats (last 3 years)
        $yearlyStats = collect();
        for ($y = $currentYear - 2; $y <= $currentYear; $y++) {
            $yearlyStats->push($user->getJpByYear($y) + ['year' => $y]);
        }

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
            'trainingHistory',
            'yearlyStats'
        ));
    }

    public function updateUserStatus(Request $request, User $user): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'role' => 'required|in:student,instructor,admin',
            'phone' => 'nullable|string|max:30',
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $user->update([
            'role' => $request->role,
            'phone' => $request->phone,
            'headline' => $request->headline,
            'bio' => $request->bio,
        ]);

        return back()->with('success', "Profil dan peran akun [{$user->name}] berhasil diperbarui!");
    }

    // ==========================================
    // 4. HOMEPAGE SLIDER / BANNER
    // ==========================================
    public function sliders(): View
    {
        $this->checkAdmin();
        $sliders = Slider::with('course.category')->orderBy('order')->get();
        $courses = Course::with('category', 'instructor')
            ->where('is_published', true)
            ->orderBy('title')
            ->get();
        $appSetting = AppSetting::getSettings();

        return view('admin.sliders.index', compact('sliders', 'courses', 'appSetting'));
    }

    public function updateSliderGrid(Request $request): RedirectResponse
    {
        $this->checkAdmin();
        $request->validate([
            'featured_grid_layout' => 'required|in:2x4,2x3',
        ]);

        $setting = AppSetting::getSettings();
        $setting->update([
            'featured_grid_layout' => $request->featured_grid_layout,
        ]);

        return back()->with('success', "Format tata letak grid Program Unggulan berhasil diatur ke " . ($request->featured_grid_layout === '2x4' ? '2x4 (4 Kolom per Baris)' : '2x3 (3 Kolom per Baris)') . "!");
    }

    public function addCourseToSlider(Request $request): RedirectResponse
    {
        $this->checkAdmin();
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::with('category')->findOrFail($request->course_id);

        // Check if already in sliders
        $existing = Slider::where('course_id', $course->id)->first();
        if ($existing) {
            $existing->update(['is_active' => true]);
            return back()->with('success', "Kursus '{$course->title}' sudah ada di slider dan telah diaktifkan kembali.");
        }

        $badge = $course->category->name ?? 'KURSUS UNGGULAN';
        $thumb = $course->thumbnail;
        $imageUrl = $thumb 
            ? (str_starts_with($thumb, 'http') || str_starts_with($thumb, '/') ? $thumb : asset('storage/' . $thumb))
            : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80';

        $nextOrder = (Slider::max('order') ?? 0) + 1;

        Slider::create([
            'course_id' => $course->id,
            'title' => $course->title,
            'subtitle' => $course->subtitle ?: \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 130),
            'badge_text' => $badge,
            'image_url' => $imageUrl,
            'cta_text' => 'Pelajari Modul Ini',
            'cta_link' => route('courses.show', $course->slug),
            'order' => $nextOrder,
            'is_active' => true,
        ]);

        return back()->with('success', "Kursus '{$course->title}' berhasil ditambahkan ke banner slider beranda!");
    }

    public function toggleSlider(Slider $slider): RedirectResponse
    {
        $this->checkAdmin();
        $slider->update([
            'is_active' => !$slider->is_active,
        ]);

        $statusLabel = $slider->is_active ? 'diaktifkan dan tampil di beranda' : 'disembunyikan (unselected) dari beranda';
        return back()->with('success', "Banner '{$slider->title}' berhasil {$statusLabel}!");
    }

    public function storeSlider(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'badge_text' => 'nullable|string|max:100',
            'image_url' => 'nullable|url',
            'cta_text' => 'required|string|max:100',
            'cta_link' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        Slider::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'badge_text' => $request->badge_text,
            'image_url' => $request->image_url ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=80',
            'cta_text' => $request->cta_text,
            'cta_link' => $request->cta_link,
            'order' => $request->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Banner slider beranda baru berhasil ditambahkan!');
    }

    public function updateSlider(Request $request, Slider $slider): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'badge_text' => 'nullable|string|max:100',
            'image_url' => 'nullable|url',
            'cta_text' => 'required|string|max:100',
            'cta_link' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $slider->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'badge_text' => $request->badge_text,
            'image_url' => $request->image_url ?: $slider->image_url,
            'cta_text' => $request->cta_text,
            'cta_link' => $request->cta_link,
            'order' => $request->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Banner slider berhasil diperbarui!');
    }

    public function deleteSlider(Slider $slider): RedirectResponse
    {
        $this->checkAdmin();
        $slider->delete();
        return back()->with('success', 'Banner slider berhasil dihapus.');
    }

    public function unselectCourseFromSlider(Request $request): RedirectResponse
    {
        $this->checkAdmin();
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $deleted = Slider::where('course_id', $request->course_id)->delete();

        return back()->with('success', 'Kursus berhasil dilepas (unselected) dari banner slider beranda.');
    }

    // ==========================================
    // 5. WEBINARS & AGENDA
    // ==========================================
    public function webinars(): View
    {
        $this->checkAdmin();
        $webinars = Webinar::with(['course', 'registrations'])
            ->withCount(['registrations', 'materials', 'evaluations'])
            ->latest('event_date')->paginate(15);
        $courses = Course::with('category', 'instructor')->orderBy('title')->get();
        return view('admin.webinars.index', compact('webinars', 'courses'));
    }

    public function storeWebinar(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'course_id'        => 'nullable|exists:courses,id',
            'delivery_mode'    => 'required|in:online,in_class,workshop,bimtek,seminar,hybrid',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'speaker_name'     => 'required|string|max:255',
            'speaker_title'    => 'nullable|string|max:255',
            'speaker_institution' => 'nullable|string|max:255',
            'speaker_bio'      => 'nullable|string',
            'speaker_avatar'   => 'nullable|url',
            'event_date'       => 'required|date',
            'time_start'       => 'required|string|max:10',
            'time_end'         => 'required|string|max:10',
            'location_type'    => 'required|string|max:255',
            'webinar_url'      => 'nullable|url',
            'registration_link'=> 'nullable|url',
            'recording_url'    => 'nullable|url',
            'cover_image'      => 'nullable|url',
            'quota'            => 'required|integer|min:1',
            'jp'               => 'nullable|integer|min:1|max:100',
            'price'            => 'required|numeric|min:0',
            'status'           => 'required|in:upcoming,ongoing,completed',
            'is_featured'      => 'nullable|boolean',
            'cert_available'   => 'nullable|boolean',
            'cert_signatory_name' => 'nullable|string|max:255',
            'cert_signatory_role' => 'nullable|string|max:255',
            'cert_signatory_nip'  => 'nullable|string|max:50',
        ]);

        $slug = Str::slug($request->title);
        $count = Webinar::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        // If cover_image is not given and course_id is provided, use course thumbnail
        $coverImage = $request->cover_image;
        if (!$coverImage && $request->course_id) {
            $c = Course::find($request->course_id);
            if ($c && $c->thumbnail) {
                $coverImage = str_starts_with($c->thumbnail, 'http') || str_starts_with($c->thumbnail, '/') ? $c->thumbnail : asset('storage/' . $c->thumbnail);
            }
        }

        Webinar::create([
            'course_id'          => $request->course_id,
            'delivery_mode'      => $request->delivery_mode,
            'title'              => $request->title,
            'slug'               => $slug,
            'description'        => $request->description,
            'speaker_name'       => $request->speaker_name,
            'speaker_title'      => $request->speaker_title,
            'speaker_institution'=> $request->speaker_institution,
            'speaker_bio'        => $request->speaker_bio,
            'speaker_avatar'     => $request->speaker_avatar,
            'event_date'         => $request->event_date,
            'time_start'         => $request->time_start,
            'time_end'           => $request->time_end,
            'location_type'      => $request->location_type,
            'webinar_url'        => $request->webinar_url,
            'registration_link'  => $request->registration_link,
            'recording_url'      => $request->recording_url,
            'cover_image'        => $coverImage ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80',
            'quota'              => $request->quota,
            'jp'                 => $request->jp ?? 2,
            'price'              => $request->price,
            'status'             => $request->status,
            'is_featured'        => $request->boolean('is_featured', true),
            'cert_available'     => $request->boolean('cert_available', false),
            'cert_signatory_name'=> $request->cert_signatory_name,
            'cert_signatory_role'=> $request->cert_signatory_role,
            'cert_signatory_nip' => $request->cert_signatory_nip,
        ]);

        return back()->with('success', 'Agenda / Workshop baru berhasil dipublikasikan!');
    }

    public function updateWebinar(Request $request, Webinar $webinar): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'course_id'        => 'nullable|exists:courses,id',
            'delivery_mode'    => 'required|in:online,in_class,workshop,bimtek,seminar,hybrid',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'speaker_name'     => 'required|string|max:255',
            'speaker_title'    => 'nullable|string|max:255',
            'speaker_institution' => 'nullable|string|max:255',
            'speaker_bio'      => 'nullable|string',
            'speaker_avatar'   => 'nullable|url',
            'event_date'       => 'required|date',
            'time_start'       => 'required|string|max:10',
            'time_end'         => 'required|string|max:10',
            'location_type'    => 'required|string|max:255',
            'webinar_url'      => 'nullable|url',
            'registration_link'=> 'nullable|url',
            'recording_url'    => 'nullable|url',
            'cover_image'      => 'nullable|url',
            'quota'            => 'required|integer|min:1',
            'jp'               => 'nullable|integer|min:1|max:100',
            'price'            => 'required|numeric|min:0',
            'status'           => 'required|in:upcoming,ongoing,completed',
            'is_featured'      => 'nullable|boolean',
            'cert_available'   => 'nullable|boolean',
            'cert_signatory_name' => 'nullable|string|max:255',
            'cert_signatory_role' => 'nullable|string|max:255',
            'cert_signatory_nip'  => 'nullable|string|max:50',
        ]);

        $webinar->update([
            'course_id'          => $request->course_id,
            'delivery_mode'      => $request->delivery_mode,
            'title'              => $request->title,
            'description'        => $request->description,
            'speaker_name'       => $request->speaker_name,
            'speaker_title'      => $request->speaker_title,
            'speaker_institution'=> $request->speaker_institution,
            'speaker_bio'        => $request->speaker_bio,
            'speaker_avatar'     => $request->speaker_avatar,
            'event_date'         => $request->event_date,
            'time_start'         => $request->time_start,
            'time_end'           => $request->time_end,
            'location_type'      => $request->location_type,
            'webinar_url'        => $request->webinar_url,
            'registration_link'  => $request->registration_link,
            'recording_url'      => $request->recording_url,
            'cover_image'        => $request->cover_image ?: $webinar->cover_image,
            'quota'              => $request->quota,
            'jp'                 => $request->jp ?? $webinar->jp ?? 2,
            'price'              => $request->price,
            'status'             => $request->status,
            'is_featured'        => $request->boolean('is_featured', true),
            'cert_available'     => $request->boolean('cert_available', false),
            'cert_signatory_name'=> $request->cert_signatory_name,
            'cert_signatory_role'=> $request->cert_signatory_role,
            'cert_signatory_nip' => $request->cert_signatory_nip,
        ]);

        return back()->with('success', 'Agenda / Workshop berhasil diperbarui!');
    }

    public function deleteWebinar(Webinar $webinar): RedirectResponse
    {
        $this->checkAdmin();
        $webinar->delete();
        return back()->with('success', 'Agenda webinar berhasil dihapus.');
    }

    /** Halaman detail webinar: peserta, presensi, materi, evaluasi, learning record */
    public function webinarDetail(Webinar $webinar): View
    {
        $this->checkAdmin();
        $webinar->load([
            'registrations.user',
            'materials',
            'evaluations.user',
            'learningRecords.user',
        ]);
        return view('admin.webinars.detail', compact('webinar'));
    }

    /** Tandai presensi peserta secara manual */
    public function markAttendance(Request $request, Webinar $webinar): RedirectResponse
    {
        $this->checkAdmin();
        $request->validate(['user_id' => 'required|exists:users,id']);

        $reg = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', $request->user_id)
            ->firstOrFail();

        $reg->markAttended('manual');

        return back()->with('success', 'Presensi peserta berhasil dicatat.');
    }

    /** Tandai hadir massal (semua peserta terdaftar) */
    public function markAllAttendance(Webinar $webinar): RedirectResponse
    {
        $this->checkAdmin();
        $webinar->registrations()->whereIn('status', ['registered', 'confirmed'])->each(function ($reg) {
            $reg->markAttended('manual');
        });
        return back()->with('success', 'Semua peserta berhasil ditandai hadir.');
    }

    /** Simpan materi webinar */
    public function storeMaterial(Request $request, Webinar $webinar): RedirectResponse
    {
        $this->checkAdmin();
        $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:document,video,link,presentation',
            'file_url'     => 'nullable|url',
            'external_url' => 'nullable|url',
            'is_public'    => 'nullable|boolean',
        ]);

        $order = $webinar->materials()->max('order') + 1;
        $webinar->materials()->create([
            'title'        => $request->title,
            'type'         => $request->type,
            'file_url'     => $request->file_url,
            'external_url' => $request->external_url,
            'order'        => $order,
            'is_public'    => $request->boolean('is_public', false),
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    /** Hapus materi webinar */
    public function deleteMaterial(WebinarMaterial $material): RedirectResponse
    {
        $this->checkAdmin();
        $material->delete();
        return back()->with('success', 'Materi berhasil dihapus.');
    }

    // ==========================================
    // 6. MODAL POPUPS
    // ==========================================
    public function popups(): View
    {
        $this->checkAdmin();
        $popups = Popup::latest()->get();
        return view('admin.popups.index', compact('popups'));
    }

    public function storePopup(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'image_url' => 'nullable|url',
            'button_text' => 'required|string|max:100',
            'button_link' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // If newly created is active, optionally deactivate other popups
        if ($request->boolean('is_active', true)) {
            Popup::where('is_active', true)->update(['is_active' => false]);
        }

        Popup::create([
            'title' => $request->title,
            'badge_text' => $request->badge_text ?: 'PENGUMUMAN PENTING',
            'content' => $request->content,
            'image_url' => $request->image_url,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Popup pengumuman baru berhasil disimpan!');
    }

    public function updatePopup(Request $request, Popup $popup): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'image_url' => 'nullable|url',
            'button_text' => 'required|string|max:100',
            'button_link' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_active')) {
            Popup::where('id', '!=', $popup->id)->update(['is_active' => false]);
        }

        $popup->update([
            'title' => $request->title,
            'badge_text' => $request->badge_text,
            'content' => $request->content,
            'image_url' => $request->image_url,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return back()->with('success', 'Pengaturan popup berhasil diperbarui!');
    }

    public function togglePopup(Popup $popup): RedirectResponse
    {
        $this->checkAdmin();
        $newState = !$popup->is_active;

        if ($newState) {
            Popup::where('id', '!=', $popup->id)->update(['is_active' => false]);
        }

        $popup->update(['is_active' => $newState]);

        return back()->with('success', 'Status tayang popup berhasil diubah!');
    }

    // ==========================================
    // 7. ASSIGNMENT SUBMISSIONS
    // ==========================================
    public function submissions(): View
    {
        $this->checkAdmin();

        $submissions = AssignmentSubmission::with(['user', 'assignment.lesson.course'])
            ->latest('submitted_at')
            ->paginate(15);

        return view('admin.submissions.index', compact('submissions'));
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->isNarasumber() && !$user->hasPermission('grade_assignments'))) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menilai penugasan ini.');
        }

        $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => 'graded',
        ]);

        return back()->with('success', 'Nilai dan umpan balik tugas berhasil disimpan!');
    }

    // ─── External Training (JP Eksternal) Admin Methods ─────────────────────

    /**
     * List all external training submissions for admin review.
     */
    public function externalTrainings(Request $request): View
    {
        $this->checkAdmin();

        $query = \App\Models\ExternalTraining::with(['user', 'reviewer'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->get();
        $pendingCount = \App\Models\ExternalTraining::where('status', 'pending')->count();

        return view('admin.external-trainings.index', compact('submissions', 'pendingCount'));
    }

    /**
     * Approve an external training submission.
     */
    public function approveExternalTraining(\App\Models\ExternalTraining $externalTraining): RedirectResponse
    {
        $this->checkAdmin();

        $externalTraining->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', "JP Eksternal \"{$externalTraining->training_name}\" berhasil disetujui ({$externalTraining->jp_count} JP).");
    }

    /**
     * Reject an external training submission.
     */
    public function rejectExternalTraining(Request $request, \App\Models\ExternalTraining $externalTraining): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $externalTraining->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Submission \"{$externalTraining->training_name}\" ditolak.");
    }
}
