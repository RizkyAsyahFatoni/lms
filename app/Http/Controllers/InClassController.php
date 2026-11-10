<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Webinar;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InClassController extends Controller
{
    /**
     * Halaman Publik Modul & Pelatihan In-Class (Tatap Muka & Klasikal)
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'upcoming');
        $search = $request->query('q');
        $categorySlug = $request->query('category');
        $level = $request->query('level');

        // Ambil data kategori dengan hitungan pelatihan in-class
        $categories = Category::withCount(['courses' => function ($q) {
            $q->where('is_published', true)
              ->whereIn('course_type', ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar']);
        }])->get();

        $totalInClassCourses = Course::where('is_published', true)
            ->whereIn('course_type', ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar'])
            ->count();
        $totalInClassWebinars = Webinar::whereIn('delivery_mode', ['in_class', 'workshop', 'bimtek', 'seminar'])->count();
        $totalAllCount = $totalInClassCourses + $totalInClassWebinars;

        // 1. Ambil Course dengan tipe in-class
        $courseQuery = Course::with(['category', 'instructor'])
            ->where('is_published', true)
            ->whereIn('course_type', ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar']);

        if ($search) {
            $courseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('schedule_location', 'like', "%{$search}%");
            });
        }

        if ($categorySlug) {
            $courseQuery->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($level) {
            $courseQuery->where('level', $level);
        }

        $courses = $courseQuery->latest()->get();

        // 2. Ambil Webinar / Sesi Tatap Muka
        $webinars = collect();
        if (!$level) {
            $webinarQuery = Webinar::with(['course'])->withCount('registrations')
                ->whereIn('delivery_mode', ['in_class', 'workshop', 'bimtek', 'seminar']);

            if ($status === 'upcoming') {
                $webinarQuery->whereIn('status', ['upcoming', 'ongoing']);
            } elseif ($status === 'completed') {
                $webinarQuery->where('status', 'completed');
            }

            if ($search) {
                $webinarQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('speaker_name', 'like', "%{$search}%")
                      ->orWhere('location_type', 'like', "%{$search}%");
                });
            }

            // Jika ada filter kategori, sesuaikan dengan webinar
            if ($categorySlug) {
                if (str_contains($categorySlug, 'kesehatan')) {
                    $webinarQuery->where(function ($q) {
                        $q->where('title', 'like', '%faskes%')
                          ->orWhere('title', 'like', '%kesehatan%')
                          ->orWhere('title', 'like', '%kesehatan%');
                    });
                } elseif (str_contains($categorySlug, 'asn') || str_contains($categorySlug, 'pemimpin')) {
                    $webinarQuery->where(function ($q) {
                        $q->where('title', 'like', '%keuangan%')
                          ->orWhere('title', 'like', '%sipd%')
                          ->orWhere('title', 'like', '%asn%');
                    });
                }
            }

            $webinars = $webinarQuery->orderBy('event_date', 'asc')->get();
        }

        $totalItemsCount = $courses->count() + $webinars->count();

        return view('in_class.index', compact(
            'courses',
            'webinars',
            'status',
            'categories',
            'categorySlug',
            'search',
            'level',
            'totalAllCount',
            'totalItemsCount'
        ));
    }
}
