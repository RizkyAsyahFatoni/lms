<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\OpdActivityRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

/**
 * OpdRequestController — Mengelola Alur Pengajuan & Approval Kegiatan OPD (KAK Pasal VII)
 *
 * Flow:
 *   Admin OPD → Ajukan kegiatan (status: pending)
 *   BKPSDM/Super Admin → Review & setujui / tolak / minta revisi
 *   Jika disetujui → Course dibuat otomatis dan dipublikasikan
 */
class OpdRequestController extends Controller
{
    /**
     * Daftar semua pengajuan (Super Admin/BKPSDM) atau pengajuan OPD sendiri (Admin OPD)
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = OpdActivityRequest::with(['submitter', 'reviewer', 'course'])->latest();

        // Admin OPD hanya lihat milik OPD-nya sendiri
        if ($user->isAdminOpd()) {
            $query->where('submitted_by', $user->id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter OPD
        if ($request->filled('opd') && !$user->isAdminOpd()) {
            $query->where('opd_name', $request->opd);
        }

        // Pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%{$q}%")
                   ->orWhere('opd_name', 'like', "%{$q}%")
                   ->orWhere('speaker_name', 'like', "%{$q}%");
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        $stats = [
            'total'           => OpdActivityRequest::count(),
            'pending'         => OpdActivityRequest::where('status', 'pending')->count(),
            'under_review'    => OpdActivityRequest::where('status', 'under_review')->count(),
            'approved'        => OpdActivityRequest::where('status', 'approved')->count(),
            'rejected'        => OpdActivityRequest::where('status', 'rejected')->count(),
            'revision_needed' => OpdActivityRequest::where('status', 'revision_needed')->count(),
        ];

        $opdList = OpdActivityRequest::distinct()->pluck('opd_name')->filter()->sort();

        return view('admin.opd-requests.index', compact('requests', 'stats', 'opdList'));
    }

    /**
     * Form pengajuan kegiatan baru oleh Admin OPD
     */
    public function create(): View
    {
        $user = auth()->user();
        if (!$user->isAdminOpd() && !$user->isSuperAdmin()) {
            abort(403, 'Hanya Admin OPD yang dapat mengajukan kegiatan.');
        }

        return view('admin.opd-requests.create');
    }

    /**
     * Simpan pengajuan kegiatan baru
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->isAdminOpd() && !$user->isSuperAdmin()) {
            abort(403, 'Hanya Admin OPD yang dapat mengajukan kegiatan.');
        }

        $validated = $request->validate([
            'title'                   => 'required|string|max:255',
            'description'             => 'nullable|string',
            'learning_method'         => 'required|string',
            'course_type'             => 'nullable|string',
            'jp'                      => 'nullable|integer|min:1|max:500',
            'target_audience'         => 'nullable|string|max:500',
            'objectives'              => 'nullable|string',
            'speaker_name'            => 'nullable|string|max:255',
            'proposed_start_date'     => 'nullable|date',
            'proposed_end_date'       => 'nullable|date|after_or_equal:proposed_start_date',
            'proposed_location'       => 'nullable|string|max:255',
            'estimated_participants'  => 'nullable|integer|min:1',
            'budget_source'           => 'nullable|string|max:255',
            'supporting_notes'        => 'nullable|string',
            'attachment'              => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $name = \App\Services\SecurityService::validateAndSanitizeUpload(
                $file,
                'attachment',
                ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
                5242880
            );
            $destination = public_path('uploads/opd-requests');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $name);
            $attachmentPath = 'uploads/opd-requests/' . $name;
        }

        $opdRequest = OpdActivityRequest::create([
            ...$validated,
            'submitted_by'    => $user->id,
            'opd_name'        => $user->opd_name ?? $request->input('opd_name', $user->opd_name),
            'status'          => 'pending',
            'attachment_path' => $attachmentPath,
        ]);

        ActivityLog::record(
            'opd_request_submit',
            "OPD [{$opdRequest->opd_name}] mengajukan kegiatan: \"{$opdRequest->title}\"",
            $user->id,
            ['request_id' => $opdRequest->id]
        );

        return redirect()->route('admin.opd-requests.index')
            ->with('success', "Pengajuan kegiatan \"{$opdRequest->title}\" berhasil dikirim dan menunggu review BKPSDM.");
    }

    /**
     * Detail pengajuan
     */
    public function show(OpdActivityRequest $opdRequest): View
    {
        $user = auth()->user();

        // Admin OPD hanya bisa lihat miliknya sendiri
        if ($user->isAdminOpd() && $opdRequest->submitted_by !== $user->id) {
            abort(403);
        }

        $opdRequest->load(['submitter', 'reviewer', 'course']);
        return view('admin.opd-requests.show', compact('opdRequest'));
    }

