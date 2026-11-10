<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Resmi: {{ $certificate->certificate_number }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans, Playfair Display, Great Vibes, Cinzel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .cert-canvas { box-shadow: none !important; margin: 0 !important; width: 100% !important; }
        }
    </style>
</head>
@php
    $course = $certificate->course;
    $isCustomBg = ($course->cert_template_mode ?? 'default') === 'custom_bg' && !empty($course->cert_bg_image);
    $bgImage = $course->cert_bg_image ?: asset('uploads/certificates/blank_certificate_gold_navy.png');
    
    $theme = $course->cert_theme_color ?: 'teal';
    $borderClass = match($theme) {
        'indigo' => 'border-indigo-800/85',
        'blue' => 'border-blue-800/85',
        'emerald' => 'border-emerald-800/85',
        'slate' => 'border-slate-800/85',
        default => 'border-teal-700/85',
    };
    $textAccentClass = match($theme) {
        'indigo' => 'text-indigo-800',
        'blue' => 'text-blue-800',
        'emerald' => 'text-emerald-800',
        'slate' => 'text-slate-800',
        default => 'text-teal-800',
    };
    $sealClass = match($theme) {
        'indigo' => 'border-indigo-600 bg-indigo-50 text-indigo-800',
        'blue' => 'border-blue-600 bg-blue-50 text-blue-800',
        'emerald' => 'border-emerald-600 bg-emerald-50 text-emerald-800',
        'slate' => 'border-slate-700 bg-slate-100 text-slate-800',
        default => 'border-teal-600 bg-teal-50 text-teal-800',
    };

    $fontFamilyClass = match($course->cert_font_family ?? 'playfair') {
        'great_vibes' => "font-['Great_Vibes'] text-4xl sm:text-5xl",
        'cinzel' => "font-['Cinzel'] tracking-wider font-bold",
        'jakarta' => "font-sans font-extrabold tracking-tight",
        default => "font-serif italic font-extrabold",
    };

    $fontSizeClass = match($course->cert_font_size ?? 'large') {
        'normal' => 'text-2xl sm:text-3xl',
        'xlarge' => 'text-4xl sm:text-5xl',
        default => 'text-3xl sm:text-4xl',
    };
    if (($course->cert_font_family ?? 'playfair') === 'great_vibes') {
        $fontSizeClass = 'text-4xl sm:text-5xl';
    }

    $nameColor = $course->cert_name_color ?: '#0f172a';
    $verticalOffset = $course->cert_name_vertical_pos ?? 46;

    $defaultLayout = [
        'header' => ['x' => 50, 'y' => 14, 'fontFamily' => 'jakarta', 'fontSize' => 11, 'color' => '#0f172a'],
        'title' => ['x' => 50, 'y' => 27, 'fontFamily' => 'cinzel', 'fontSize' => 12, 'color' => '#0f766e'],
        'recipient' => ['x' => 50, 'y' => 48, 'fontFamily' => $course->cert_font_family ?: 'playfair', 'fontSize' => 20, 'color' => $course->cert_name_color ?: '#0f172a'],
        'course' => ['x' => 50, 'y' => 66, 'fontFamily' => 'jakarta', 'fontSize' => 9, 'color' => '#1e293b'],
        'signature' => ['x' => 80, 'y' => 85, 'fontFamily' => 'playfair', 'fontSize' => 10, 'color' => '#0f172a'],
        'seal' => ['x' => 50, 'y' => 85, 'fontFamily' => 'jakarta', 'fontSize' => 6, 'color' => '#78350f'],
        'qr' => ['x' => 20, 'y' => 85, 'fontFamily' => 'jakarta', 'fontSize' => 7, 'color' => '#64748b'],
    ];
    $layout = $defaultLayout;
    if (is_array($course->cert_elements_layout)) {
        foreach ($defaultLayout as $k => $def) {
            if (isset($course->cert_elements_layout[$k]) && is_array($course->cert_elements_layout[$k])) {
                $layout[$k] = array_merge($def, $course->cert_elements_layout[$k]);
            }
        }
    }
    $showHeader = $course->cert_show_header ?? true;

    $getFontFamilyCss = function($fontKey) {
        return match($fontKey ?? 'jakarta') {
            'playfair' => "'Playfair Display', Georgia, serif",
            'great_vibes' => "'Great Vibes', cursive",
            'cinzel' => "'Cinzel', serif",
            'jakarta' => "'Plus Jakarta Sans', sans-serif",
            'montserrat' => "'Montserrat', sans-serif",
            'serif' => "Georgia, Cambria, 'Times New Roman', serif",
            'sans' => "system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif",
            'mono' => "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace",
            default => "'Plus Jakarta Sans', sans-serif",
        };
    };
