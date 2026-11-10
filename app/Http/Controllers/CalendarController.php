<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * CalendarController — Kalender Pembelajaran Publik (KAK Pasal XVII)
 *
 * Menampilkan semua kegiatan pembelajaran dalam format kalender:
 * - E-Course, Live Webinar, In-Class, Hybrid, Webinar mandiri
 * - Filter visibilitas: publik / hanya login / hanya OPD tertentu
 * - Endpoint JSON untuk FullCalendar.io
 */
class CalendarController extends Controller
{
    /**
     * Halaman utama kalender pembelajaran
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Ambil daftar OPD untuk filter (jika login)
        $opdList = collect();
        if ($user) {
            $opdList = \App\Models\User::whereNotNull('opd_name')
                ->where('opd_name', '!=', '')
                ->distinct()
                ->pluck('opd_name')
                ->sort();
        }

        // Stats ringkas
        $now = now();
        $stats = [
            'upcoming_courses'  => Course::where('is_published', true)
                ->where('show_in_calendar', true)
                ->where(function ($q) use ($now) {
                    $q->where('start_date', '>=', $now->toDateString())
                      ->orWhereNull('start_date');
                })->count(),
            'upcoming_webinars' => Webinar::where('show_in_calendar', true)
                ->where('event_date', '>=', $now->toDateString())
                ->count(),
        ];

        return view('calendar.index', compact('opdList', 'stats'));
    }

    /**
     * API JSON: Ambil events untuk FullCalendar
     * GET /kalender/events?start=&end=&opd=&type=
     */
    public function events(Request $request): JsonResponse
    {
        $user = auth()->user();
        $start = $request->input('start');
        $end   = $request->input('end');
        $opdFilter  = $request->input('opd');
        $typeFilter = $request->input('type'); // course, webinar, all

        $events = collect();

        // ─── COURSES (E-Course, Live Webinar, In-Class, Hybrid) ───
        if (!$typeFilter || in_array($typeFilter, ['course', 'all'])) {
            $courseQuery = Course::where('is_published', true)
                ->where('show_in_calendar', true)
                ->whereNotNull('start_date');

            // Filter visibility
            $courseQuery->where(function ($q) use ($user, $opdFilter) {
                // public: semua bisa lihat (termasuk tamu)
                $q->where('calendar_visibility', 'public');

                // private: hanya user yang login
                if ($user) {
                    $q->orWhere('calendar_visibility', 'private');

                    // opd_only: login + OPD cocok
                    $q->orWhere(function ($sq) use ($user, $opdFilter) {
                        $sq->where('calendar_visibility', 'opd_only');
                        if ($user->opd_name) {
                            $sq->where(function ($inner) use ($user) {
                                $inner->whereNull('calendar_opd_filter')
                                    ->orWhere('calendar_opd_filter', '')
                                    ->orWhereRaw("FIND_IN_SET(?, REPLACE(calendar_opd_filter, ', ', ',')) > 0", [$user->opd_name]);
                            });
                        }
                    });
                }
            });

            // Filter OPD yang dipilih user di UI
            if ($opdFilter && $user) {
                $courseQuery->where(function ($q) use ($opdFilter) {
                    $q->where('submitter_opd', $opdFilter)
                      ->orWhere('calendar_opd_filter', 'like', "%{$opdFilter}%");
                });
            }

            if ($start) $courseQuery->where('start_date', '>=', $start);
            if ($end)   $courseQuery->where('start_date', '<=', $end);

            $courses = $courseQuery->with('category', 'instructor')->get();

            foreach ($courses as $course) {
                $color = $this->getCourseColor($course->course_type, $course->calendar_color);

                $events->push([
                    'id'          => 'course_' . $course->id,
                    'title'       => $course->title,
                    'start'       => $course->start_date->toDateString(),
                    'end'         => $course->end_date
                        ? $course->end_date->addDay()->toDateString()
                        : $course->start_date->addDay()->toDateString(),
                    'color'       => $color['bg'],
                    'textColor'   => $color['text'],
                    'borderColor' => $color['border'],
                    'url'         => route('courses.show', $course->slug),
                    'extendedProps' => [
                        'type'        => 'course',
                        'course_type' => $course->course_type_label,
                        'jp'          => $course->jp,
                        'location'    => $course->schedule_location,
                        'instructor'  => $course->instructor?->name,
                        'opd'         => $course->submitter_opd,
                        'quota'       => null,
                        'description' => \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 150),
                    ],
                ]);
            }
        }

