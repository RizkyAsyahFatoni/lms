<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware — Memeriksa apakah user memiliki salah satu dari role yang diizinkan.
 *
 * Penggunaan di routes:
 *   ->middleware('role:super_admin')
 *   ->middleware('role:super_admin,admin_opd')
 *
 * Role yang valid (sesuai KAK):
 *   super_admin  / admin / bkpsdm  → Super Admin / BKPSDM (pengelolaan keseluruhan)
 *   admin_opd                      → Admin OPD (setelah approval BKPSDM)
 *   asn_learner  / student         → ASN / Peserta belajar
 *   narasumber   / instructor      → Narasumber / Instruktur
 *   pimpinan                       → Pimpinan (read-only dashboard)
 *   reviewer_talent                → Reviewer / Admin Talent
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk mengakses halaman ini.');
        }

        $user = auth()->user();

        // Cek akun aktif
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        // Normalisasi role aliases
        $userRole = match($user->role) {
            'admin', 'bkpsdm'   => 'super_admin',
            'student'           => 'asn_learner',
            'instructor'        => 'narasumber',
            default             => $user->role,
        };

        // Normalisasi allowed roles yang diterima parameter middleware
        $allowedNormalized = array_map(fn($r) => match(trim($r)) {
            'admin', 'bkpsdm'   => 'super_admin',
            'student'           => 'asn_learner',
            'instructor'        => 'narasumber',
            default             => trim($r),
        }, $roles);

        if (in_array($userRole, $allowedNormalized)) {
            return $next($request);
        }

        // Jika request AJAX/API → return JSON
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.'], 403);
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
    }
}
