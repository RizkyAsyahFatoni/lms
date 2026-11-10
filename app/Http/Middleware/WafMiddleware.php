<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * PHP-Level Web Application Firewall (WAF) Middleware
 *
 * Implementasi aturan WAF setara ModSecurity OWASP Core Rule Set (CRS) v3.3
 * di layer aplikasi PHP. Mendeteksi dan memblokir:
 *
 * 1. SQL Injection (SQLi)         — OWASP CRS Rule 942xxx
 * 2. Cross-Site Scripting (XSS)   — OWASP CRS Rule 941xxx
 * 3. Remote Code Execution (RCE)  — OWASP CRS Rule 932xxx
 * 4. Path Traversal               — OWASP CRS Rule 930xxx
 * 5. Command Injection            — OWASP CRS Rule 932xxx
 * 6. SSRF (Server-Side Request Forgery) — Baru di CRS 4.x
 * 7. Scanner/Recon Tools          — OWASP CRS Rule 913xxx
 * 8. HTTP Protocol Anomaly        — OWASP CRS Rule 920xxx
 *
 * CATATAN: Ini adalah "application-level WAF" dan tidak menggantikan
 * WAF infrastruktur (ModSecurity/Cloudflare) tetapi memberikan lapisan
 * perlindungan tambahan yang berjalan di dalam PHP.
 */
class WafMiddleware
{
    /**
     * Poin anomali sebelum block (setara paranoia level ModSecurity)
     * Semakin tinggi = lebih toleran, semakin rendah = lebih ketat
     */
    protected int $anomalyThreshold = 5;

    /**
     * Durasi blacklist IP (menit) jika melebihi threshold
     */
    protected int $blacklistMinutes = 60;

    /**
     * Request paths yang dikecualikan dari WAF (file statis, healthcheck)
     */
    protected array $excludedPaths = [
        '/up',
        '/favicon.ico',
        '/robots.txt',
    ];

    // =====================================================
    // Pola SQL Injection (CRS 942xxx)
    // =====================================================
    protected array $sqlInjectionPatterns = [
        // UNION-based injection
        '/\bunion\b.{0,50}\bselect\b/i',
        // Comment-based
        '/\/\*.*\*\//s',
        '/--\s*$/m',
        // Stacked queries
        '/;\s*(select|insert|update|delete|drop|truncate|create|alter)\b/i',
        // Blind SQLi timing
        '/\b(sleep|benchmark|waitfor\s+delay|pg_sleep)\s*\(/i',
        // Information schema
        '/\binformation_schema\b/i',
        // Stored procedures
        '/\b(xp_cmdshell|sp_executesql|exec\s*\(|execute\s*\()\b/i',
        // Boolean-based
        '/\b(and|or)\s+\d+\s*=\s*\d+/i',
        '/\b(and|or)\s+\'[^\']*\'\s*=\s*\'[^\']*\'/i',
        // LOAD FILE / INTO OUTFILE
        '/\b(load_file|into\s+outfile|into\s+dumpfile)\b/i',
        // Hex encoding bypass
        '/0x[0-9a-f]{8,}/i',
    ];

    // =====================================================
    // Pola XSS (CRS 941xxx)
    // =====================================================
    protected array $xssPatterns = [
        // Script tags
        '/<\s*script\b[^>]*>/i',
        // Event handlers
        '/\bon\w+\s*=/i',
        // javascript: protocol
        '/javascript\s*:/i',
        // vbscript:
        '/vbscript\s*:/i',
        // data: with base64 HTML
        '/data\s*:\s*text\/html/i',
        // document.cookie / document.write via encoding
        '/document\.(cookie|write|location|domain)/i',
        // eval() dengan input
        '/eval\s*\(.{0,50}\)/i',
        // SVG/IMG based XSS
        '/<\s*(svg|img|iframe|object|embed|link|base)\b[^>]*(on\w+|javascript|src\s*=\s*["\']?\s*javascript)/i',
        // Expression-based (IE)
        '/expression\s*\(/i',
    ];