@endphp
<body class="bg-slate-100 min-h-screen py-8 px-4 sm:px-6 font-sans">

    <!-- Top Action Bar (No-Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl shadow-sm border border-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Dashboard</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Terverifikasi Sah & Valid
            </span>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Certificate Canvas Frame -->
    <div class="max-w-4xl mx-auto rounded-3xl shadow-2xl relative overflow-hidden cert-canvas {{ $isCustomBg ? 'bg-white' : 'bg-white border-8 border-double ' . $borderClass }}"
         style="aspect-ratio: 1.414 / 1; min-height: 580px;">
        
        @if($isCustomBg)
            <!-- Custom Blank Background Image Template -->
            <div class="absolute inset-0 w-full h-full bg-cover bg-center pointer-events-none"
                 style="background-image: url('{{ $bgImage }}'); background-size: 100% 100%;">
            </div>
        @else
            <!-- Corner Ornaments (Vector Frame) -->
            <div class="absolute top-0 left-0 w-28 h-28 bg-gradient-to-br from-teal-500/10 to-transparent pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-28 h-28 bg-gradient-to-tl from-teal-500/10 to-transparent pointer-events-none"></div>
        @endif

        <!-- Content Overlay Container (Relative container for absolute positioned certificate elements) -->
        <div class="relative z-10 w-full h-full {{ $isCustomBg ? 'bg-black/[0.01]' : '' }}">
            
            <!-- 1. Top Header & Institution -->
            @if($showHeader)
                <div style="position: absolute; left: {{ $layout['header']['x'] }}%; top: {{ $layout['header']['y'] }}%; transform: translate(-50%, -50%); width: 85%; max-width: 650px;" class="text-center space-y-1.5 pointer-events-none">
                    <div class="flex items-center justify-center gap-3">
                        @if($course->cert_logo_url)
                            <img src="{{ $course->cert_logo_url }}" class="h-10 max-w-[140px] object-contain rounded drop-shadow-xs" alt="Logo Lembaga">
                        @else
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white font-extrabold text-lg flex items-center justify-center shadow-sm">
                                OL
                            </div>
                        @endif
                        <div class="text-left">
                            <h2 class="font-extrabold uppercase tracking-tight leading-tight"
                                style="font-family: {{ $getFontFamilyCss($layout['header']['fontFamily'] ?? 'jakarta') }}; font-size: {{ ($layout['header']['fontSize'] ?? 11) * 1.4 }}px; color: {{ $layout['header']['color'] ?? '#0f172a' }};">
                                {{ $course->cert_institution_name ?: 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA' }}
                            </h2>
                            <p class="uppercase tracking-widest font-semibold"
                               style="font-family: {{ $getFontFamilyCss($layout['header']['fontFamily'] ?? 'jakarta') }}; font-size: {{ max(8, ($layout['header']['fontSize'] ?? 11) * 1.0) }}px; color: {{ $layout['header']['color'] ?? '#0f172a' }}; opacity: 0.75;">
                                {{ $course->cert_institution_sub ?: 'Pemerintah Kabupaten Berau' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. Document Title & Registration Number -->
            <div style="position: absolute; left: {{ $layout['title']['x'] }}%; top: {{ $layout['title']['y'] }}%; transform: translate(-50%, -50%); width: 85%; max-width: 650px;" class="text-center space-y-0.5 pointer-events-none">
                <p class="font-black uppercase tracking-[0.25em]"
                   style="font-family: {{ $getFontFamilyCss($layout['title']['fontFamily'] ?? 'cinzel') }}; font-size: {{ ($layout['title']['fontSize'] ?? 12) * 1.4 }}px; color: {{ $layout['title']['color'] ?? '#0f766e' }};">
                    {{ $course->cert_title ?: 'SERTIFIKAT KOMPETENSI RESMI' }}
                </p>
                @if($course->cert_show_number ?? true)
                    <p class="font-mono"
                       style="font-size: {{ max(9, ($layout['title']['fontSize'] ?? 12) * 0.95) }}px; color: {{ $layout['title']['color'] ?? '#0f766e' }}; opacity: 0.7;">
                        Nomor Registrasi: {{ $certificate->certificate_number }}
                    </p>
                @endif
            </div>

            <!-- 3. Recipient Name -->
            <div style="position: absolute; left: {{ $layout['recipient']['x'] }}%; top: {{ $layout['recipient']['y'] }}%; transform: translate(-50%, -50%); width: 85%; max-width: 700px;" class="text-center space-y-1 pointer-events-none">
                <p class="text-slate-500" style="font-size: {{ max(9, ($layout['recipient']['fontSize'] ?? 20) * 0.55) }}px;">Diberikan dengan bangga dan apresiasi setinggi-tingginya kepada:</p>
                <div class="py-1">
                    <h1 class="tracking-tight border-b inline-block pb-1 px-8 font-bold"
                        style="color: {{ $layout['recipient']['color'] ?? $nameColor }}; border-color: {{ $layout['recipient']['color'] ?? $nameColor }}40; font-family: {{ $getFontFamilyCss($layout['recipient']['fontFamily'] ?? 'playfair') }}; font-size: {{ ($layout['recipient']['fontSize'] ?? 20) * 1.5 }}px;">
                        {{ $certificate->user->name }}
                    </h1>
                </div>
            </div>

            <!-- 4. Course Information & Statement -->
            <div style="position: absolute; left: {{ $layout['course']['x'] }}%; top: {{ $layout['course']['y'] }}%; transform: translate(-50%, -50%); width: 85%; max-width: 650px;" class="text-center space-y-1.5 pointer-events-none">
                <p class="max-w-xl mx-auto leading-relaxed"
                   style="font-family: {{ $getFontFamilyCss($layout['course']['fontFamily'] ?? 'jakarta') }}; font-size: {{ max(9, ($layout['course']['fontSize'] ?? 9) * 1.25) }}px; color: {{ $layout['course']['color'] ?? '#1e293b' }}; opacity: 0.85;">
                    {{ $course->cert_statement ?: 'Telah menyelesaikan seluruh rangkaian silabus, pengujian formatif, dan tugas evaluasi mandiri pada program pelatihan:' }}
                </p>
                <div class="max-w-xl mx-auto bg-slate-50/95 border border-slate-200/90 rounded-2xl p-2.5 sm:p-3 shadow-sm">
                    <h3 class="font-extrabold leading-snug"
                        style="font-family: {{ $getFontFamilyCss($layout['course']['fontFamily'] ?? 'jakarta') }}; font-size: {{ ($layout['course']['fontSize'] ?? 9) * 1.4 }}px; color: {{ $layout['course']['color'] ?? '#0f172a' }};">
                        {{ $course->title }}
                    </h3>
                    <div class="flex items-center justify-center gap-3 font-semibold mt-1"
                         style="font-size: {{ max(8, ($layout['course']['fontSize'] ?? 9) * 1.1) }}px; color: {{ $layout['course']['color'] ?? '#0f172a' }}; opacity: 0.9;">
                        <span>Kategori: {{ $course->category->name }}</span>
                        @if($course->cert_show_jp ?? true)
                            <span>•</span>
                            <span class="font-bold font-mono">Bobot: {{ $course->jp ?? 2 }} Jam Pelajaran (JP)</span>
                        @endif
                        <span>•</span>
                        <span>Durasi: {{ $course->duration_hours }} Jam Belajar</span>
                    </div>
                </div>
            </div>

            <!-- 5. Verification Hash & QR Code -->
            <div style="position: absolute; left: {{ $layout['qr']['x'] }}%; top: {{ $layout['qr']['y'] }}%; transform: translate(-50%, -50%); min-width: 140px; max-width: 220px; color: {{ $layout['qr']['color'] ?? '#64748b' }};" class="text-left space-y-1 pointer-events-none">
                @if($course->cert_show_qr ?? true)
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded bg-white p-1 border border-slate-300 shadow-sm flex flex-col items-center justify-center">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-slate-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 3h6v6H3zM15 3h6v6h-6zM3 15h6v6H3zM15 15h2v2h-2zM19 15h2v2h-2zM15 19h2v2h-2zM19 19h2v2h-2z" />
                            </svg>
                        </div>
                        <div class="leading-tight">
                            <p class="font-bold" style="font-family: {{ $getFontFamilyCss($layout['qr']['fontFamily'] ?? 'jakarta') }}; font-size: {{ ($layout['qr']['fontSize'] ?? 7) * 1.4 }}px; color: {{ $layout['qr']['color'] ?? '#0f172a' }};">Scan Verifikasi</p>
                            <p class="font-mono text-[9px] opacity-70">Keabsahan Resmi</p>
                        </div>
                    </div>
                @endif
                <p class="font-semibold text-[9px]">Kode Hash:</p>
                <p class="font-mono font-bold break-all text-[8px]" style="color: {{ $layout['qr']['color'] ?? '#0f172a' }};">{{ $certificate->verification_hash }}</p>
                @if($course->cert_show_date ?? true)
                    <p class="text-[9px]" style="color: {{ $layout['qr']['color'] ?? '#64748b' }};">Diterbitkan: {{ $certificate->issued_at->format('d F Y') }}</p>
                @endif
                <p class="text-emerald-600 font-semibold flex items-center gap-1 text-[9px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Tanda Tangan Digital Sah
                </p>
            </div>

            <!-- 6. Official Seal Stamp -->
            @if($course->cert_show_seal ?? true)
                <div style="position: absolute; left: {{ $layout['seal']['x'] }}%; top: {{ $layout['seal']['y'] }}%; transform: translate(-50%, -50%);" class="flex flex-col items-center justify-center pointer-events-none">
                    <div class="w-14 h-14 sm:w-18 sm:h-18 rounded-full border-4 border-double flex flex-col items-center justify-center shadow-md transform rotate-[-5deg]"
                         style="border-color: {{ $layout['seal']['color'] ?? '#b45309' }}; color: {{ $layout['seal']['color'] ?? '#78350f' }}; background-color: {{ $layout['seal']['color'] ?? '#b45309' }}15;">
                        <svg class="w-5 h-5 sm:w-7 sm:h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <span class="font-extrabold uppercase tracking-wider text-center px-1 leading-none"
                              style="font-family: {{ $getFontFamilyCss($layout['seal']['fontFamily'] ?? 'jakarta') }}; font-size: {{ ($layout['seal']['fontSize'] ?? 6) * 1.35 }}px; color: {{ $layout['seal']['color'] ?? '#78350f' }};">
                            {{ $course->cert_seal_text ?: 'LMS BERAU VALID' }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- 7. Pejabat Penandatangan Resmi (FREE DRAG & MOVE POSITION) -->
            <div style="position: absolute; left: {{ $layout['signature']['x'] }}%; top: {{ $layout['signature']['y'] }}%; transform: translate(-50%, -50%); min-width: 150px; max-width: 240px; color: {{ $layout['signature']['color'] ?? '#0f172a' }};" class="text-center space-y-0.5 text-xs pointer-events-none">
                <p class="text-[9px]" style="color: {{ $layout['signature']['color'] ?? '#64748b' }}; opacity: 0.7;">Pejabat Penandatangan:</p>
                <div class="h-8 sm:h-10 flex items-center justify-center">
                    @if($course->cert_signature_image)
                        <img src="{{ $course->cert_signature_image }}" class="h-8 sm:h-10 object-contain" alt="Tanda Tangan">
                    @else
                        <span class="font-['Great_Vibes'] text-xl sm:text-2xl opacity-80" style="color: {{ $layout['signature']['color'] ?? '#0f172a' }};">Tanda Tangan Sah</span>
                    @endif
                </div>
                <p class="font-bold leading-tight"
                   style="font-family: {{ $getFontFamilyCss($layout['signature']['fontFamily'] ?? 'playfair') }}; font-size: {{ ($layout['signature']['fontSize'] ?? 10) * 1.4 }}px; color: {{ $layout['signature']['color'] ?? '#0f172a' }};">
                    {{ $course->cert_signatory_name ?: ($course->instructor->name ?? 'Dr. H. Ahmad Fauzi, M.Kom') }}
                </p>
                <p class="font-medium leading-tight"
                   style="font-size: {{ max(8, ($layout['signature']['fontSize'] ?? 10) * 1.1) }}px; color: {{ $layout['signature']['color'] ?? '#64748b' }}; opacity: 0.85;">
                    {{ $course->cert_signatory_role ?: 'Kepala BKPSDM Kabupaten Berau' }}
                </p>
                @if($course->cert_signatory_nip)
                    <p class="font-mono"
                       style="font-size: {{ max(7, ($layout['signature']['fontSize'] ?? 10) * 0.95) }}px; color: {{ $layout['signature']['color'] ?? '#94a3b8' }}; opacity: 0.75;">
                        NIP. {{ $course->cert_signatory_nip }}
                    </p>
                @endif
            </div>

        </div>

    </div>

</body>
</html>
