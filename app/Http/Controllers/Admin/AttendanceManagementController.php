<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Webinar;
use App\Models\Course;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceManagementController extends Controller
{
    private function checkAccess(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'bkpsdm', 'admin_opd', 'instructor', 'narasumber'])) {
            abort(403, 'Akses ditolak. Memerlukan wewenang Pengelola Presensi.');
        }
    }

    /**
     * Rekapitulasi Presensi Digital (Tabel Utama)
     */
    public function index(Request $request): View
    {
        $this->checkAccess();

        $query = Attendance::with(['session', 'user'])->latest('check_in_at');

        // Filter Sesi / Kegiatan
        if ($request->filled('session_id')) {
            $query->where('attendance_session_id', $request->session_id);
        }

        // Filter Status Kehadiran
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Metode Presensi
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Pencarian (Nama Peserta, NIP, OPD, Judul Kegiatan)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('participant_name', 'like', "%{$q}%")
                   ->orWhere('participant_nip', 'like', "%{$q}%")
                   ->orWhere('participant_opd', 'like', "%{$q}%")
                   ->orWhere('activity_title', 'like', "%{$q}%")
                   ->orWhere('session_code', 'like', "%{$q}%");
            });
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Statistik Ringkasan
        $stats = [
            'total'     => Attendance::count(),
            'present'   => Attendance::where('status', 'present')->count(),
            'late'      => Attendance::where('status', 'late')->count(),
            'excused'   => Attendance::whereIn('status', ['excused', 'sick'])->count(),
            'qualified' => Attendance::where('is_duration_qualified', true)->count(),
        ];

        $sessions = AttendanceSession::latest('date')->get();

        return view('admin.attendances.index', compact('attendances', 'stats', 'sessions'));
    }

    /**
     * Pengelolaan Sesi Kegiatan Presensi Digital
     */
    public function sessions(Request $request): View
    {
        $this->checkAccess();

        $sessions = AttendanceSession::withCount('attendances')
            ->latest('date')
            ->paginate(15);

        $webinars = Webinar::latest('event_date')->take(20)->get();
        $courses = Course::latest()->take(20)->get();

        return view('admin.attendances.sessions', compact('sessions', 'webinars', 'courses'));
    }

    /**
     * Simpan Sesi Presensi Baru
     */
    public function storeSession(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'title'                  => 'required|string|max:255',
            'date'                   => 'required|date',
            'time_start'             => 'nullable|string',
            'time_end'               => 'nullable|string',
            'tolerance_late_minutes' => 'required|integer|min:0|max:180',
            'min_duration_minutes'   => 'required|integer|min:0|max:480',
            'allowed_methods'        => 'required|in:both,qr_code,button',
            'open_at'                => 'nullable|date',
            'close_at'               => 'nullable|date|after_or_equal:open_at',
        ]);

        $session = AttendanceSession::create([
            'title'                  => strip_tags(trim($request->title)),
            'session_code'           => 'PRES-' . strtoupper(Str::random(8)),
            'attendable_type'        => $request->attendable_type ?: null,
            'attendable_id'          => $request->attendable_id ?: null,
            'date'                   => $request->date,
            'time_start'             => $request->time_start ?: null,
            'time_end'               => $request->time_end ?: null,
            'open_at'                => $request->open_at ? Carbon::parse($request->open_at) : Carbon::parse($request->date . ' ' . ($request->time_start ?? '08:00'))->subMinutes(30),
            'close_at'               => $request->close_at ? Carbon::parse($request->close_at) : Carbon::parse($request->date . ' ' . ($request->time_end ?? '17:00'))->addMinutes(60),
            'tolerance_late_minutes' => (int) $request->tolerance_late_minutes,
            'min_duration_minutes'   => (int) $request->min_duration_minutes,
            'allowed_methods'        => $request->allowed_methods,
            'is_active'              => $request->boolean('is_active', true),
            'qr_secret_token'        => Str::random(40),
            'created_by'             => auth()->id(),
            'notes'                  => $request->notes,
        ]);

        ActivityLog::record(
            'create_attendance_session',
            "Membuat sesi presensi digital: {$session->title} ({$session->session_code})",
            auth()->id(),
            ['session_id' => $session->id]
        );

        return back()->with('success', "Sesi presensi [{$session->title}] berhasil dibuat dengan kode: {$session->session_code}");
    }

    /**
     * Update Konfigurasi Sesi Presensi
     */
    public function updateSession(Request $request, AttendanceSession $session): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'title'                  => 'required|string|max:255',
            'date'                   => 'required|date',
            'tolerance_late_minutes' => 'required|integer|min:0|max:180',
            'min_duration_minutes'   => 'required|integer|min:0|max:480',
            'allowed_methods'        => 'required|in:both,qr_code,button',
        ]);

        $session->update([
            'title'                  => strip_tags(trim($request->title)),
            'date'                   => $request->date,
            'time_start'             => $request->time_start ?: $session->time_start,
            'time_end'               => $request->time_end ?: $session->time_end,
            'open_at'                => $request->open_at ? Carbon::parse($request->open_at) : $session->open_at,
            'close_at'               => $request->close_at ? Carbon::parse($request->close_at) : $session->close_at,
            'tolerance_late_minutes' => (int) $request->tolerance_late_minutes,
            'min_duration_minutes'   => (int) $request->min_duration_minutes,
            'allowed_methods'        => $request->allowed_methods,
            'is_active'              => $request->boolean('is_active', true),
            'notes'                  => $request->notes,
        ]);

        ActivityLog::record(
            'update_attendance_session',
            "Memperbarui konfigurasi sesi presensi: {$session->title}",
            auth()->id(),
            ['session_id' => $session->id]
        );

        return back()->with('success', 'Konfigurasi sesi presensi berhasil diperbarui.');
    }

    /**
     * Buka / Tutup Cepat Sesi Presensi
     */
    public function toggleSession(AttendanceSession $session): RedirectResponse
    {
        $this->checkAccess();

        $session->is_active = !$session->is_active;
        $session->save();

        $statusText = $session->is_active ? 'DIBUKA' : 'DITUTUP';
        ActivityLog::record(
            'toggle_attendance_session',
            "Mengubah status sesi presensi {$session->title} menjadi {$statusText}",
            auth()->id(),
            ['session_id' => $session->id, 'is_active' => $session->is_active]
        );

        return back()->with('success', "Presensi digital untuk [{$session->title}] sekarang {$statusText}.");
    }

    /**
     * Tampilan Layar Penuh Mode Proyektor QR Code
     */
    public function projector(AttendanceSession $session): View
    {
        $this->checkAccess();

        $attendances = $session->attendances()->with('user')->latest('check_in_at')->get();
        $attendedCount = $attendances->count();

        return view('admin.attendances.projector', compact('session', 'attendances', 'attendedCount'));
    }

    /**
     * Input Presensi Manual oleh Panitia
     */
    public function manualStore(Request $request, AttendanceSession $session): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status'  => 'required|in:present,late,excused,sick,absent',
        ]);

        $user = User::findOrFail($request->user_id);

        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('error', "Peserta [{$user->name}] sudah memiliki catatan presensi di sesi ini.");
        }

        $now = now();
        $duration = $request->filled('duration_minutes') ? (int) $request->duration_minutes : $session->min_duration_minutes;

        Attendance::create([
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
            'date'                  => $session->date,
            'time'                  => $now->toTimeString(),
            'check_in_at'           => $now,
            'status'                => $request->status,
            'method'                => 'manual',
            'duration_minutes'      => $duration,
            'min_duration_minutes'  => $session->min_duration_minutes,
            'is_duration_qualified' => $duration >= $session->min_duration_minutes,
            'notes'                 => $request->input('notes', 'Diinput secara manual oleh panitia/pengelola.'),
            'verified_by'           => auth()->id(),
        ]);

        ActivityLog::record(
            'manual_attendance',
            "Panitia mencatat presensi manual untuk {$user->name} pada kegiatan {$session->title} (Status: {$request->status})",
            auth()->id(),
            ['user_id' => $user->id, 'session_id' => $session->id]
        );

        return back()->with('success', "Presensi manual untuk [{$user->name}] berhasil disimpan.");
    }

    /**
     * Update Status Kehadiran oleh Pengelola
     */
    public function updateStatus(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'status' => 'required|in:present,late,excused,sick,absent',
            'notes'  => 'nullable|string|max:500',
        ]);

        $oldStatus = $attendance->status;
        $attendance->update([
            'status'      => $request->status,
            'notes'       => $request->notes ?: $attendance->notes,
            'verified_by' => auth()->id(),
        ]);

        ActivityLog::record(
            'update_attendance_status',
            "Mengubah status presensi {$attendance->participant_name} dari {$oldStatus} ke {$request->status}",
            auth()->id(),
            ['attendance_id' => $attendance->id, 'old_status' => $oldStatus, 'new_status' => $request->status]
        );

        return back()->with('success', "Status kehadiran [{$attendance->participant_name}] berhasil diubah menjadi: {$attendance->status_label}");
    }

    /**
     * Hapus Catatan Presensi
     */
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $this->checkAccess();

        $participantName = $attendance->participant_name;
        $attendance->delete();

        ActivityLog::record(
            'delete_attendance',
            "Menghapus catatan presensi peserta: {$participantName}",
            auth()->id(),
            ['attendance_id' => $attendance->id]
        );

        return back()->with('success', "Catatan presensi [{$participantName}] berhasil dihapus.");
    }

    /**
     * Ekspor Rekapitulasi Presensi ke CSV (Excel Ready)
     */
    public function export(Request $request, ?AttendanceSession $session = null): Response
    {
        $this->checkAccess();

        $query = Attendance::with(['session', 'user'])->latest('check_in_at');
        if ($session && $session->exists) {
            $query->where('attendance_session_id', $session->id);
            $filename = 'daftar_hadir_' . Str::slug($session->title) . '_' . date('Ymd_His') . '.csv';
        } else {
            $filename = 'rekapitulasi_presensi_lms_' . date('Ymd_His') . '.csv';
        }

        $records = $query->get();

        // UU PDP Compliance: Rekam log audit saat data presensi ASN diekspor secara massal
        ActivityLog::recordExport(
            'Daftar Hadir & Presensi ASN',
            $records->count(),
            'CSV',
            ['session_id' => $session?->id, 'session_title' => $session?->title ?? 'Semua Sesi']
        );

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($file, [
                'No',
                'Nama Peserta',
                'NIP',
                'Instansi / OPD',
                'Jabatan',
                'Kegiatan',
                'Kode Sesi',
                'Tanggal',
                'Waktu Hadir',
                'Waktu Selesai',
                'Status Kehadiran',
                'Metode Presensi',
                'Durasi (Menit)',
                'Syarat Durasi Terpenuhi',
                'Alamat IP',
                'Catatan',
            ]);

            $no = 1;
            foreach ($records as $row) {
                fputcsv($file, [
                    $no++,
                    $row->participant_name,
                    $row->participant_nip ? "'" . $row->participant_nip : '-',
                    $row->participant_opd ?: '-',
                    $row->participant_jabatan ?: '-',
                    $row->activity_title,
                    $row->session_code ?: '-',
                    $row->date ? $row->date->format('d/m/Y') : '-',
                    $row->check_in_at ? $row->check_in_at->format('H:i:s') : '-',
                    $row->check_out_at ? $row->check_out_at->format('H:i:s') : '-',
                    $row->status_label,
                    $row->method_label,
                    $row->duration_minutes,
                    $row->is_duration_qualified ? 'Ya' : 'Tidak',
                    $row->ip_address ?: '-',
                    $row->notes ?: '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Lembar Cetak Daftar Hadir Resmi (PDF Print Ready)
     */
    public function print(AttendanceSession $session): View
    {
        $this->checkAccess();

        $attendances = $session->attendances()->orderBy('participant_name')->get();
        return view('admin.attendances.print', compact('session', 'attendances'));
    }
}
