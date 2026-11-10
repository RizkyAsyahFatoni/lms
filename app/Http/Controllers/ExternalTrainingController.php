<?php

namespace App\Http\Controllers;

use App\Models\ExternalTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use App\Services\SecurityService;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExternalTrainingController extends Controller
{
    /**
     * List all external training submissions for the current user.
     */
    public function index(): View
    {
        $user = Auth::user();
        $currentYear = (int) date('Y');

        $trainings = ExternalTraining::where('user_id', $user->id)
            ->orderByDesc('training_date')
            ->get();

        $jpData = $user->getJpByYear($currentYear);

        // Group by year for display
        $trainingsByYear = $trainings->groupBy('year')->sortKeysDesc();

        return view('external-trainings.index', compact(
            'user',
            'trainings',
            'trainingsByYear',
            'jpData',
            'currentYear'
        ));
    }

    /**
     * Show the external training submission form.
     */
    public function create(): View
    {
        return view('external-trainings.create');
    }

    /**
     * Store a new external training submission with deep security validation.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'training_name' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
            'training_date' => 'required|date|before_or_equal:today',
            'training_end_date' => 'nullable|date|after_or_equal:training_date',
            'jp_count' => 'required|integer|min:1|max:100',
            'certificate_file' => 'nullable|file',
            'supporting_file' => 'nullable|file',
            'description' => 'nullable|string|max:1000',
        ], [
            'training_name.required' => 'Nama pelatihan wajib diisi.',
            'institution_name.required' => 'Nama lembaga penyelenggara wajib diisi.',
            'training_date.required' => 'Tanggal kegiatan wajib diisi.',
            'training_date.before_or_equal' => 'Tanggal kegiatan tidak boleh di masa depan.',
            'jp_count.required' => 'Jumlah JP wajib diisi.',
            'jp_count.min' => 'Jumlah JP minimal 1.',
        ]);

        $data = $request->only([
            'training_name', 'institution_name', 'training_date',
            'training_end_date', 'jp_count', 'description'
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // 1. Handle certificate file upload dengan Deep Security Inspection (Magic Bytes & Sanitized UUID)
        if ($request->hasFile('certificate_file')) {
            $certFile = $request->file('certificate_file');
            $safeCertName = SecurityService::validateAndSanitizeUpload(
                $certFile,
                'Berkas Sertifikat',
                ['pdf', 'jpg', 'jpeg', 'png', 'webp'],
                5242880 // 5 MB
            );

            // Simpan ke Private Local Storage (di luar web root publik)
            $storedPath = $certFile->storeAs('external-trainings/certificates', $safeCertName, 'local');
            $data['certificate_file'] = $storedPath;
        }

        // 2. Handle supporting file upload dengan Deep Security Inspection
        if ($request->hasFile('supporting_file')) {
            $suppFile = $request->file('supporting_file');
            $safeSuppName = SecurityService::validateAndSanitizeUpload(
                $suppFile,
                'Berkas Pendukung',
                ['pdf', 'jpg', 'jpeg', 'png', 'webp'],
                5242880 // 5 MB
            );

            // Simpan ke Private Local Storage
            $storedPath = $suppFile->storeAs('external-trainings/supporting', $safeSuppName, 'local');
            $data['supporting_file'] = $storedPath;
        }

        $training = ExternalTraining::create($data);

        // Audit Trail Pengajuan JP Eksternal
        ActivityLog::record(
            'submit_external_jp',
            "Mengajukan JP Pelatihan Eksternal: {$data['training_name']} ({$data['jp_count']} JP)",
            Auth::id(),
            ['training_id' => $training->id, 'jp' => $data['jp_count']]
        );

        if ($request->input('source') === 'dashboard' || !$request->routeIs('external-trainings.create')) {
            return redirect()->route('dashboard', ['tab' => 'eksternal'])
                ->with('success', 'Pengajuan JP pelatihan eksternal "' . $data['training_name'] . '" berhasil dikirim! Menunggu verifikasi admin.');
        }

        return redirect()->route('external-trainings.index')
            ->with('success', 'Bukti pelatihan eksternal berhasil diajukan. Menunggu verifikasi admin.');
    }

    /**
     * Secure Document Stream / Download with Anti-IDOR Authorization Check
     */
    public function viewFile(ExternalTraining $externalTraining, string $type)
    {
        $user = Auth::user();

        // 1. Anti-IDOR Authorization Gate
        if (!SecurityService::canAccessTrainingDocument($user, $externalTraining)) {
            ActivityLog::record(
                'unauthorized_access_attempt',
                "Upaya akses dokumen tidak sah pada pengajuan ID #{$externalTraining->id} (Tipe: {$type})",
                Auth::id(),
                ['target_training_id' => $externalTraining->id, 'owner_id' => $externalTraining->user_id]
            );

            abort(403, 'Akses Ditolak: Anda tidak memiliki otoritas untuk melihat atau mengunduh dokumen kepegawaian ini.');
        }

        // 2. Tentukan path file berdasarkan tipe dokumen
        $filePath = match($type) {
            'certificate' => $externalTraining->certificate_file,
            'supporting'  => $externalTraining->supporting_file,
            default       => null,
        };

        if (!$filePath) {
            abort(404, 'Berkas dokumen tidak ditemukan pada pengajuan ini.');
        }

        // 3. Periksa lokasi berkas (Private Storage first, Fallback ke Legacy Public Storage)
        $disk = 'local';
        if (!Storage::disk('local')->exists($filePath)) {
            if (Storage::disk('public')->exists($filePath)) {
                $disk = 'public';
            } else {
                abort(404, 'File fisik dokumen tidak ditemukan di server penyimpanan.');
            }
        }

        // 4. Catat Audit Log Akses Dokumen Sensitif (UU PDP Compliance)
        ActivityLog::record(
            'view_document',
            "Mengakses dokumen {$type} pada pengajuan pelatihan: {$externalTraining->training_name}",
            Auth::id(),
            ['training_id' => $externalTraining->id, 'document_type' => $type]
        );

        // 5. Stream Response dengan Header Keamanan HTTP
        return Storage::disk($disk)->response($filePath, null, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, no-cache, no-store, must-revalidate',
            'Pragma'                 => 'no-cache',
            'Expires'                => '0',
        ]);
    }
}