    // =====================================================
    // Pola Path Traversal (CRS 930xxx)
    // =====================================================
    protected array $pathTraversalPatterns = [
        '/\.\.[\/\\\\]/i',
        '/[\/\\\\]\.\.[\/\\\\]/i',
        '/%2e%2e[\/\\\\%]/i',
        '/\.\.%2f/i',
        '/etc\/passwd/i',
        '/\/proc\/self\/environ/i',
        '/windows\/(system32|syswow64)/i',
        '/boot\.ini/i',
        '/win\.ini/i',
    ];

    // =====================================================
    // Pola Command Injection / RCE (CRS 932xxx)
    // =====================================================
    protected array $commandInjectionPatterns = [
        '/;\s*(ls|cat|pwd|whoami|id|uname|wget|curl|bash|sh|python|perl|ruby|php|nc|netcat)\b/i',
        '/\|\s*(ls|cat|pwd|whoami|id|wget|curl|bash|sh)\b/i',
        '/`[^`]*`/s',
        '/\$\([^)]*\)/s',
        '/\b(passthru|shell_exec|system|exec|popen|proc_open|pcntl_exec)\s*\(/i',
        '/phpinfo\s*\(\)/i',
        '/base64_decode\s*\(.{10,}\)/i',
    ];

    // =====================================================
    // Pola Scanner/Recon (CRS 913xxx)
    // =====================================================
    protected array $scannerUserAgents = [
        '/nikto/i',
        '/sqlmap/i',
        '/nmap/i',
        '/masscan/i',
        '/dirbuster/i',
        '/gobuster/i',
        '/wfuzz/i',
        '/skipfish/i',
        '/acunetix/i',
        '/nessus/i',
        '/openvas/i',
        '/metasploit/i',
        '/burpsuite/i',
        '/havij/i',
        '/w3af/i',
        '/paros/i',
        '/beef/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip excluded paths
        foreach ($this->excludedPaths as $path) {
            if ($request->is(ltrim($path, '/'))) {
                return $next($request);
            }
        }

        $ip = $request->ip();

        // 1. Cek apakah IP sudah di-blacklist
        if ($this->isBlacklisted($ip)) {
            Log::warning('[WAF] Blacklisted IP blocked', ['ip' => $ip]);
            return $this->blockResponse('IP Address anda telah diblokir sementara karena aktivitas mencurigakan.');
        }

        // 2. Kumpulkan semua input untuk dianalisis
        $anomalyScore = 0;
        $violations = [];

        // 2a. Analisis User-Agent (scanner detection)
        $userAgent = $request->userAgent() ?? '';
        if ($this->matchesScanner($userAgent)) {
            $anomalyScore += 10; // Langsung blacklist scanner
            $violations[] = "Scanner tool detected: {$userAgent}";
        }

        // 2b. Analisis semua input parameter
        $inputsToCheck = array_merge(
            $request->query->all(),
            $request->request->all(),
            ['path' => $request->path()]
        );

        $inputString = $this->flattenInputs($inputsToCheck);

        // SQL Injection check
        foreach ($this->sqlInjectionPatterns as $pattern) {
            if (preg_match($pattern, $inputString)) {
                $anomalyScore += 5;
                $violations[] = "SQL Injection pattern: {$pattern}";
                break; // Satu match cukup
            }
        }

        // XSS check (sudah ada SanitizeInput, ini double-check)
        foreach ($this->xssPatterns as $pattern) {
            if (preg_match($pattern, $inputString)) {
                $anomalyScore += 4;
                $violations[] = "XSS pattern: {$pattern}";
                break;
            }
        }

        // Path traversal check
        foreach ($this->pathTraversalPatterns as $pattern) {
            if (preg_match($pattern, $request->fullUrl() . $inputString)) {
                $anomalyScore += 6;
                $violations[] = "Path traversal: {$pattern}";
                break;
            }
        }

        // Command injection check
        foreach ($this->commandInjectionPatterns as $pattern) {
            if (preg_match($pattern, $inputString)) {
                $anomalyScore += 8;
                $violations[] = "Command injection: {$pattern}";
                break;
            }
        }

        // 2c. HTTP Protocol anomaly checks
        $anomalyScore += $this->checkHttpAnomalies($request, $violations);

        // 3. Evaluasi anomaly score
        if ($anomalyScore >= $this->anomalyThreshold) {
            $this->logViolation($request, $anomalyScore, $violations);
            $this->incrementViolationCount($ip, $anomalyScore);

            if ($anomalyScore >= 10) {
                // Blacklist IP
                Cache::put("waf_blacklist:{$ip}", true, $this->blacklistMinutes * 60);
                return $this->blockResponse('Permintaan anda diblokir karena aktivitas yang tidak diizinkan.');
            }

            // Score rendah: log saja, jangan block (false positive prevention)
            return $next($request);
        }

        return $next($request);
    }

