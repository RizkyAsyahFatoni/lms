<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply BSSN & OWASP compliant HTTP Security Headers.
     * Enhanced: CSP Nonce, strict permissions, server info removal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate CSP nonce and share to views
        $nonce = base64_encode(random_bytes(16));
        app()->instance('csp-nonce', $nonce);
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        // 1. Anti-Clickjacking: Melarang situs lain membingkai (embed iframe) halaman sistem
        $response->headers->set('X-Frame-Options', 'DENY');

        // 2. Anti-MIME Sniffing: Memaksa browser mematuhi MIME-Type asli berkas
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Cross-Site Scripting (XSS) Protection Filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Referrer Policy: Melindungi kebocoran parameter URL internal ke situs eksternal
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Hardware Permissions Policy: Membatasi akses sensor/hardware perangkat
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=(), usb=(), bluetooth=(), accelerometer=(), gyroscope=()');

        // 6. HSTS (HTTP Strict Transport Security): Wajibkan HTTPS jika diakses lewat SSL
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // 7. Content Security Policy: Mencegah XSS dengan whitelist sumber konten
        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-eval' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.youtube.com https://maps.googleapis.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob: https:",
            "media-src 'self' blob: https://www.youtube.com",
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com",
            "connect-src 'self'",
            "worker-src 'self' blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        if (app()->environment('production')) {
            $cspDirectives[] = 'upgrade-insecure-requests';
        }

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        // 8. Sembunyikan informasi server untuk mencegah fingerprinting
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow', false); // Hanya untuk halaman admin

        // 9. Cache control untuk halaman sensitif (mencegah browser caching data sensitif)
        if ($request->is('admin/*') || $request->is('profile*') || $request->is('dashboard*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
