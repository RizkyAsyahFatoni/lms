<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckActive — Memastikan akun yang login berstatus aktif (is_active = true).
 * Middleware ini diaplikasikan secara global ke semua route yang membutuhkan auth.
 */
class CheckActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi BKPSDM untuk informasi lebih lanjut.');
        }

        return $next($request);
    }
}