    /**
     * BKPSDM/Super Admin: Setujui pengajuan → buat Course otomatis
     */
    public function approve(Request $request, OpdActivityRequest $opdRequest): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin / BKPSDM yang dapat menyetujui pengajuan.');
        }

        $request->validate([
            'review_notes' => 'nullable|string',
        ]);

        // Buat Course baru secara otomatis
        $slug = Str::slug($opdRequest->title);
        $count = Course::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) $slug .= '-' . ($count + 1);

        $course = Course::create([
            'title'                      => $opdRequest->title,
            'slug'                       => $slug,
            'description'                => $opdRequest->description,
            'course_type'                => $opdRequest->course_type ?? 'e-course',
            'learning_method'            => $opdRequest->learning_method,
            'jp'                         => $opdRequest->jp ?? 2,
            'target_audience_description'=> $opdRequest->target_audience,
            'learning_objectives'        => $opdRequest->objectives,
            'start_date'                 => $opdRequest->proposed_start_date,
            'end_date'                   => $opdRequest->proposed_end_date,
            'schedule_location'          => $opdRequest->proposed_location,
            'approval_status'            => 'approved',
            'is_published'               => true,
            'opd_request_id'             => $opdRequest->id,
            'submitter_opd'              => $opdRequest->opd_name,
            'created_by'                 => $opdRequest->submitted_by,
            'thumbnail'                  => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80',
            'price'                      => 0,
            'level'                      => 'Semua Level',
            'duration_hours'             => max(1, (int)(($opdRequest->jp ?? 2) / 2)),
            'category_id'                => \App\Models\Category::value('id') ?? 1, // Kategori default yang valid
            'instructor_id'              => $opdRequest->submitted_by,
            'cert_title'                 => 'SERTIFIKAT KEIKUTSERTAAN',
            'cert_institution_name'      => $opdRequest->opd_name,
            'cert_theme_color'           => 'blue',
            'certificate_available'      => true,
        ]);

        // Update status pengajuan
        $opdRequest->update([
            'status'      => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes'=> $request->review_notes,
            'course_id'   => $course->id,
        ]);

        ActivityLog::record(
            'opd_request_approve',
            "BKPSDM menyetujui pengajuan OPD [{$opdRequest->opd_name}]: \"{$opdRequest->title}\" → Course ID #{$course->id} dibuat",
            $user->id,
            ['request_id' => $opdRequest->id, 'course_id' => $course->id]
        );

        // Notifikasi ke OPD Pemohon
        \App\Services\NotificationService::send(
            $opdRequest->submitted_by,
            'Pengajuan Kegiatan OPD Disetujui',
            "Selamat! Pengajuan kegiatan '{$opdRequest->title}' dari OPD {$opdRequest->opd_name} telah disetujui BKPSDM dan diterbitkan sebagai materi pembelajaran.",
            route('admin.opd-requests.show', $opdRequest->id),
            'approval',
            'building'
        );

        return back()->with('success', "Pengajuan disetujui! Kursus \"{$course->title}\" telah dibuat dan dipublikasikan secara otomatis.");
    }

    /**
     * BKPSDM/Super Admin: Tolak pengajuan
     */
    public function reject(Request $request, OpdActivityRequest $opdRequest): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'review_notes' => 'required|string|min:10',
        ]);

        $opdRequest->update([
            'status'       => 'rejected',
            'reviewed_by'  => $user->id,
            'reviewed_at'  => now(),
            'review_notes' => $request->review_notes,
        ]);

        ActivityLog::record(
            'opd_request_reject',
            "BKPSDM menolak pengajuan OPD [{$opdRequest->opd_name}]: \"{$opdRequest->title}\"",
            $user->id,
            ['request_id' => $opdRequest->id]
        );

        // Notifikasi ke OPD Pemohon
        \App\Services\NotificationService::send(
            $opdRequest->submitted_by,
            'Pengajuan Kegiatan OPD Belum Disetujui',
            "Pengajuan kegiatan '{$opdRequest->title}' ditolak oleh BKPSDM dengan catatan: {$request->review_notes}",
            route('admin.opd-requests.show', $opdRequest->id),
            'approval',
            'building'
        );

        return back()->with('success', "Pengajuan ditolak dan OPD telah diberitahu alasannya.");
    }

    /**
     * BKPSDM/Super Admin: Minta revisi
     */
    public function requestRevision(Request $request, OpdActivityRequest $opdRequest): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'revision_request' => 'required|string|min:10',
            'review_notes'     => 'nullable|string',
        ]);

        $opdRequest->update([
            'status'           => 'revision_needed',
            'reviewed_by'      => $user->id,
            'reviewed_at'      => now(),
            'review_notes'     => $request->review_notes,
            'revision_request' => $request->revision_request,
        ]);

        ActivityLog::record(
            'opd_request_revision',
            "BKPSDM meminta revisi pengajuan OPD [{$opdRequest->opd_name}]: \"{$opdRequest->title}\"",
            $user->id,
            ['request_id' => $opdRequest->id]
        );

        // Notifikasi ke OPD Pemohon
        \App\Services\NotificationService::send(
            $opdRequest->submitted_by,
            'Permintaan Revisi Pengajuan Kegiatan OPD',
            "Pengajuan kegiatan '{$opdRequest->title}' memerlukan revisi: {$request->revision_request}",
            route('admin.opd-requests.show', $opdRequest->id),
            'approval',
            'building'
        );

        return back()->with('success', "Permintaan revisi berhasil dikirim ke Admin OPD.");
    }

    /**
     * Admin OPD: Edit pengajuan yang perlu revisi
     */
    public function edit(OpdActivityRequest $opdRequest): View
    {
        $user = auth()->user();
        if ($user->isAdminOpd() && $opdRequest->submitted_by !== $user->id) {
            abort(403);
        }
        if (!in_array($opdRequest->status, ['draft', 'revision_needed'])) {
            return back()->with('error', 'Pengajuan tidak dapat diedit karena sudah dalam proses review.');
        }

        return view('admin.opd-requests.edit', compact('opdRequest'));
    }

    /**
     * Admin OPD: Simpan revisi dan kirim ulang
     */
    public function update(Request $request, OpdActivityRequest $opdRequest): RedirectResponse
    {
        $user = auth()->user();
        if ($user->isAdminOpd() && $opdRequest->submitted_by !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'description'            => 'nullable|string',
            'learning_method'        => 'required|string',
            'jp'                     => 'nullable|integer|min:1|max:500',
            'target_audience'        => 'nullable|string|max:500',
            'objectives'             => 'nullable|string',
            'speaker_name'           => 'nullable|string|max:255',
            'proposed_start_date'    => 'nullable|date',
            'proposed_end_date'      => 'nullable|date|after_or_equal:proposed_start_date',
            'proposed_location'      => 'nullable|string|max:255',
            'estimated_participants' => 'nullable|integer|min:1',
            'supporting_notes'       => 'nullable|string',
        ]);

        $opdRequest->update([
            ...$validated,
            'status'           => 'pending',  // Kirim ulang untuk review
            'revision_request' => null,        // Clear revision request
        ]);

        ActivityLog::record(
            'opd_request_resubmit',
            "OPD [{$opdRequest->opd_name}] mengirim ulang pengajuan revisi: \"{$opdRequest->title}\"",
            $user->id,
            ['request_id' => $opdRequest->id]
        );

        return redirect()->route('admin.opd-requests.show', $opdRequest)
            ->with('success', "Pengajuan berhasil direvisi dan dikirim kembali ke BKPSDM.");
    }

    /**
     * Hapus pengajuan kegiatan oleh pemohon (Admin OPD) atau Super Admin
     */
    public function destroy(OpdActivityRequest $opdRequest): RedirectResponse
    {
        $user = auth()->user();
        if ($user->isAdminOpd() && $opdRequest->submitted_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus pengajuan OPD ini.');
        }

        $title = $opdRequest->title;
        $opdRequest->delete();

        ActivityLog::record(
            'opd_request_delete',
            "Menghapus pengajuan kegiatan OPD: \"{$title}\"",
            $user->id
        );

        return redirect()->route('admin.opd-requests.index')
            ->with('success', "Pengajuan kegiatan \"{$title}\" berhasil dihapus.");
    }
}