        // ─── WEBINARS ───
        if (!$typeFilter || in_array($typeFilter, ['webinar', 'all'])) {
            $webinarQuery = Webinar::where('show_in_calendar', true)
                ->whereNotNull('event_date');

            // Filter visibility
            $webinarQuery->where(function ($q) use ($user, $opdFilter) {
                $q->where('calendar_visibility', 'public');
                if ($user) {
                    $q->orWhere('calendar_visibility', 'private');
                    $q->orWhere(function ($sq) use ($user) {
                        $sq->where('calendar_visibility', 'opd_only');
                        if ($user->opd_name) {
                            $sq->where(function ($inner) use ($user) {
                                $inner->whereNull('calendar_opd_filter')
                                    ->orWhere('calendar_opd_filter', '')
                                    ->orWhereRaw("FIND_IN_SET(?, REPLACE(calendar_opd_filter, ', ', ',')) > 0", [$user->opd_name]);
                            });
                        }
                    });
                }
            });

            if ($start) $webinarQuery->where('event_date', '>=', $start);
            if ($end)   $webinarQuery->where('event_date', '<=', $end);

            $webinars = $webinarQuery->get();

            foreach ($webinars as $webinar) {
                $remaining = $webinar->remaining_quota ?? null;
                $isFull = $webinar->quota && $remaining !== null && $remaining <= 0;

                $events->push([
                    'id'          => 'webinar_' . $webinar->id,
                    'title'       => ($isFull ? '🔴 ' : '🟢 ') . $webinar->title,
                    'start'       => $webinar->event_date->toDateString(),
                    'end'         => $webinar->event_date->addDay()->toDateString(),
                    'color'       => $isFull ? '#ef4444' : ($webinar->calendar_color ?? '#8b5cf6'),
                    'textColor'   => '#ffffff',
                    'borderColor' => $isFull ? '#dc2626' : '#7c3aed',
                    'url'         => route('webinars.show', $webinar->slug),
                    'extendedProps' => [
                        'type'         => 'webinar',
                        'course_type'  => 'Webinar Live',
                        'jp'           => $webinar->jp,
                        'location'     => $webinar->location_type ?? 'Online',
                        'time_start'   => $webinar->time_start,
                        'time_end'     => $webinar->time_end,
                        'quota'        => $webinar->quota,
                        'remaining'    => $remaining,
                        'is_full'      => $isFull,
                        'speaker'      => $webinar->speaker_name,
                        'description'  => \Illuminate\Support\Str::limit(strip_tags($webinar->description ?? ''), 150),
                    ],
                ]);
            }
        }

        return response()->json($events->values());
    }

    /**
     * Admin: Toggle visibilitas kalender untuk course atau webinar
     */
    public function toggleVisibility(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin() && !$user->isAdminOpd()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'type'       => 'required|in:course,webinar',
            'id'         => 'required|integer',
            'field'      => 'required|in:show_in_calendar,calendar_visibility,calendar_opd_filter,calendar_color',
            'value'      => 'nullable',
        ]);

        if ($request->type === 'course') {
            $model = Course::findOrFail($request->id);
        } else {
            $model = Webinar::findOrFail($request->id);
        }

        $model->update([$request->field => $request->value]);

        return response()->json(['success' => true, 'updated' => $request->field]);
    }

    /**
     * Mendapatkan warna event berdasarkan tipe kursus
     */
    private function getCourseColor(string $courseType, ?string $customColor): array
    {
        if ($customColor) {
            return ['bg' => $customColor, 'text' => '#ffffff', 'border' => $customColor];
        }

        return match($courseType) {
            'e-course'                  => ['bg' => '#0ea5e9', 'text' => '#ffffff', 'border' => '#0284c7'],
            'live-webinar', 'live_webinar' => ['bg' => '#8b5cf6', 'text' => '#ffffff', 'border' => '#7c3aed'],
            'in-class', 'in_class'      => ['bg' => '#f97316', 'text' => '#ffffff', 'border' => '#ea580c'],
            'bimtek'                    => ['bg' => '#ef4444', 'text' => '#ffffff', 'border' => '#dc2626'],
            'workshop'                  => ['bg' => '#f59e0b', 'text' => '#ffffff', 'border' => '#d97706'],
            'seminar'                   => ['bg' => '#10b981', 'text' => '#ffffff', 'border' => '#059669'],
            'hybrid'                    => ['bg' => '#ec4899', 'text' => '#ffffff', 'border' => '#db2777'],
            default                     => ['bg' => '#64748b', 'text' => '#ffffff', 'border' => '#475569'],
        };
    }
}
