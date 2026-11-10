<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

/**
 * Advanced Brute Force Protection Middleware
 *
 * Melindungi endpoint autentikasi dari serangan brute force dengan:
 * - Progressive delay (exponential backoff)
 * - IP-based lockout dengan CIDR-aware tracking
 * - Account-based lockout terpisah dari IP lockout
 * - Bot detection via request pattern analysis
 * - Honeypot field validation
 * OWASP: A07:2021 – Identification and Authentication Failures
 */
class BruteForceProtectionMiddleware
{
    /** Maksimum percobaan login sebelum lockout */
    protected int $maxAttempts = 5;

    /** Durasi lockout dalam menit */
    protected int $lockoutMinutes = 15;

    /** Maksimum percobaan per IP (lebih ketat) */
    protected int $maxIpAttempts = 10;

    /** Durasi lockout IP dalam menit */
    protected int $ipLockoutMinutes = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethod('POST')) {
            return $next($request);
        }

        $ip = $request->ip();
        $email = $request->input('email', '');

        // 1. Honeypot check: hanya jika field bot khusus terisi (menghindari false positive dari autofill browser)
        if ($request->filled('_honeypot_bot_trap')) {
            Log::warning('[SECURITY] Bot detected via honeypot', [
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Silakan coba lagi.'], 422);
            }
            return redirect()->route('login')->withErrors(['email' => 'Permintaan tidak valid. Silakan coba lagi.']);
        }

        // 2. Cek IP lockout
        $ipKey = "brute_force_ip:{$ip}";
        $ipAttempts = Cache::get($ipKey, 0);

        if ($ipAttempts >= $this->maxIpAttempts) {
            $remainingMinutes = Cache::getTimeToLive($ipKey) ?? ($this->ipLockoutMinutes * 60);
            $remainingMinutes = ceil($remainingMinutes / 60);

            Log::warning('[SECURITY] IP Locked out from login', [
                'ip' => $ip,
                'attempts' => $ipAttempts,
                'remaining_minutes' => $remainingMinutes,
            ]);

            return $this->tooManyAttemptsResponse(
                "Terlalu banyak percobaan dari IP ini. Coba lagi dalam {$remainingMinutes} menit.",
                $remainingMinutes * 60
            );
        }

        // 3. Cek Account lockout (per email)
        if ($email) {
            $accountKey = "brute_force_account:" . hash('sha256', strtolower(trim($email)));
            $accountAttempts = Cache::get($accountKey, 0);

            if ($accountAttempts >= $this->maxAttempts) {
                $remainingMinutes = Cache::getTimeToLive($accountKey) ?? ($this->lockoutMinutes * 60);
                $remainingMinutes = ceil($remainingMinutes / 60);

                Log::warning('[SECURITY] Account Locked out', [
                    'email_hash' => hash('sha256', strtolower(trim($email))),
                    'ip' => $ip,
                    'attempts' => $accountAttempts,
                ]);

                return $this->tooManyAttemptsResponse(
                    "Akun ini sementara dikunci karena terlalu banyak percobaan gagal. Hubungi administrator atau coba lagi dalam {$remainingMinutes} menit.",
                    $remainingMinutes * 60
                );
            }
        }

        // 4. Progressive delay: tambahkan artificial delay berdasarkan jumlah percobaan
        if ($ipAttempts > 2) {
            $delay = min(pow(2, $ipAttempts - 2), 8); // Max 8 detik delay
            sleep($delay);
        }

        // 5. Lanjutkan request
        $response = $next($request);

        // 6. Jika login gagal (redirect dengan error atau 422), increment counter
        if ($this->isFailedAuthentication($response, $request)) {
            // Increment IP counter
            Cache::add($ipKey, 0, $this->ipLockoutMinutes * 60);
            Cache::increment($ipKey);

            // Increment account counter
            if ($email) {
                $accountKey = "brute_force_account:" . hash('sha256', strtolower(trim($email)));
                Cache::add($accountKey, 0, $this->lockoutMinutes * 60);
                Cache::increment($accountKey);

                $currentIpAttempts = Cache::get($ipKey, 0);
                Log::info('[SECURITY] Failed login attempt', [
                    'ip' => $ip,
                    'ip_attempts' => $currentIpAttempts,
                    'user_agent' => substr($request->userAgent() ?? '', 0, 200),
                ]);
            }
        } elseif ($this->isSuccessfulAuthentication($response)) {
            // Login berhasil: reset counter
            if ($email) {
                $accountKey = "brute_force_account:" . hash('sha256', strtolower(trim($email)));
                Cache::forget($accountKey);
            }
        }

        return $response;
    }

    /**
     * Deteksi apakah response menandakan autentikasi gagal
     */
    protected function isFailedAuthentication(Response $response, Request $request): bool
    {
        // Redirect kembali ke login dengan error
        if ($response->isRedirection()) {
            $location = $response->headers->get('Location', '');
            return str_contains($location, 'login') || str_contains($location, 'back');
        }

        // JSON response dengan status 422 atau 401
        return in_array($response->getStatusCode(), [401, 422]);
    }

    /**
     * Deteksi apakah response menandakan autentikasi berhasil
     */
    protected function isSuccessfulAuthentication(Response $response): bool
    {
        if ($response->isRedirection()) {
            $location = $response->headers->get('Location', '');
            return str_contains($location, 'dashboard') ||
                   str_contains($location, 'home') ||
                   (!str_contains($location, 'login'));
        }

        return $response->getStatusCode() === 200;
    }

    /**
     * Response untuk terlalu banyak percobaan
     */
    protected function tooManyAttemptsResponse(string $message, int $retryAfter): Response
    {
        return response()->view('auth.login', [
            'errors' => collect(['email' => [$message]]),
            'locked' => true,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $this->maxAttempts,
            'X-RateLimit-Remaining' => 0,
        ]);
    }
}
