<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\GoogleSetting;
use App\Models\PaymentSetting;
use App\Models\SsoSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    /**
     * Display SSO Settings management page.
     */
    public function ssoSettings(): View
    {
        $setting = SsoSetting::getSettings();
        return view('admin.integrations.sso', compact('setting'));
    }

    /**
     * Update SSO Configuration from Admin UI.
     */
    public function updateSsoSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => 'nullable|boolean',
            'provider_name' => 'required|string|max:100',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'authorization_url' => 'nullable|url|max:255',
            'token_url' => 'nullable|url|max:255',
            'userinfo_url' => 'nullable|url|max:255',
            'scopes' => 'nullable|string|max:255',
            'button_label' => 'required|string|max:150',
            'auto_register' => 'nullable|boolean',
            'default_role' => 'required|string|in:student,instructor',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['auto_register'] = $request->has('auto_register');

        $setting = SsoSetting::getSettings();
        $setting->update($validated);

        return redirect()->back()
            ->with('success', 'Konfigurasi SSO berhasil diperbarui! Sistem SSO kini berstatus ' . ($setting->is_active ? 'AKTIF' : 'NONAKTIF') . '.');
    }

    /**
     * Display External ASN Integration API management, Google OAuth & Payment Gateway configuration.
     */
    public function apiKeys(): View
    {
        $apiKeys = ApiKey::latest()->get();
        $googleSetting = GoogleSetting::getSettings();
        $paymentSetting = PaymentSetting::getSettings();
        $totalAsnParticipants = User::where('role', 'student')->whereNotNull('nip')->count();
        $totalCertificates = Certificate::count();
        $totalJpDelivered = Certificate::join('courses', 'certificates.course_id', '=', 'courses.id')->sum('courses.jp');

        // Sample participant with NIP for live testing console
        $sampleParticipant = User::where('role', 'student')->whereNotNull('nip')->first()
            ?: User::where('role', 'student')->first();

        return view('admin.integrations.api', compact(
            'apiKeys',
            'googleSetting',
            'paymentSetting',
            'totalAsnParticipants',
            'totalCertificates',
            'totalJpDelivered',
            'sampleParticipant'
        ));
    }

    /**
     * Update Payment Gateway configuration from API Settings UI.
     */
    public function updatePaymentSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => 'nullable|boolean',
            'provider' => 'required|string|in:midtrans,xendit,tripay',
            'environment' => 'required|string|in:sandbox,production',
            'merchant_id' => 'nullable|string|max:100',
            'client_key' => 'nullable|string|max:255',
            'server_key' => 'nullable|string|max:255',
            'webhook_secret' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $setting = PaymentSetting::getSettings();
        $setting->update($validated);

        return redirect()->back()
            ->with('success', 'Konfigurasi API Payment Gateway (' . strtoupper($setting->provider) . ') berhasil disimpan! Status: ' . ($setting->is_active ? 'AKTIF' : 'NONAKTIF') . '.');
    }

    /**
     * Update Google OAuth configuration from API Settings UI.
     */
    public function updateGoogleSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => 'nullable|boolean',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'button_label' => 'required|string|max:100',
            'auto_register' => 'nullable|boolean',
            'default_role' => 'required|string|in:student,instructor',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['auto_register'] = $request->has('auto_register');

        $googleSetting = GoogleSetting::getSettings();
        $googleSetting->update($validated);

        return redirect()->back()
            ->with('success', 'Pengaturan API Login with Google berhasil disimpan! Status: ' . ($googleSetting->is_active ? 'AKTIF' : 'NONAKTIF') . '.');
    }

    /**
     * Generate new API Key for an external ASN application.
     */
    public function storeApiKey(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'allowed_ips' => 'nullable|string|max:255',
        ]);

        $token = ApiKey::generateToken();

        ApiKey::create([
            'name' => $validated['name'],
            'token' => $token,
            'allowed_ips' => $validated['allowed_ips'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->back()
            ->with('success', "API Key untuk [{$validated['name']}] berhasil diterbitkan! Token: {$token}");
    }

    /**
     * Toggle API key active status.
     */
    public function toggleApiKey(ApiKey $apiKey): RedirectResponse
    {
        $apiKey->update(['is_active' => !$apiKey->is_active]);

        return redirect()->back()
            ->with('success', "Status API Key [{$apiKey->name}] berhasil diubah menjadi " . ($apiKey->is_active ? 'AKTIF' : 'NONAKTIF') . '.');
    }

    /**
     * Revoke / Delete API Key.
     */
    public function revokeApiKey(ApiKey $apiKey): RedirectResponse
    {
        $name = $apiKey->name;
        $apiKey->delete();

        return redirect()->back()
            ->with('success', "Akses API Key [{$name}] telah dicabut dan dihapus.");
    }
}
