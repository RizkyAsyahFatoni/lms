<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Cek aktivasi akun (KAK: aktivasi/nonaktif akun)
            if (!$user->is_active) {
                ActivityLog::record('login_blocked', 'Percobaan masuk ke akun yang dinonaktifkan: ' . $user->email, $user->id);
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda saat ini berstatus NONAKTIF. Silakan hubungi BKPSDM / Administrator untuk aktivasi.',
                ]);
            }

            // Catat aktivitas login (KAK: pencatatan aktivitas pengguna)
            ActivityLog::record('login', 'Pengguna berhasil login ke sistem', $user->id);

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Audit Trail: Catat percobaan login gagal (Deteksi Dini Brute-Force / Credential Stuffing)
        ActivityLog::record(
            'failed_login',
            "Percobaan login gagal untuk akun: {$request->email}",
            null,
            ['attempted_email' => $request->email, 'ip' => $request->ip()]
        );

        throw ValidationException::withMessages([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ]);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        // 1. Honeypot check
        if (!empty($request->input('website_hp_identity'))) {
            throw ValidationException::withMessages([
                'name' => 'Aktivitas tidak wajar terdeteksi. Silakan muat ulang halaman.',
            ]);
        }

        // 2. Time-gate check
        $timeToken = $request->input('form_load_token');
        if (!empty($timeToken)) {
            try {
                $openedAt = (int) decrypt($timeToken);
                if ((time() - $openedAt) < 2) {
                    throw ValidationException::withMessages([
                        'name' => 'Pengisian form terlalu cepat. Silakan coba kembali.',
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore decryption error
            }
        }

        // 3. Validation
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (preg_match('/(https?:\/\/|www\.|\.ru\b|\.com\b|\.net\b|\.org\b)/i', $value)) {
                        $fail('Nama tidak boleh mengandung URL atau domain web.');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (str_contains(explode('@', $value)[0] ?? '', '+')) {
                        $fail('Alamat email dengan tanda plus (+) tidak diizinkan.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nip' => ['nullable', 'string', 'max:30'],
            'opd_name' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan login.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => strip_tags(trim($request->name)),
            'email' => strtolower(trim($request->email)),
            'nip' => $request->filled('nip') ? preg_replace('/[^0-9]/', '', $request->nip) : null,
            'opd_name' => $request->filled('opd_name') ? strip_tags(trim($request->opd_name)) : null,
            'headline' => $request->filled('headline') ? strip_tags(trim($request->headline)) : ($request->filled('opd_name') ? $request->opd_name : null),
            'password' => Hash::make($request->password),
            'role' => 'asn_learner',
            'is_active' => true,
        ]);

        ActivityLog::record('register', "Pendaftaran akun baru: {$user->name} sebagai ASN / Learner", $user->id);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang di platform pembelajaran.');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            ActivityLog::record('logout', 'Pengguna keluar dari sistem', Auth::id());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah keluar dari akun.');
    }
}
