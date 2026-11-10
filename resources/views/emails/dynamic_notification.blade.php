<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #0e6c84 0%, #153c48 100%);
            color: #ffffff;
            padding: 24px 30px;
            text-align: left;
        }
        .brand-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 99px;
            margin-bottom: 8px;
        }
        .app-name {
            font-size: 18px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.3px;
        }
        .sub-header {
            font-size: 11px;
            opacity: 0.85;
            margin: 2px 0 0 0;
        }
        .content {
            padding: 30px;
            font-size: 14px;
            color: #334155;
        }
        .content p {
            margin: 0 0 14px 0;
        }
        .content strong {
            color: #0f172a;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            font-size: 11px;
            color: #64748b;
            text-align: center;
            line-height: 1.5;
        }
        .footer p {
            margin: 0 0 4px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Kedinasan -->
        <div class="header">
            <span class="brand-badge">Sistem Notifikasi Resmi</span>
            <h1 class="app-name">LMS Pemerintah Kabupaten Berau</h1>
            <p class="sub-header">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM)</p>
        </div>

        <!-- Konten Surat Notifikasi Dinamis -->
        <div class="content">
            {!! $bodyHtml !!}
        </div>

        <!-- Footer Otomatis Kedinasan -->
        <div class="footer">
            <p><strong>Pemberitahuan Otomatis Sistem Pembelajaran Mandiri & Corporate University</strong></p>
            <p>Pemerintah Kabupaten Berau &bull; BKPSDM &bull; Hak Cipta &copy; {{ date('Y') }}. Semua Hak Dilindungi.</p>
            <p style="font-size: 10px; color: #94a3b8; margin-top: 8px;">
                Email ini dikirimkan secara otomatis oleh sistem notifikasi SPBE LMS KAB Berau. Mohon untuk tidak membalas langsung ke alamat email ini (*no-reply*).
            </p>
        </div>
    </div>
</body>
</html>

