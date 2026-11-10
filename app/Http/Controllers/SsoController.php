<?php

namespace App\Http\Controllers;

use App\Models\SsoSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    /**
     * Redirect user to external SSO Identity Provider (OIDC / OAuth 2.0).
     */
    public function redirect(Request $request): RedirectResponse
    {
        $setting = SsoSetting::getSettings();

        if (!$setting->is_active) {
            return redirect()->route('login')
                ->with('error', 'Sistem SSO belum diaktifkan oleh Administrator LMS.');
        }

        // Check if authorization URL is configured
        if (empty($setting->authorization_url) || empty($setting->client_id)) {
            return redirect()->route('login')
                ->with('error', 'Konfigurasi SSO belum lengkap. Silakan lengkapi Client ID dan Authorization URL di Admin Panel.');
        }

        $state = Str::random(40);
        session(['sso_state' => $state]);

        $redirectUri = route('auth.sso.callback');

        $query = http_build_query([
            'client_id' => $setting->client_id,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $setting->scopes ?: 'openid profile email nip nik',
            'state' => $state,
        ]);

        return redirect()->away($setting->authorization_url . '?' . $query);
    }

    /**
     * Handle OAuth2 / OIDC callback from SSO Identity Provider.
     */
    public function callback(Request $request): RedirectResponse
    {
        $setting = SsoSetting::getSettings();

        if (!$setting->is_active) {
            return redirect()->route('login')
                ->with('error', 'Sistem SSO tidak aktif.');
        }

        // Validate state token
        $savedState = session('sso_state');
        if ($savedState && $request->input('state') !== $savedState) {
            return redirect()->route('login')
                ->with('error', 'Validasi token keamanan SSO gagal (CSRF State Mismatch).');
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('login')
                ->with('error', 'Kode otorisasi dari penyedia SSO tidak ditemukan: ' . $request->input('error_description', 'Akses ditolak.'));
        }

        try {
            // 1. Exchange code for access token (Server-to-Server)
            $tokenResponse = Http::asForm()->timeout(15)->post($setting->token_url, [
                'grant_type' => 'authorization_code',
                'client_id' => $setting->client_id,
                'client_secret' => $setting->client_secret,
                'redirect_uri' => route('auth.sso.callback'),
                'code' => $code,
            ]);

            if ($tokenResponse->failed()) {
                return redirect()->route('login')
                    ->with('error', 'Gagal menukar token dengan penyedia SSO: ' . $tokenResponse->body());
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                return redirect()->route('login')
                    ->with('error', 'Access token tidak ditemukan dalam respon penyedia SSO.');
            }

            // 2. Fetch user information
            $userinfoResponse = Http::withToken($accessToken)
                ->timeout(15)
                ->get($setting->userinfo_url);

            if ($userinfoResponse->failed()) {
                return redirect()->route('login')
                    ->with('error', 'Gagal mengambil data profil dari UserInfo SSO.');
            }

            $userData = $userinfoResponse->json();
            return $this->loginOrRegisterUser($userData, $setting);

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan komunikasi dengan server SSO: ' . $e->getMessage());
        }
    }

    /**
     * Mock / Sandbox SSO Login Simulator for instant demo testing.
     */
    public function mockLogin(Request $request): RedirectResponse
    {
        if (!app()->environment('local', 'testing')) {
            abort(404, 'Endpoint simulasi login SSO hanya tersedia pada lingkungan pengembangan lokal.');
        }

        $setting = SsoSetting::getSettings();

        // Simulasi payload data pengguna dari provider SSO (hanya tersedia di lingkungan lokal)
        $mockUser = [
            'id' => 'sso-asn-' . rand(1000, 9999),
            'name' => $request->input('name', 'dr. Budi Santoso, Sp.A'),
            'email' => $request->input('email', 'budi.santoso@dinkes.go.id'),
            'nip' => $request->input('nip', '198805142014021003'),
            'nik' => $request->input('nik', '3510091405880002'),
            'headline' => 'ASN Pranata Komputer / Dokter Spesialis RSUD',
        ];

        return $this->loginOrRegisterUser($mockUser, $setting);
    }

    /**
     * Authenticate or auto-register user from SSO data payload.
     */
    private function loginOrRegisterUser(array $userData, SsoSetting $setting): RedirectResponse
    {
        $email = $userData['email'] ?? null;
        $name = $userData['name'] ?? 'Peserta SSO';
        $nip = $userData['nip'] ?? null;
        $nik = $userData['nik'] ?? null;
        $ssoId = $userData['sub'] ?? $userData['id'] ?? null;

        if (!$email) {
            return redirect()->route('login')
                ->with('error', 'Email tidak ditemukan dalam profil SSO.');
        }

        // Match by email or NIP
        $user = User::where('email', $email)
            ->when($nip, function ($q) use ($nip) {
                $q->orWhere('nip', $nip);
            })
            ->first();

        if ($user) {
            // Update NIP/NIK/SSO info if available
            $user->update([
                'nip' => $nip ?: $user->nip,
                'nik' => $nik ?: $user->nik,
                'sso_provider' => $setting->provider_name,
                'sso_id' => $ssoId ?: $user->sso_id,
            ]);
        } else {
            if (!$setting->auto_register) {
                return redirect()->route('login')
                    ->with('error', 'Akun belum terdaftar di sistem LMS dan fitur Auto-Register dinonaktifkan.');
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'nip' => $nip,
                'nik' => $nik,
                'password' => Hash::make(Str::random(32)),
                'role' => $setting->default_role ?: 'student',
                'headline' => $userData['headline'] ?? 'Aparatur Sipil Negara Terintegrasi SSO',
                'sso_provider' => $setting->provider_name,
                'sso_id' => $ssoId,
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'))
            ->with('success', "Selamat datang, {$user->name}! Anda berhasil masuk melalui {$setting->provider_name}.");
    }
}
