<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS KAB Berau') }}</title>
    <meta name="description" content="Platform pengembangan kompetensi ASN, PPPK, dan aparatur Pemerintah Kabupaten Berau.">
    <script>
        // Redirect ke halaman utama — welcome.blade.php tidak digunakan sebagai halaman publik
        window.location.replace('/home');
    </script>
    <noscript>
        <meta http-equiv="refresh" content="0;url=/home">
    </noscript>
</head>
<body>
    <p style="font-family:sans-serif; padding:2rem; color:#475569;">
        Mengalihkan ke halaman utama...
        <a href="/home" style="color:#0d9488;">Klik di sini jika tidak dialihkan otomatis.</a>
    </p>
</body>
</html>
