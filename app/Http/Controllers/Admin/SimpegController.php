<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SimpegSetting;
use App\Models\User;
use App\Services\SimpegApiService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SimpegController extends Controller
{
    protected SimpegApiService $simpegService;

    public function __construct(SimpegApiService $simpegService)
    {
        $this->simpegService = $simpegService;
    }

    private function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'bkpsdm'])) {
            abort(403, 'Akses ditolak. Khusus Super Admin / BKPSDM.');
        }
    }

    /**
     * Halaman Konfigurasi Integrasi REST API SIMPEG Kabupaten
     */
    public function index(): View
    {
        $this->checkAdmin();

        $setting = SimpegSetting::getSettings();
        $totalAsn = User::whereNotNull('nip')->count();
        $totalOpd = User::whereNotNull('opd_name')->where('opd_name', '!=', '')->distinct('opd_name')->count('opd_name');

        return view('admin.integrations.simpeg', compact('setting', 'totalAsn', 'totalOpd'));
    }

    /**
     * Simpan Perubahan Konfigurasi REST API SIMPEG & Field Mapping
     */
    public function update(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'is_active'          => 'nullable|boolean',
            'provider_name'      => 'required|string|max:150',
            'server_url'         => 'required|url|max:255',
            'auth_type'          => 'required|in:bearer,api_key_header,basic,none',
            'api_key'            => 'nullable|string',
            'header_name'        => 'nullable|string|max:100',
            'endpoint_all'       => 'required|string|max:150',
            'endpoint_single'    => 'required|string|max:150',
            'data_wrapper'       => 'nullable|string|max:50',
            'default_password'   => 'nullable|string|min:6|max:100',
            'auto_sync_on_login' => 'nullable|boolean',
            'field_mapping'      => 'nullable|array',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['auto_sync_on_login'] = $request->boolean('auto_sync_on_login');

        // Normalisasi field mapping
        $mapping = $request->input('field_mapping', []);
        $cleanMapping = [];
        foreach ($mapping as $key => $val) {
            if (!empty(trim((string)$val))) {
                $cleanMapping[$key] = trim((string)$val);
            }
        }
        $validated['field_mapping'] = $cleanMapping;

        $setting = SimpegSetting::getSettings();
        $setting->update($validated);

        return back()->with('success', 'Konfigurasi integrasi REST API SIMPEG berhasil disimpan! Sistem kini siap ' . ($setting->is_active ? 'TERHUBUNG (Aktif)' : 'SIAP PAKAI (Nonaktif)') . '.');
    }

    /**
     * Uji Koneksi / Ping ke Endpoint REST API SIMPEG
     */
    public function testConnection(): RedirectResponse
    {
        $this->checkAdmin();

        $result = $this->simpegService->testConnection();

        if ($result['success']) {
            $msg = $result['message'];
            if (!empty($result['sample_data'])) {
                $msg .= ' | Sampel Respons: ' . json_encode(array_slice($result['sample_data'], 0, 4));
            }
            return back()->with('test_success', $msg);
        }

        return back()->with('test_error', $result['message']);
    }

    /**
     * Tarik Seluruh Data Pegawai dari REST API SIMPEG (Sync All ASN)
     */
    public function syncAll(): RedirectResponse
    {
        $this->checkAdmin();

        $result = $this->simpegService->pullAllPegawai();

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Tarik Data 1 ASN Spesifik Berdasarkan NIP dari SIMPEG
     */
    public function syncByNip(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'nip' => 'required|string|min:8|max:30',
        ], [
            'nip.required' => 'Masukkan NIP yang ingin disinkronkan.',
        ]);

        $result = $this->simpegService->pullByNip($request->nip);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
