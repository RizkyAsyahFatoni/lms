<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\JpService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JpReportController extends Controller
{
    protected JpService $jpService;

    public function __construct(JpService $jpService)
    {
        $this->jpService = $jpService;
    }

    /**
     * Check if current user is authorized to view JP reports
     */
    protected function checkAuthorizedUser(): User
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // Allow Admin, Super Admin, BKPSDM, Admin OPD, and Pimpinan
        $allowedRoles = ['admin', 'super_admin', 'bkpsdm', 'admin_opd', 'pimpinan'];
        if (!in_array($user->role, $allowedRoles) && !$user->hasPermission('view_reports') && !$user->hasPermission('manage_courses')) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka modul Rekapitulasi Jam Pelajaran (JP).');
        }

        return $user;
    }

    /**
     * Helper to enforce OPD boundary for Admin OPD
     */
    protected function applyOpdScope(Request $request, array &$filters): void
    {
        $user = Auth::user();
        if ($user && $user->role === 'admin_opd' && !empty($user->opd_name)) {
            $filters['opd_name'] = $user->opd_name;
        }
    }

    /**
     * 1. Dashboard Overview Rekapitulasi JP
     */
    public function index(Request $request)
    {
        $this->checkAuthorizedUser();

        $filters = $request->only([
            'year', 'period_type', 'start_date', 'end_date', 'opd_name', 'status', 'search', 'learning_type'
        ]);
        $this->applyOpdScope($request, $filters);

        $asnReport = $this->jpService->getAsnJpReport($filters);
        $opdReport = $this->jpService->getOpdJpReport($filters);
        $appSetting = AppSetting::first();

        return view('admin.jp.index', [
            'asnReport'     => $asnReport,
            'opdReport'     => $opdReport,
            'learningTypes' => JpService::LEARNING_TYPES,
            'appSetting'    => $appSetting,
            'filters'       => $asnReport['filters'],
        ]);
    }

    /**
     * 2. Rekapitulasi JP per ASN
     */
    public function perAsn(Request $request)
    {
        $this->checkAuthorizedUser();

        $filters = $request->only([
            'year', 'period_type', 'start_date', 'end_date', 'opd_name', 'status', 'search', 'learning_type'
        ]);
        $this->applyOpdScope($request, $filters);

        $report = $this->jpService->getAsnJpReport($filters);
        $appSetting = AppSetting::first();

        return view('admin.jp.asn', [
            'report'        => $report,
            'learningTypes' => JpService::LEARNING_TYPES,
            'appSetting'    => $appSetting,
            'filters'       => $report['filters'],
        ]);
    }

    /**
     * 3. Rekapitulasi JP per OPD
     */
    public function perOpd(Request $request)
    {
        $this->checkAuthorizedUser();

        $filters = $request->only([
            'year', 'period_type', 'start_date', 'end_date', 'search'
        ]);
        $this->applyOpdScope($request, $filters);

        $report = $this->jpService->getOpdJpReport($filters);
        $appSetting = AppSetting::first();

        return view('admin.jp.opd', [
            'report'        => $report,
            'learningTypes' => JpService::LEARNING_TYPES,
            'appSetting'    => $appSetting,
            'filters'       => $report['filters'],
        ]);
    }

    /**
     * 4. Detail Riwayat JP Satu ASN
     */
    public function detailAsn(User $user, Request $request)
    {
        $currentUser = $this->checkAuthorizedUser();

        // Enforce OPD boundary for Admin OPD
        if ($currentUser->role === 'admin_opd' && !empty($currentUser->opd_name) && $user->opd_name !== $currentUser->opd_name) {
            abort(403, 'Anda hanya dapat melihat riwayat ASN dari perangkat daerah Anda sendiri.');
        }

        $year       = $request->filled('year') && $request->year !== 'all' ? (int) $request->year : (int) date('Y');
        $periodType = $request->get('period_type', 'all');
        $startDate  = $request->get('start_date');
        $endDate    = $request->get('end_date');

        $summary    = $this->jpService->getUserJpSummary($user, $year, $periodType, $startDate, $endDate);
        $activities = $this->jpService->getUserJpActivities($user, $year, $periodType, $startDate, $endDate);
        $appSetting = AppSetting::first();

        return view('admin.jp.detail_asn', [
            'targetUser'    => $user,
            'summary'       => $summary,
            'activities'    => $activities,
            'learningTypes' => JpService::LEARNING_TYPES,
            'appSetting'    => $appSetting,
            'year'          => $year,
            'periodType'    => $periodType,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
        ]);
    }

    /**
     * 5. Ekspor CSV Data JP per ASN
     */
    public function exportAsn(Request $request): Response
    {
        $this->checkAuthorizedUser();

        $filters = $request->only([
            'year', 'period_type', 'start_date', 'end_date', 'opd_name', 'status', 'search', 'learning_type'
        ]);
        $this->applyOpdScope($request, $filters);

        $report = $this->jpService->getAsnJpReport($filters);
        $csvContent = $this->jpService->generateAsnCsv($report['rows'], $report['range']['label']);

        // UU PDP Compliance: Catat log audit saat data ASN diekspor secara massal
        ActivityLog::recordExport('Rekapitulasi JP per ASN', count($report['rows']), 'CSV', $filters);

        $filename = 'rekapitulasi_jp_asn_' . str_replace(' ', '_', strtolower($report['range']['label'])) . '_' . date('Ymd_His') . '.csv';

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    /**
     * 6. Ekspor CSV Data JP per OPD
     */
    public function exportOpd(Request $request): Response
    {
        $this->checkAuthorizedUser();

        $filters = $request->only([
            'year', 'period_type', 'start_date', 'end_date', 'search'
        ]);
        $this->applyOpdScope($request, $filters);

        $report = $this->jpService->getOpdJpReport($filters);
        $csvContent = $this->jpService->generateOpdCsv($report['rows'], $report['range']['label']);

        // UU PDP Compliance: Catat log audit saat data OPD diekspor
        ActivityLog::recordExport('Rekapitulasi JP per OPD', count($report['rows']), 'CSV', $filters);

        $filename = 'rekapitulasi_jp_opd_' . str_replace(' ', '_', strtolower($report['range']['label'])) . '_' . date('Ymd_His') . '.csv';

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    /**
     * 7. Format Cetak Laporan Resmi (PDF Ready)
     */
    public function printReport(Request $request)
    {
        $this->checkAuthorizedUser();

        $type = $request->get('type', 'asn'); // asn or opd
        $filters = $request->only([
            'year', 'period_type', 'start_date', 'end_date', 'opd_name', 'status', 'search', 'learning_type'
        ]);
        $this->applyOpdScope($request, $filters);

        $asnReport = $this->jpService->getAsnJpReport($filters);
        $opdReport = $this->jpService->getOpdJpReport($filters);
        $appSetting = AppSetting::first();

        return view('admin.jp.print', [
            'type'          => $type,
            'asnReport'     => $asnReport,
            'opdReport'     => $opdReport,
            'learningTypes' => JpService::LEARNING_TYPES,
            'appSetting'    => $appSetting,
            'filters'       => $asnReport['filters'],
        ]);
    }
}
