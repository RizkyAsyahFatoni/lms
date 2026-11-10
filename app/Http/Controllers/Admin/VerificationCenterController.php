<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use App\Models\ExternalTraining;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationCenterController extends Controller
{
    private function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'bkpsdm'])) {
            abort(403, 'Akses ditolak. Khusus Super Admin / BKPSDM.');
        }
    }

    /**
     * Dashboard Terpadu Pusat Verifikasi & Approval Administrator
     */
    public function index(Request $request): View
    {
        $this->checkAdmin();

        $activeTab = $request->input('tab', 'all'); // 'all', 'asn', 'jp'

        // 1. Antrean Pengajuan Perubahan Data ASN
        $pendingAsnRequests = ProfileChangeRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 2. Antrean Pengajuan JP Eksternal
        $pendingExternalTrainings = ExternalTraining::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Statistik Metrik
        $stats = [
            'pending_asn'          => $pendingAsnRequests->count(),
            'pending_external'     => $pendingExternalTrainings->count(),
            'pending_external_jp'  => (int) $pendingExternalTrainings->sum('jp_count'),
            'total_pending'        => $pendingAsnRequests->count() + $pendingExternalTrainings->count(),
            'approved_asn'         => ProfileChangeRequest::where('status', 'approved')->count(),
            'approved_external'    => ExternalTraining::where('status', 'approved')->count(),
            'approved_external_jp' => (int) ExternalTraining::where('status', 'approved')->sum('jp_count'),
        ];

        return view('admin.verifications.index', compact(
            'pendingAsnRequests',
            'pendingExternalTrainings',
            'stats',
            'activeTab'
        ));
    }
}
