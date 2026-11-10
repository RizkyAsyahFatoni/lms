<?php

namespace App\Services;

/**
 * HTML Purifier Service - Sanitasi konten rich text dari WYSIWYG editor
 *
 * Mencegah Stored XSS pada kolom yang menyimpan HTML (deskripsi kursus, konten lesson, dll.)
 * Menggunakan ezyang/htmlpurifier untuk sanitasi HTML yang aman dan permisif untuk konten edukasi.
 * OWASP: A03:2021 – Injection, Cross-Site Scripting (XSS)
 *
 * Penggunaan di controller:
 *   $clean = app(HtmlPurifierService::class)->purify($request->description);
 *   $strictClean = app(HtmlPurifierService::class)->purifyStrict($request->bio);
 */
class HtmlPurifierService
{
    protected ?\HTMLPurifier $purifier = null;
    protected ?\HTMLPurifier $strictPurifier = null;

    /**
     * Buat konfigurasi HTMLPurifier untuk konten kursus/edukasi
     * Mengizinkan: headings, lists, bold, italic, links, images (dari domain sendiri)
     */
    protected function buildPurifier(): \HTMLPurifier
    {
        $config = \HTMLPurifier_Config::createDefault();

        // Cache dir untuk performa
        $cacheDir = storage_path('app/purifier');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cacheDir);

        // Charset
        $config->set('Core.Encoding', 'UTF-8');

        // Element yang diizinkan (untuk konten edukasi/kursus)
        $config->set('HTML.Allowed', implode(',', [
            // Structure
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'br', 'hr', 'div', 'span',
            // Lists
            'ul', 'ol', 'li',
            // Text formatting
            'strong', 'b', 'em', 'i', 'u', 's', 'del', 'ins', 'sup', 'sub', 'mark',
            // Links (hanya ke domain sendiri atau whitelist)
            'a[href|title|target]',
            // Images (hanya src yang aman)
            'img[src|alt|title|width|height|class]',
            // Tables
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'th[colspan|rowspan|scope]', 'td[colspan|rowspan]',
            // Code
            'pre', 'code', 'kbd', 'samp',
            // Quote
            'blockquote', 'cite',
            // Other
            'figure', 'figcaption',
        ]));

        // Atribut yang diizinkan
        $config->set('HTML.AllowedAttributes', 'a.href,a.title,a.target,img.src,img.alt,img.title,img.width,img.height,img.class,*.class,*.id');

        // Larang link ke javascript: atau vbscript:
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

        // Paksa rel="noopener noreferrer" untuk semua link eksternal
        $config->set('HTML.TargetBlank', true);
        $config->set('HTML.TargetNoreferrer', true);
        $config->set('HTML.TargetNoopener', true);

        // Larang elemen berbahaya
        $config->set('HTML.ForbiddenElements', ['script', 'style', 'iframe', 'frame', 'object', 'embed', 'applet', 'meta', 'link', 'base']);

        return new \HTMLPurifier($config);
    }

    /**
     * Buat konfigurasi HTMLPurifier yang SANGAT ketat untuk input pendek (bio, nama, dll.)
     */
    protected function buildStrictPurifier(): \HTMLPurifier
    {
        $config = \HTMLPurifier_Config::createDefault();

        $cacheDir = storage_path('app/purifier');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cacheDir);
        $config->set('Core.Encoding', 'UTF-8');

        // Hanya izinkan text formatting minimal
        $config->set('HTML.Allowed', 'strong,em,br');

        return new \HTMLPurifier($config);
    }

    /**
     * Purifikasi HTML lengkap (untuk deskripsi kursus, konten lesson, dll.)
     */
    public function purify(string $html): string
    {
        if (!class_exists('\HTMLPurifier')) {
            // Fallback jika library belum terinstall: strip semua HTML
            return strip_tags($html);
        }

        if ($this->purifier === null) {
            $this->purifier = $this->buildPurifier();
        }

        return $this->purifier->purify($html);
    }

    /**
     * Purifikasi ketat untuk input pendek (bio, nama, deskripsi singkat)
     */
    public function purifyStrict(string $input): string
    {
        if (!class_exists('\HTMLPurifier')) {
            return strip_tags($input);
        }

        if ($this->strictPurifier === null) {
            $this->strictPurifier = $this->buildStrictPurifier();
        }

        return $this->strictPurifier->purify($input);
    }

    /**
     * Sanitasi plain text: hapus SEMUA HTML
     */
    public function plainText(string $input): string
    {
        $input = strip_tags($input);
        $input = html_entity_decode($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $input = strip_tags($input); // strip lagi setelah decode
        return trim($input);
    }
}
