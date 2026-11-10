<?php

namespace App\Http\Controllers;

use App\Models\GoogleSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth 2.0 Consent Screen.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $setting = GoogleSetting::getSettings();

        if (!$setting->is_active || empty($setting->client_id)) {
            return redirect()->route('login')
                ->with('error', 'Login dengan Google saat ini belum diaktifkan atau kredensial Google Client ID belum dikonfigurasi.');
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $params = http_build_query([
            'client_id' => $setting->client_id,
            'redirect_uri' => route('auth.google.callback'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'select_account',
            'state' => $state,
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$params}");
    }

    /**
     * Handle OAuth callback from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        $setting = GoogleSetting::getSettings();

        if (!$setting->is_active) {
            return redirect()->route('login')
                ->with('error', 'Integrasi Google Login saat ini sedang dinonaktifkan.');
        }

        // Validate state
        $storedState = $request->session()->pull('google_oauth_state');
        if ($storedState && $request->get('state') !== $storedState) {
            return redirect()->route('login')
                ->with('error', 'Validasi sesi Google OAuth gagal (CSRF mismatch). Silakan coba lagi.');
        }

        $code = $request->get('code');
        if (!$code) {
            $errorDesc = $request->get('error_description', $request->get('error', 'Akses dibatalkan oleh pengguna.'));
            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google: ' . $errorDesc);
        }

        try {
            // Exchange code for Access Token
            $tokenResponse = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'client_id' => $setting->client_id,
                'client_secret' => $setting->client_secret,
                'code' => $code,
                'redirect_uri' => route('auth.google.callback'),
                'grant_type' => 'authorization_code',
            ]);

            if ($tokenResponse->failed()) {
                return redirect()->route('login')
                    ->with('error', 'Gagal mendapatkan token autentikasi dari Google. Pastikan Client ID & Secret di menu Pengaturan API telah sesuai.');
            }

            $accessToken = $tokenResponse->json('access_token');

            // Fetch User Profile from Google UserInfo endpoint
            $userInfoResponse = Http::withToken($accessToken)->timeout(10)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($userInfoResponse->failed()) {
                return redirect()->route('login')
                    ->with('error', 'Gagal memverifikasi profil Google pengguna.');
            }

            $googleUser = $userInfoResponse->json();

            return $this->loginOrCreateUser($googleUser, $setting);
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan sistem saat memproses login Google: ' . $e->getMessage());
        }
    }

    /**
     * Sandbox Simulator: Test Google Login flow without external Google credentials.
     */
    public function mockLogin(Request $request): RedirectResponse
    {
        if (!app()->environment('local', 'testing')) {
            abort(404, 'Endpoint simulasi login Google hanya tersedia pada lingkungan pengembangan lokal.');
        }

        $setting = GoogleSetting::getSettings();

        $googleUser = [
            'sub' => 'google_mock_' . rand(100000, 999999),
            'name' => $request->input('name', 'Pengguna Google Demo'),
            'email' => $request->input('email', 'google.user@gmail.com'),
            'picture' => $request->input('picture', null),
        ];

        return $this->loginOrCreateUser($googleUser, $setting);
    }

    /**
     * Helper to authenticate or register user from Google profile data.
     */
    protected function loginOrCreateUser(array $googleUser, GoogleSetting $setting): RedirectResponse
    {
        $email = $googleUser['email'] ?? null;
        $name = $googleUser['name'] ?? 'Pengguna Google';
        $googleId = $googleUser['sub'] ?? null;

        if (!$email) {
            return redirect()->route('login')
                ->with('error', 'Akun Google Anda tidak menyediakan alamat email yang valid.');
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            // Link existing user to Google provider
            $user->update([
                'sso_provider' => 'google',
                'sso_id' => $googleId,
            ]);
        } else {
            if (!$setting->auto_register) {
                return redirect()->route('login')
                    ->with('error', 'Pendaftaran mandiri akun baru melalui Google dinonaktifkan oleh administrator.');
            }

            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => $setting->default_role ?? 'student',
                'sso_provider' => 'google',
                'sso_id' => $googleId,
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', "Selamat datang kembali, {$user->name}! Anda berhasil masuk melalui Akun Google.");
        }

        return redirect()->route('dashboard')
            ->with('success', "Selamat datang, {$user->name}! Autentikasi Google berhasil.");
    }
}
