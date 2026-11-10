<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Diblokir — LMS KAB Berau</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 1.5rem;
            padding: 3rem;
            max-width: 480px;
            width: 90%;
            text-align: center;
            backdrop-filter: blur(12px);
        }
        .icon {
            width: 72px; height: 72px;
            background: rgba(239,68,68,0.15);
            border: 2px solid rgba(239,68,68,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon svg { color: #ef4444; }
        h1 { color: #f1f5f9; font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem; }
        p { color: #94a3b8; font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.5rem; }
        .code { color: #ef4444; font-family: monospace; font-size: 0.75rem;
                background: rgba(239,68,68,0.1); padding: 0.25rem 0.75rem; border-radius: 9999px;
                display: inline-block; margin-bottom: 2rem; }
        .detail {
            background: rgba(0,0,0,0.2); border-radius: 0.75rem;
            padding: 1rem; margin-bottom: 2rem; text-align: left;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .detail p { font-size: 0.75rem; color: #64748b; margin: 0; }
        .detail code { color: #f87171; font-size: 0.7rem; }
        a.btn {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white; text-decoration: none;
            padding: 0.75rem 2rem; border-radius: 0.75rem;
            font-size: 0.875rem; font-weight: 600;
            transition: opacity 0.2s;
        }
        a.btn:hover { opacity: 0.85; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>

        <h1>Akses Diblokir oleh Sistem Keamanan</h1>
        <span class="code">HTTP 403 — WAF BLOCKED</span>

        <p>{{ $message ?? 'Permintaan anda mengandung pola yang mencurigakan dan diblokir oleh sistem keamanan kami.' }}</p>

        <div class="detail">
            <p>Jika anda merasa ini adalah kesalahan, hubungi administrator sistem:</p>
            <code>Waktu: {{ now()->format('d/m/Y H:i:s') }} WIB</code>
        </div>

        <a href="{{ url('/') }}" class="btn">← Kembali ke Beranda</a>
    </div>
</body>
</html>
