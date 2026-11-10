<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Input Sanitization Middleware (Anti-XSS Layer)
 *
 * Membersihkan semua string input dari request sebelum diproses controller.
 * Mencegah Stored XSS dan Reflected XSS pada seluruh lapisan aplikasi.
 * OWASP: A03:2021 – Injection, A07:2021 – Cross-Site Scripting
 */
class SanitizeInputMiddleware
{
    /**
     * Kolom yang boleh mengandung HTML (dari WYSIWYG editor, sanitasi dilakukan terpisah)
     */
    protected array $richTextFields = [
        'description', 'content', 'body', 'message', 'about', 'bio',
        'footer_content', 'popup_content', 'lesson_content',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        $cleaned = $this->sanitize($input);
        $request->replace($cleaned);

        return $next($request);
    }

    /**
     * Rekursif membersihkan array input
     */
    protected function sanitize(array $data): array
    {
        $cleaned = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                // Kolom rich-text hanya dibersihkan dari tag berbahaya (script, event handlers)
                // Sanitasi HTML penuh dilakukan di controller dengan HTMLPurifier
                if (in_array(strtolower($key), $this->richTextFields)) {
                    $cleaned[$key] = $this->stripDangerousTags($value);
                } else {
                    // Input biasa: strip semua HTML tags dan normalize whitespace
                    $cleaned[$key] = $this->cleanString($value);
                }
            } else {
                $cleaned[$key] = $value;
            }
        }

        return $cleaned;
    }

    /**
     * Bersihkan string biasa dari semua HTML dan karakter berbahaya
     */
    protected function cleanString(string $value): string
    {
        // Hapus null bytes (path traversal prevention)
        $value = str_replace("\0", '', $value);

        // Strip semua HTML tags
        $value = strip_tags($value);

        // Decode HTML entities setelah strip untuk mencegah double encoding bypass
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Strip lagi setelah decode (mencegah encoded tag bypass)
        $value = strip_tags($value);

        // Hapus kontrol karakter berbahaya (non-printable)
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);

        return $value;
    }

    /**
     * Hapus hanya tag berbahaya dari rich text (script, event handlers, iframe)
     * untuk mencegah XSS sambil mempertahankan format HTML yang sah
     */
    protected function stripDangerousTags(string $value): string
    {
        // Hapus null bytes
        $value = str_replace("\0", '', $value);

        // Hapus script tags dan kontennya
        $value = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $value);

        // Hapus event handler attributes (onclick, onload, onerror, dll.)
        $value = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
        $value = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/i', '', $value);

        // Hapus javascript: protocol
        $value = preg_replace('/javascript\s*:/i', 'removed:', $value);

        // Hapus vbscript: dan data: URI schemes berbahaya
        $value = preg_replace('/vbscript\s*:/i', 'removed:', $value);
        $value = preg_replace('/data\s*:\s*text\/html/i', 'removed:', $value);

        // Hapus iframe, object, embed, dan form tags tersembunyi
        $value = preg_replace('/<\s*(iframe|object|embed|applet|meta|link)\b[^>]*>.*?<\/\1>/is', '', $value);
        $value = preg_replace('/<\s*(iframe|object|embed|applet|meta|link)\b[^>]*\/?>/i', '', $value);

        return $value;
    }
}