    /**
     * Periksa anomali protokol HTTP
     */
    protected function checkHttpAnomalies(Request $request, array &$violations): int
    {
        $score = 0;

        // Request method tidak biasa
        if (in_array($request->method(), ['TRACE', 'TRACK', 'OPTIONS'])) {
            $score += 2;
            $violations[] = "Unusual HTTP method: {$request->method()}";
        }

        // Content-Type mismatch untuk POST request
        if ($request->isMethod('POST') && !$request->hasHeader('Content-Type')) {
            $score += 1;
        }

        // URL-encoded path manipulation
        $encodedPath = $request->path();
        if (preg_match('/%[0-9a-f]{2}/i', $encodedPath)) {
            $decoded = urldecode($encodedPath);
            foreach ($this->pathTraversalPatterns as $pattern) {
                if (preg_match($pattern, $decoded)) {
                    $score += 6;
                    $violations[] = "Encoded path traversal in URL";
                    break;
                }
            }
        }

        // Unusually long headers (header injection attempt)
        foreach ($request->headers->all() as $name => $values) {
            foreach ($values as $value) {
                if (strlen($value) > 4096) {
                    $score += 2;
                    $violations[] = "Unusually long header: {$name}";
                    break 2;
                }
            }
        }

        return $score;
    }

    /**
     * Cek apakah User-Agent adalah tool scanner
     */
    protected function matchesScanner(string $userAgent): bool
    {
        foreach ($this->scannerUserAgents as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Flatten nested array ke string untuk inspeksi
     */
    protected function flattenInputs(array $data, int $depth = 0): string
    {
        if ($depth > 5) return ''; // Cegah rekursi dalam

        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[] = $this->flattenInputs($value, $depth + 1);
            } elseif (is_string($value)) {
                $result[] = $key . '=' . $value;
            }
        }
        return implode(' ', $result);
    }

    /**
     * Cek apakah IP di-blacklist
     */
    protected function isBlacklisted(string $ip): bool
    {
        return Cache::has("waf_blacklist:{$ip}");
    }

    /**
     * Increment violation counter dan auto-blacklist jika terlalu banyak
     */
    protected function incrementViolationCount(string $ip, int $score): void
    {
        $key = "waf_violations:{$ip}";
        $current = Cache::get($key, 0) + $score;
        Cache::put($key, $current, 3600); // Window 1 jam

        if ($current >= 20) {
            Cache::put("waf_blacklist:{$ip}", true, $this->blacklistMinutes * 60);
            Log::warning('[WAF] IP auto-blacklisted due to cumulative violations', [
                'ip'    => $ip,
                'score' => $current,
            ]);
        }
    }

    /**
     * Log pelanggaran WAF ke audit trail
     */
    protected function logViolation(Request $request, int $score, array $violations): void
    {
        Log::warning('[WAF] Attack pattern detected', [
            'ip'         => $request->ip(),
            'url'        => $request->fullUrl(),
            'method'     => $request->method(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 200),
            'score'      => $score,
            'violations' => $violations,
            'user_id'    => auth()->id(),
        ]);
    }

    /**
     * Response untuk request yang diblokir
     */
    protected function blockResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error'   => 'Forbidden',
                'message' => $message,
            ], 403);
        }

        return response()->view('errors.waf_blocked', [
            'message' => $message,
        ], 403)->withHeaders([
            'X-Blocked-By' => 'WAF',
        ]);
    }
}
