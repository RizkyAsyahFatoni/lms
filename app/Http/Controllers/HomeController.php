<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Slider;
use App\Models\Webinar;
use App\Models\Popup;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $appSetting = AppSetting::getSettings();
        $featuredGridLayout = $appSetting->featured_grid_layout ?: '2x4';

        $categories = Category::main()
            ->withCount(['courses', 'subCategories'])
            ->get();
        
        $featuredCourses = Course::with(['category', 'instructor', 'lessons'])
            ->where('is_published', true)
            ->where('is_featured', true)
            ->take(4)
            ->get();

        $latestCourses = Course::with(['category', 'instructor', 'lessons'])
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        // Ambil jumlah peserta dari kolom students_count yang ter-update saat enrollment
        $totalStudents = Course::sum('students_count');
        $totalCourses = Course::where('is_published', true)->count();

        // 1. Homepage Sliders (Disesuaikan dengan pengaturan grid: 2x3 maks 6, 2x4 maks 8)
        $sliderLimit = $featuredGridLayout === '2x3' ? 6 : 8;
        $sliders = Slider::where('is_active', true)
            ->orderBy('order')
            ->take($sliderLimit)
            ->get();

        // 2. Incoming Webinars / Agenda
        $upcomingWebinars = Webinar::whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('event_date')
            ->take(6)
            ->get();
        if ($upcomingWebinars->isEmpty()) {
            $upcomingWebinars = Webinar::orderBy('event_date')->take(6)->get();
        }

        // 3. Active Promotional / Announcement Popup
        $activePopup = Popup::where('is_active', true)->latest()->first();

        return view('home', compact(
            'categories', 
            'featuredCourses', 
            'latestCourses', 
            'totalStudents', 
            'totalCourses',
            'sliders',
            'upcomingWebinars',
            'activePopup',
            'featuredGridLayout'
        ));
    }
}
