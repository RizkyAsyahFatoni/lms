<?php

namespace App\Http\Controllers;

use App\Models\Webinar;
use App\Models\Category;
use App\Models\WebinarRegistration;
use App\Models\WebinarMaterial;
use App\Models\WebinarEvaluation;
use App\Models\WebinarLearningRecord;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WebinarController extends Controller
{
    /**
     * Katalog Webinar & Kalender Pembelajaran (KAK Poin 1 & 2)
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'upcoming');
        $search = $request->query('q');
        $categorySlug = $request->query('category');
        $type = $request->query('type'); // 'free' or 'paid'

        $categories = Category::main()->get();

        // Hitung total semua webinar online
        $baseOnlineQuery = Webinar::where(function ($q) {
            $q->where('delivery_mode', 'online')->orWhereNull('delivery_mode');
        });
        $totalAllCount = (clone $baseOnlineQuery)->count();

        // Hitung jumlah per kategori
        $categoryCounts = [];
        foreach ($categories as $cat) {
            $subIds = $cat->subCategories()->pluck('id');
            $cQuery = (clone $baseOnlineQuery)->where(function ($q) use ($cat, $subIds) {
                $q->whereHas('course', function ($cq) use ($cat, $subIds) {
                    $cq->where('category_id', $cat->id)
                      ->orWhereIn('sub_category_id', $subIds);
                });
            });
            $categoryCounts[$cat->slug] = $cQuery->count();
        }

        $query = Webinar::query()->with(['course.category'])->withCount('registrations')
            ->where(function ($q) {
                $q->where('delivery_mode', 'online')
                  ->orWhereNull('delivery_mode');
            });

        // Filter status: Akan Datang & Sedang Live vs Riwayat Webinar
        if ($status === 'upcoming') {
            $query->whereIn('status', ['upcoming', 'ongoing']);
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('speaker_name', 'like', "%{$search}%")
                  ->orWhere('speaker_institution', 'like', "%{$search}%")
                  ->orWhere('location_type', 'like', "%{$search}%");
            });
        }

        // Filter Kategori
        if ($categorySlug) {
            $cat = Category::where('slug', $categorySlug)->first();
            if ($cat) {
                $subIds = $cat->subCategories()->pluck('id');
                $query->whereHas('course', function ($cq) use ($cat, $subIds) {
                    $cq->where('category_id', $cat->id)
                      ->orWhereIn('sub_category_id', $subIds);
                });
            }
        }

        // Filter Type (Akses)
        if ($type === 'free') {
            $query->where(function ($q) {
                $q->where('price', 0)->orWhereNull('price');
            });
        } elseif ($type === 'paid') {
            $query->where('price', '>', 0);
        }

        $webinars = $query->orderBy('event_date', 'asc')->paginate(12)->withQueryString();

        // Kalender Pembelajaran: Ambil semua agenda bulan berjalan & depan untuk kalender interaktif
        $calendarEvents = Webinar::select('id', 'title', 'slug', 'event_date', 'time_start', 'time_end', 'delivery_mode', 'location_type', 'status')
            ->where('event_date', '>=', now()->startOfMonth()->subMonth())
            ->where('event_date', '<=', now()->addMonths(3)->endOfMonth())
            ->orderBy('event_date')
            ->get()
            ->map(function ($ev) {
                return [
                    'id'            => $ev->id,
                    'title'         => $ev->title,
                    'date'          => $ev->event_date->format('Y-m-d'),
                    'time'          => $ev->time_start . ' - ' . $ev->time_end,
                    'delivery_mode' => $ev->delivery_mode,
                    'location'      => $ev->location_type,
                    'status'        => $ev->status,
                    'url'           => route('webinars.show', $ev->slug),
                ];
            });

        return view('webinars.index', compact('webinars', 'calendarEvents', 'status', 'categories', 'categoryCounts', 'totalAllCount', 'categorySlug', 'type', 'search'));
    }

    /**
     * Halaman Detail Webinar & Ruang Kegiatan (KAK Poin 3 s/d 14)
     */
    public function show(string $slug): View
    {
        $webinar = Webinar::with(['course.instructor', 'materials' => function ($q) {
            $q->orderBy('order');
        }])->where('slug', $slug)->firstOrFail();

        $user = Auth::user();
        $registration = null;
        $learningRecord = null;
        $evaluation = null;

        if ($user) {
            $registration = WebinarRegistration::where('webinar_id', $webinar->id)
                ->where('user_id', $user->id)
                ->first();

            $learningRecord = WebinarLearningRecord::where('webinar_id', $webinar->id)
                ->where('user_id', $user->id)
                ->first();

            $evaluation = WebinarEvaluation::where('webinar_id', $webinar->id)
                ->where('user_id', $user->id)
                ->first();
        }

        // Hitung rata-rata rating dari seluruh evaluasi peserta
        $evaluationsCount = $webinar->evaluations()->count();
        $avgRating = $evaluationsCount > 0 ? round($webinar->evaluations()->avg('rating_overall'), 1) : 0;

        return view('webinars.show', compact('webinar', 'registration', 'learningRecord', 'evaluation', 'avgRating', 'evaluationsCount'));
    }

    /**
     * Pendaftaran Peserta Webinar (KAK Poin 8 & 14)
     */
    public function register(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();
        $webinar = Webinar::where('slug', $slug)->firstOrFail();

        // Cek apakah sudah terdaftar
        $existing = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('info', 'Anda sudah terdaftar pada kegiatan pembelajaran ini.');
        }

        // Cek kuota
        if ($webinar->remaining_quota <= 0) {
            return back()->with('error', 'Mohon maaf, kuota peserta untuk kegiatan ini telah penuh.');
        }

        // Buat registrasi baru
        $reg = WebinarRegistration::create([
            'webinar_id' => $webinar->id,
            'user_id'    => $user->id,
            'status'     => 'registered',
        ]);

        // Inisialisasi Pencatatan Learning Record (KAK Poin 14)
        $jpEarned = $webinar->jp ?: ($webinar->course->jp ?? 2);
        WebinarLearningRecord::firstOrCreate(
            ['webinar_id' => $webinar->id, 'user_id' => $user->id],
            [
                'registered' => true,
                'jp_earned'  => $jpEarned,
            ]
        );

        // Kirim Notifikasi In-App (KAK Bab XXIII & Bab IX)
        \App\Services\NotificationService::send(
            $user,
            'Pendaftaran Webinar Berhasil',
            "Pendaftaran Anda untuk kegiatan '{$webinar->title}' telah terkonfirmasi. Nomor Tiket: {$reg->ticket_code}.",
            route('webinars.show', $webinar->slug),
            'webinar',
            'video'
        );

        return back()->with('success', 'Selamat! Pendaftaran Anda berhasil. Tiket: ' . $reg->ticket_code);
    }

    /**
     * Presensi Mandiri Peserta (KAK Poin 9 & 14)
     */
    public function recordAttendance(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();
        $webinar = Webinar::where('slug', $slug)->firstOrFail();

        $registration = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($registration->is_attended) {
            return back()->with('info', 'Anda sudah melakukan presensi kehadiran sebelumnya.');
        }

        $registration->markAttended('self');

        // Update Learning Record
        WebinarLearningRecord::updateOrCreate(
            ['webinar_id' => $webinar->id, 'user_id' => $user->id],
            [
                'registered' => true,
                'attended'   => true,
            ]
        );

        return back()->with('success', 'Presensi kehadiran Anda berhasil dicatat!');
    }

    /**
     * Unduh / Akses Materi Webinar (KAK Poin 10 & 14)
     */
    public function downloadMaterial(string $slug, WebinarMaterial $material): RedirectResponse
    {
        $user = Auth::user();
        $webinar = Webinar::where('slug', $slug)->firstOrFail();

        if ($user) {
            // Update Learning Record materi terunduh
            $record = WebinarLearningRecord::firstOrCreate(
                ['webinar_id' => $webinar->id, 'user_id' => $user->id],
                ['registered' => true]
            );
            $record->update(['material_downloaded' => true]);
        }

        $url = $material->download_url;
        if (!$url) {
            return back()->with('error', 'Tautan materi tidak tersedia.');
        }

        return redirect()->away($url);
    }

    /**
     * Kirim Evaluasi Pembelajaran (KAK Poin 12, 13, 14)
     */
    public function submitEvaluation(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();
        $webinar = Webinar::where('slug', $slug)->firstOrFail();

        $request->validate([
            'rating_materi'      => 'required|integer|min:1|max:5',
            'rating_narasumber'  => 'required|integer|min:1|max:5',
            'rating_platform'    => 'required|integer|min:1|max:5',
            'rating_relevansi'   => 'required|integer|min:1|max:5',
            'rating_overall'     => 'required|integer|min:1|max:5',
            'saran'              => 'nullable|string|max:1000',
            'komentar'           => 'nullable|string|max:1000',
            'will_recommend'     => 'nullable|boolean',
        ]);

        $eval = WebinarEvaluation::updateOrCreate(
            ['webinar_id' => $webinar->id, 'user_id' => $user->id],
            [
                'rating_materi'     => $request->rating_materi,
                'rating_narasumber' => $request->rating_narasumber,
                'rating_platform'   => $request->rating_platform,
                'rating_relevansi'  => $request->rating_relevansi,
                'rating_overall'    => $request->rating_overall,
                'saran'             => $request->saran,
                'komentar'          => $request->komentar,
                'will_recommend'    => $request->boolean('will_recommend', true),
                'submitted_at'      => now(),
            ]
        );

        // Update Learning Record & cek penerbitan sertifikat
        $record = WebinarLearningRecord::firstOrCreate(
            ['webinar_id' => $webinar->id, 'user_id' => $user->id],
            ['registered' => true]
        );

        $certNumber = $record->certificate_number;
        $certIssued = $record->certificate_issued;

        // Jika webinar menyediakan sertifikat dan peserta telah hadir
        if ($webinar->cert_available && $record->attended && !$certIssued) {
            $certIssued = true;
            $certNumber = 'WBN/' . date('Y') . '/' . strtoupper(Str::random(6)) . '/' . sprintf('%04d', $user->id);

            // Kirim Notifikasi In-App E-Sertifikat Terbit (KAK Bab XXIII & Bab IX)
            \App\Services\NotificationService::send(
                $user,
                'E-Sertifikat Webinar Telah Terbit',
                "Selamat! E-Sertifikat untuk kegiatan '{$webinar->title}' telah terbit dan siap diunduh.",
                route('webinars.certificate', $webinar->slug),
                'certificate',
                'award'
            );
        }

        $record->update([
            'evaluation_submitted' => true,
            'certificate_issued'   => $certIssued,
            'certificate_number'   => $certNumber,
            'completed_at'         => now(),
        ]);

        return back()->with('success', 'Terima kasih! Evaluasi pembelajaran berhasil dikirim. E-Sertifikat Anda telah diterbitkan.');
    }

    /**
     * Tampilan E-Sertifikat Webinar (KAK Poin 13)
     */
    public function certificate(string $slug): View
    {
        $user = Auth::user();
        $webinar = Webinar::with('course')->where('slug', $slug)->firstOrFail();

        $record = WebinarLearningRecord::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$record->certificate_issued) {
            abort(403, 'Sertifikat belum diterbitkan. Pastikan Anda telah melakukan presensi dan mengisi evaluasi.');
        }

        return view('webinars.certificate', compact('webinar', 'user', 'record'));
    }
}
