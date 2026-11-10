<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Webinar;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::main()->withCount('courses')->get();
        $format = $request->query('format'); // 'ecourse', 'webinar', 'in_class', 'hybrid'
        $type = $request->query('type');     // 'free', 'paid'
        $categorySlug = $request->query('category');
        $search = $request->query('q');

        // Hitung total dan count per kategori & format untuk sidebar
        $totalAllCourses = Course::where('is_published', true)->count();
        $totalAllWebinars = Webinar::count();
        $totalAllCount = $totalAllCourses + $totalAllWebinars;

        $categoryCounts = [];
        foreach ($categories as $cat) {
            $subIds = $cat->subCategories()->pluck('id');
            $cCount = Course::where('is_published', true)
                ->where(function ($q) use ($cat, $subIds) {
                    $q->where('category_id', $cat->id)
                      ->orWhereIn('sub_category_id', $subIds);
                })
                ->count();
            $categoryCounts[$cat->slug] = $cCount;
        }

        $formatCounts = [
            'ecourse' => Course::where('is_published', true)->whereIn('course_type', ['e-course', 'e-learning', 'online', 'free'])->count(),
            'webinar' => Webinar::where(fn($q) => $q->where('delivery_mode', 'online')->orWhereNull('delivery_mode'))->count(),
            'in_class' => Course::where('is_published', true)->whereIn('course_type', ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar'])->count() + Webinar::whereIn('delivery_mode', ['in_class', 'workshop', 'bimtek', 'seminar'])->count(),
            'hybrid' => Course::where('is_published', true)->where('course_type', 'hybrid')->count() + Webinar::where('delivery_mode', 'hybrid')->count(),
        ];

        // 1. Query Courses
        $courseQuery = Course::with(['category', 'instructor', 'lessons'])
            ->where('is_published', true);

        if ($search) {
            $courseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug) {
            $courseQuery->where(function ($q) use ($categorySlug) {
                $q->whereHas('category', fn($cq) => $cq->where('slug', $categorySlug))
                  ->orWhereHas('subCategory', function ($sq) use ($categorySlug) {
                      $sq->where('slug', $categorySlug)
                         ->orWhereHas('parent', fn($pq) => $pq->where('slug', $categorySlug));
                  });
            });
        }

        if ($type === 'free') {
            $courseQuery->where('price', 0);
        } elseif ($type === 'paid') {
            $courseQuery->where('price', '>', 0);
        }

        if ($format === 'ecourse') {
            $courseQuery->whereIn('course_type', ['e-course', 'e-learning', 'online', 'free']);
        } elseif ($format === 'webinar') {
            $courseQuery->whereIn('course_type', ['live-webinar', 'live_webinar']);
        } elseif ($format === 'in_class') {
            $courseQuery->whereIn('course_type', ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar']);
        } elseif ($format === 'hybrid') {
            $courseQuery->where('course_type', 'hybrid');
        }

        $courses = $courseQuery->latest()->get();

        // 2. Query Webinars (Live Webinar, In-Class sessions, Hybrid)
        $webinarItems = collect();
        if ($format !== 'ecourse') {
            $webinarQuery = Webinar::with(['course']);

            if ($search) {
                $webinarQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('speaker_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($type === 'free') {
                $webinarQuery->where('price', 0);
            } elseif ($type === 'paid') {
                $webinarQuery->where('price', '>', 0);
            }

            if ($format === 'webinar') {
                $webinarQuery->where('delivery_mode', 'online');
            } elseif ($format === 'in_class') {
                $webinarQuery->whereIn('delivery_mode', ['in_class', 'workshop', 'bimtek', 'seminar']);
            } elseif ($format === 'hybrid') {
                $webinarQuery->where('delivery_mode', 'hybrid');
            }

            $webinarItems = $webinarQuery->orderBy('event_date', 'desc')->get();
        }

        // Map unified catalog items
        $catalogItems = collect();

        foreach ($courses as $c) {
            $badgeType = match ($c->course_type) {
                'live-webinar', 'live_webinar' => 'webinar',
                'in-class', 'in_class', 'workshop', 'bimtek', 'seminar' => 'in_class',
                'hybrid' => 'hybrid',
                default => 'ecourse',
            };

            $badgeLabel = match ($badgeType) {
                'webinar' => 'Live Webinar',
                'in_class' => 'In-Class Tatap Muka',
                'hybrid' => 'Pelatihan Hybrid',
                default => 'E-Course Mandiri',
            };

            $catalogItems->push((object)[
                'id' => 'course_' . $c->id,
                'title' => $c->title,
                'slug' => $c->slug,
                'subtitle' => $c->subtitle ?: $c->description,
                'thumbnail' => $c->thumbnail,
                'category_name' => $c->category?->name ?? 'Pengembangan ASN',
                'category_slug' => $c->category?->slug ?? '',
                'instructor_name' => $c->instructor?->name ?? 'Instruktur Widyaiswara',
                'format' => $badgeType,
                'format_label' => $badgeLabel,
                'rating' => (float)$c->rating ?: 4.9,
                'jp' => (int)($c->jp ?: 2),
                'price' => (float)$c->price,
                'url' => route('courses.show', $c->slug),
                'is_webinar' => false,
                'created_at' => $c->created_at,
            ]);
        }

        foreach ($webinarItems as $w) {
            if ($courses->contains('slug', $w->slug)) {
                continue;
            }

            $wFormat = match ($w->delivery_mode) {
                'in_class', 'workshop', 'bimtek', 'seminar' => 'in_class',
                'hybrid' => 'hybrid',
                default => 'webinar',
            };

            $wFormatLabel = match ($wFormat) {
                'in_class' => 'In-Class Tatap Muka',
                'hybrid' => 'Pelatihan Hybrid',
                default => 'Live Webinar',
            };

            $catalogItems->push((object)[
                'id' => 'webinar_' . $w->id,
                'title' => $w->title,
                'slug' => $w->slug,
                'subtitle' => $w->description,
                'thumbnail' => $w->cover_image ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80',
                'category_name' => 'Kesehatan Digital & RME',
                'category_slug' => 'kesehatan-digital-rme',
                'instructor_name' => $w->speaker_name ?: 'Narasumber Ahli',
                'format' => $wFormat,
                'format_label' => $wFormatLabel,
                'rating' => 4.9,
                'jp' => (int)($w->jp ?: 4),
                'price' => (float)$w->price,
                'url' => route('webinars.show', $w->slug),
                'is_webinar' => true,
                'created_at' => $w->created_at,
            ]);
        }

        if ($categorySlug) {
            $cat = Category::where('slug', $categorySlug)->first();
            if ($cat) {
                $catalogItems = $catalogItems->filter(function ($item) use ($cat, $categorySlug) {
                    return $item->category_slug === $categorySlug || str_contains(strtolower($item->category_name), strtolower($cat->name));
                });
            }
        }

        // Manual pagination
        $page = (int) $request->get('page', 1);
        $perPage = 12;
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $catalogItems->forPage($page, $perPage)->values(),
            $catalogItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('courses.index', [
            'catalogItems' => $paginatedItems,
            'categories' => $categories,
            'format' => $format,
            'type' => $type,
            'categorySlug' => $categorySlug,
            'search' => $search,
            'totalAllCount' => $totalAllCount,
            'categoryCounts' => $categoryCounts,
            'formatCounts' => $formatCounts,
        ]);
    }

    public function show(string $slug): View
    {
        $course = Course::with(['category', 'instructor', 'lessons'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $isEnrolled = false;
        $progressPercent = 0;
        $enrollment = null;
        $certificate = null;

        if (Auth::check()) {
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();
            
            if ($enrollment) {
                $isEnrolled = true;
                $progressPercent = $enrollment->progress_percent;
                $certificate = \App\Models\Certificate::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->first();
            }
        }

        $relatedCourses = Course::where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->where('is_published', true)
            ->take(3)
            ->get();

        return view('courses.show', compact('course', 'isEnrolled', 'progressPercent', 'enrollment', 'certificate', 'relatedCourses'));
    }

    public function enroll(Request $request, string $slug): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan masuk atau daftar terlebih dahulu untuk mengikuti pelatihan ini.');
        }

        $course = Course::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['progress_percent' => 0]
        );

        // Increment student count
        if ($enrollment->wasRecentlyCreated) {
            $course->increment('students_count');
        }

        // If it's a webinar or in-class/hybrid course, stay on the show page so they can see meeting link & attendance
        if ($course->requiresAttendance()) {
            return redirect()->route('courses.show', $course->slug)
                ->with('success', 'Pendaftaran sesi berhasil! Silakan ikuti kegiatan dan lakukan presensi kehadiran.');
        }

        $firstLesson = $course->lessons()->orderBy('order')->first();
        if ($firstLesson) {
            return redirect()->route('classroom.show', [$course->slug, $firstLesson->slug])
                ->with('success', 'Selamat belajar! Pendaftaran kursus berhasil.');
        }

        return redirect()->route('dashboard')->with('success', 'Pendaftaran kursus berhasil.');
    }

    /**
     * Record attendance for Live Webinar, In-Class, Workshop, Bimtek, or Hybrid sessions.
     * Automatically completes enrollment, issues certificate, and accumulates JP into dashboard.
     */
    public function recordAttendance(Request $request, string $slug): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk melakukan presensi.');
        }

        $course = Course::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // 1. Ensure user is enrolled
        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['progress_percent' => 0]
        );

        if ($enrollment->wasRecentlyCreated) {
            $course->increment('students_count');
        }

        // 2. Mark attendance & completion
        $enrollment->attended_at = now();
        $enrollment->progress_percent = 100;
        $enrollment->completed_at = now();
        $enrollment->attendance_notes = $request->input('attendance_notes');
        $enrollment->attendance_rating = (int) $request->input('attendance_rating', 5);
        $enrollment->save();

        // 3. Mark all lessons completed if any
        foreach ($course->lessons as $lesson) {
            \App\Models\LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['is_completed' => true, 'completed_at' => now()]
            );
        }

        // 4. Auto-generate Certificate if available
        $certificate = null;
        if ($course->certificate_available) {
            $certificate = \App\Models\Certificate::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'certificate_number' => 'CERT-LMS-' . date('Y') . '-' . str_pad($course->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'issued_at' => now(),
                    'verification_hash' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12)),
                ]
            );
        }

        $jpEarned = $course->jp ?? 2;
        $msg = "Presensi kehadiran berhasil! Sertifikat resmi Anda telah diterbitkan dan {$jpEarned} JP otomatis ditambahkan ke Dashboard.";

        return back()->with('success', $msg)->with('attendance_completed', true);
    }
}
