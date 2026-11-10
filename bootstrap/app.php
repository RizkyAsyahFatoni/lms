<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.apikey'        => \App\Http\Middleware\AuthenticateApiKey::class,
            'role'               => \App\Http\Middleware\RoleMiddleware::class,
            'check_active'       => \App\Http\Middleware\CheckActive::class,
            'brute.force'        => \App\Http\Middleware\BruteForceProtectionMiddleware::class,
            'admin.access'       => \App\Http\Middleware\AdminAccessMiddleware::class,
            'waf'                => \App\Http\Middleware\WafMiddleware::class,
        ]);
        // WAF sebagai lapisan pertama (sebelum semua middleware lain)
        $middleware->prependToGroup('web', \App\Http\Middleware\WafMiddleware::class);
        $middleware->prependToGroup('api', \App\Http\Middleware\WafMiddleware::class);
        // Terapkan CheckActive ke semua web request yang sudah login
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckActive::class);
        // Terapkan input sanitization ke semua web request (anti-XSS di layer request)
        $middleware->appendToGroup('web', \App\Http\Middleware\SanitizeInputMiddleware::class);
        // Terapkan BSSN & OWASP HTTP Security Headers ke seluruh request
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
