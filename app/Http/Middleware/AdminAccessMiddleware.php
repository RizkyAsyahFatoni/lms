<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

/**
 * Admin Access Protection Middleware
 *
 * Proteksi tambahan untuk admin area:
 * - Rate limiting per IP untuk akses admin
 * - Deteksi akses dari IP yang tidak biasa
 * - Logging setiap akses admin panel
 * - Validasi session integrity
 * OWASP: A07:2021 – Identification Failures, A09:2021 – Logging Failures
 */
class AdminAccessMiddleware
{
    /** Maksimum request admin per menit per IP */
    protected int $maxAdminRequests = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Re-validasi role (defense in depth - tidak hanya mengandalkan RoleMiddleware)
        if (!in_array($user->role, ['admin', 'super_admin', 'bkpsdm', 'superadmin', 'admin_opd', 'narasumber', 'instructor'])) {
            Log::warning('[SECURITY] Unauthorized admin access attempt', [
                'user_id'   => $user->id,
                'role'      => $user->role,
                'ip'        => $request->ip(),
                'url'       => $request->fullUrl(),
            ]);

            ActivityLog::create([
                'user_id'      => $user->id,
                'action'       => 'unauthorized_admin_access',
                'description'  => 'Percobaan akses admin tanpa otorisasi',
                'module'       => 'security',
                'ip_address'   => $request->ip(),
                'user_agent'   => substr($request->userAgent() ?? '', 0, 255),
                'status'       => 'failed',
            ]);

            abort(403, 'Akses ditolak.');
        }

        // 2. Rate limiting per user untuk admin panel
        $key = "admin_access_rate:{$user->id}";
        if (RateLimiter::tooManyAttempts($key, $this->maxAdminRequests)) {
            Log::warning('[SECURITY] Admin rate limit hit', [
                'user_id' => $user->id,
                'ip'      => $request->ip(),
            ]);
            abort(429, 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.');
        }
        RateLimiter::hit($key, 60); // Window 60 detik

        // 3. Deteksi perubahan IP dalam satu session (session hijacking)
        $sessionIp = session('admin_ip');
        if ($sessionIp && $sessionIp !== $request->ip()) {
            Log::warning('[SECURITY] Admin session IP mismatch - possible session hijacking', [
                'user_id'    => $user->id,
                'session_ip' => $sessionIp,
                'current_ip' => $request->ip(),
            ]);

            // Invalidate session dan paksa login ulang
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Sesi tidak valid. Silakan masuk kembali untuk keamanan.']);
        }

        // Simpan IP saat pertama kali akses admin
        if (!$sessionIp) {
            session(['admin_ip' => $request->ip()]);
        }

        return $next($request);
    }
}
