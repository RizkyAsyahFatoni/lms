<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use App\Models\WebinarLearningRecord;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Presensi Melalui Tombol Presensi Digital (KAK Metode 2)
     */
    public function submitButton(Request $request, AttendanceSession $session): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk melakukan presensi.');
        }

        $user = Auth::user();

        // 1. Validasi Metode
        if (!in_array($session->allowed_methods, ['both', 'button'])) {
            return back()->with('error', 'Metode presensi melalui tombol tidak diizinkan untuk kegiatan ini. Gunakan pemindaian QR Code.');
        }

        // 2. Validasi Jendela Waktu
        if (!$session->isOpen()) {
            return back()->with('error', 'Presensi digital sedang ditutup atau berada di luar jendela waktu yang ditentukan.');
        }

        // 3. Cek Duplikasi
        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('info', 'Anda sudah melakukan presensi pada ' . $existing->check_in_at->format('d/m/Y H:i') . ' WIB dengan status: ' . $existing->status_label);
        }

        // 4. Evaluasi Status Kehadiran (Hadir / Terlambat)
        $now = now();
        $status = $session->evaluateStatus($now);

        // 5. Simpan Data Presensi Lengkap Sesuai KAK
        $attendance = Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id'               => $user->id,
            'participant_name'      => $user->name,
            'participant_nip'       => $user->nip,
            'participant_opd'       => $user->opd_name ?: ($user->headline ?: null),
            'participant_jabatan'   => $user->jabatan,
            'attendable_type'       => $session->attendable_type,
            'attendable_id'         => $session->attendable_id,
            'activity_title'        => $session->title,
            'session_code'          => $session->session_code,
            'date'                  => $now->toDateString(),
            'time'                  => $now->toTimeString(),
            'check_in_at'           => $now,
            'status'                => $status,
            'method'                => 'button',
            'duration_minutes'      => $session->min_duration_minutes, // default setara minimal
            'min_duration_minutes'  => $session->min_duration_minutes,
            'is_duration_qualified' => true,
            'ip_address'            => $request->ip(),
            'user_agent'            => $request->userAgent(),
            'notes'                 => $request->input('notes'),
        ]);

        // 6. Sinkronisasi ke Kegiatan Terkait
        $this->syncAttendanceRelations($attendance, $session, $user, 'button');

        // 7. Audit Log
        ActivityLog::record(
            'digital_attendance',
            "Presensi berhasil via Tombol Presensi: {$user->name} pada kegiatan {$session->title} (Status: {$attendance->status_label})",
            $user->id,
            ['session_id' => $session->id, 'status' => $status, 'method' => 'button']
        );

        $statusMsg = $status === 'late' ? ' (Tercatat Terlambat)' : ' (Tepat Waktu)';
        return back()->with('success', "Presensi digital Anda berhasil dicatat{$statusMsg}! Terima kasih atas partisipasinya.");
    }

    /**
     * Tampilan / Halaman Pemindaian QR Code (KAK Metode 1)
     */
    public function scanQr(Request $request, string $code): View|RedirectResponse
    {
        $session = AttendanceSession::where('session_code', $code)->firstOrFail();

        // Validasi Token Rahasia QR jika ada
        if ($request->filled('token') && $session->qr_secret_token !== $request->token) {
            return redirect()->route('home')->with('error', 'Token QR Code tidak valid atau telah kedaluwarsa.');
        }

        if (!Auth::check()) {
            session(['url.intended' => url()->full()]);
            return redirect()->route('login')->with('info', "Silakan login terlebih dahulu untuk mencatat presensi: {$session->title}");
        }

        $user = Auth::user();
        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        return view('attendance.scan', compact('session', 'user', 'existing'));
    }

    /**
     * Proses Presensi via QR Code (Scan / Submit)
     */
    public function submitQr(Request $request, AttendanceSession $session): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // 1. Validasi Metode
        if (!in_array($session->allowed_methods, ['both', 'qr_code'])) {
            return back()->with('error', 'Metode presensi melalui QR Code tidak diizinkan untuk kegiatan ini.');
        }

        // 2. Validasi Jendela Waktu
        if (!$session->isOpen()) {
            return back()->with('error', 'Presensi digital kegiatan ini telah ditutup.');
        }

        // 3. Validasi Token QR jika disertakan
        if ($request->filled('token') && $session->qr_secret_token !== $request->token) {
            return back()->with('error', 'Token QR Code tidak cocok atau telah diperbarui oleh panitia.');
        }

        // 4. Cek Duplikasi
        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('info', 'Anda sudah melakukan presensi sebelumnya pada ' . $existing->check_in_at->format('d/m/Y H:i') . ' WIB.');
        }

        // 5. Evaluasi Status Kehadiran
        $now = now();
        $status = $session->evaluateStatus($now);

        // 6. Simpan Catatan Presensi
        $attendance = Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id'               => $user->id,
            'participant_name'      => $user->name,
            'participant_nip'       => $user->nip,
            'participant_opd'       => $user->opd_name ?: ($user->headline ?: null),
            'participant_jabatan'   => $user->jabatan,
            'attendable_type'       => $session->attendable_type,
            'attendable_id'         => $session->attendable_id,
            'activity_title'        => $session->title,
            'session_code'          => $session->session_code,
            'date'                  => $now->toDateString(),
            'time'                  => $now->toTimeString(),
            'check_in_at'           => $now,
            'status'                => $status,
            'method'                => 'qr_code',
            'duration_minutes'      => $session->min_duration_minutes,
            'min_duration_minutes'  => $session->min_duration_minutes,
            'is_duration_qualified' => true,
            'qr_token'              => $request->input('token', $session->qr_secret_token),
            'ip_address'            => $request->ip(),
            'user_agent'            => $request->userAgent(),
            'notes'                 => $request->input('notes'),
        ]);

        // 7. Sinkronisasi ke Kegiatan Terkait
        $this->syncAttendanceRelations($attendance, $session, $user, 'qr_code');

        // 8. Audit Log
        ActivityLog::record(
            'digital_attendance',
            "Presensi berhasil via Scan QR Code: {$user->name} pada kegiatan {$session->title} (Status: {$attendance->status_label})",
            $user->id,
            ['session_id' => $session->id, 'status' => $status, 'method' => 'qr_code']
        );

        $statusMsg = $status === 'late' ? ' (Tercatat Terlambat)' : ' (Tepat Waktu)';
        return redirect()->route('attendance.history')->with('success', "Presensi QR Code Anda berhasil dicatat{$statusMsg}!");
    }

    /**
     * Checkout / Presensi Keluar untuk menghitung durasi presensi aktual
     */
    public function checkout(Request $request, Attendance $attendance): RedirectResponse
    {
        $user = Auth::user();

        if ($attendance->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($attendance->check_out_at) {
            return back()->with('info', 'Anda sudah melakukan presensi selesai sebelumnya.');
        }

        $now = now();
        $checkIn = $attendance->check_in_at;
        $durationMinutes = max(1, $checkIn->diffInMinutes($now));
        $qualified = $durationMinutes >= $attendance->min_duration_minutes;

        $attendance->update([
            'check_out_at'          => $now,
            'duration_minutes'      => $durationMinutes,
            'is_duration_qualified' => $qualified,
        ]);

        ActivityLog::record(
            'attendance_checkout',
            "Peserta {$user->name} menyelesaikan sesi presensi ({$durationMinutes} menit) pada {$attendance->activity_title}",
            $user->id,
            ['attendance_id' => $attendance->id, 'duration' => $durationMinutes]
        );

        return back()->with('success', "Presensi selesai dicatat. Durasi kehadiran Anda: {$durationMinutes} menit.");
    }

    /**
     * Riwayat Presensi Pribadi ASN / Peserta
     */
    public function history(): View
    {
        $user = Auth::user();
        $attendances = Attendance::with('session')
            ->where('user_id', $user->id)
            ->latest('check_in_at')
            ->paginate(15);

        $stats = [
            'total'     => Attendance::where('user_id', $user->id)->count(),
            'present'   => Attendance::where('user_id', $user->id)->where('status', 'present')->count(),
            'late'      => Attendance::where('user_id', $user->id)->where('status', 'late')->count(),
            'total_min' => Attendance::where('user_id', $user->id)->sum('duration_minutes'),
        ];

        return view('attendance.history', compact('attendances', 'stats', 'user'));
    }

    /**
     * Sinkronisasi ke modul Webinar atau Course terkait
     */
    private function syncAttendanceRelations(Attendance $attendance, AttendanceSession $session, $user, string $method): void
    {
        // 1. Jika terhubung ke Webinar
        if ($session->attendable_type === Webinar::class && $session->attendable_id) {
            $webinar = Webinar::find($session->attendable_id);
            if ($webinar) {
                $reg = WebinarRegistration::firstOrCreate(
                    ['webinar_id' => $webinar->id, 'user_id' => $user->id],
                    ['status' => 'confirmed']
                );

                $reg->update([
                    'is_attended'       => true,
                    'attended_at'       => $attendance->check_in_at,
                    'attendance_method' => $method,
                ]);

                WebinarLearningRecord::updateOrCreate(
                    ['webinar_id' => $webinar->id, 'user_id' => $user->id],
                    ['attended' => true, 'registered' => true]
                );
            }
        }

        // 2. Jika terhubung ke Course
        if ($session->attendable_type === Course::class && $session->attendable_id) {
            $course = Course::find($session->attendable_id);
            if ($course) {
                $enrollment = Enrollment::firstOrCreate(
                    ['course_id' => $course->id, 'user_id' => $user->id],
                    ['progress_percent' => 0]
                );

                $enrollment->update([
                    'attended_at'      => $attendance->check_in_at,
                    'progress_percent' => max($enrollment->progress_percent, 100),
                    'completed_at'     => $enrollment->completed_at ?: now(),
                ]);
            }
        }
    }
}
