<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sertifikat - {{ $webinar->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; background: white; }
            .cert-page { box-shadow: none !important; margin: 0 !important; width: 100vw !important; height: 100vh !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 flex flex-col items-center">

    <!-- Action Bar -->
    <div class="no-print max-w-4xl w-full mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('webinars.show', $webinar->slug) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            ← Kembali ke Ruang Webinar
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-2">
                <span>🖨️ Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Certificate Sheet (A4 Landscape aspect) -->
    <div class="cert-page relative bg-white w-[1000px] h-[700px] p-10 rounded-2xl shadow-2xl border-8 border-teal-800 flex flex-col justify-between overflow-hidden">
        
        <!-- Ornaments -->
        <div class="absolute top-0 right-0 w-44 h-44 bg-gradient-to-bl from-teal-700 via-teal-800 to-transparent opacity-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-44 h-44 bg-gradient-to-tr from-amber-600 via-amber-700 to-transparent opacity-20 pointer-events-none"></div>

        <!-- Header -->
        <div class="text-center space-y-1 relative z-10">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-teal-800 text-white flex items-center justify-center font-black text-xl shadow-md">
                    1
                </div>
                <div class="text-left">
                    <h3 class="text-sm font-black tracking-widest uppercase text-teal-900 leading-none">PEMERINTAH KABUPATEN BERAU</h3>
                    <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</p>
                </div>
            </div>

            <h1 class="text-3xl font-serif font-black tracking-wider text-slate-900 uppercase">
                SERTIFIKAT KELULUSAN
            </h1>
            <p class="text-xs font-mono text-slate-500 tracking-widest uppercase">
                NOMOR: {{ $record->certificate_number ?? 'WBN/' . date('Y') . '/CERT-001' }}
            </p>
        </div>

        <!-- Recipient Info -->
        <div class="text-center space-y-3 my-auto relative z-10">
            <p class="text-xs text-slate-600 tracking-widest uppercase">Diberikan kepada:</p>
            <h2 class="text-3xl font-black text-teal-950 font-serif tracking-tight border-b-2 border-slate-300 inline-block px-8 pb-1">
                {{ $user->name }}
            </h2>
            @if($user->nip)
                <p class="text-xs font-mono text-slate-600">NIP: {{ $user->nip }}</p>
            @endif

            <p class="text-xs text-slate-600 max-w-xl mx-auto leading-relaxed pt-2">
                Atas partisipasi aktif dan kelulusan evaluasi pada kegiatan pembelajaran:
            </p>

            <h3 class="text-xl font-extrabold text-slate-900 max-w-2xl mx-auto leading-snug">
                "{{ $webinar->title }}"
            </h3>

            <p class="text-xs text-slate-600">
                Diselenggarakan pada <strong>{{ $webinar->event_date->translatedFormat('d F Y') }}</strong>
                dengan beban pembelajaran setara <strong class="font-mono text-teal-800">{{ $record->jp_earned }} Jam Pelajaran (JP)</strong>.
            </p>
        </div>

        <!-- Footer: Signatory & QR -->
        <div class="flex items-end justify-between pt-6 border-t border-slate-200 relative z-10">
            <!-- QR Verification -->
            <div class="flex items-center gap-3">
                <div class="w-16 h-16 bg-slate-900 text-white rounded-lg flex flex-col items-center justify-center p-1 text-center">
                    <span class="text-[9px] font-mono leading-none">VERIFIED</span>
                    <span class="text-xs font-mono font-bold mt-1">LMS BERAU</span>
                </div>
                <div class="text-[10px] text-slate-500 leading-tight">
                    <p class="font-bold text-slate-800">Verifikasi Resmi</p>
                    <p>Sertifikat elektronik sah tercatat pada</p>
                    <p class="font-mono">Learning Record Nasional</p>
                </div>
            </div>

            <!-- Signatory -->
            <div class="text-center min-w-[220px]">
                <p class="text-xs text-slate-600">{{ $webinar->event_date->translatedFormat('d F Y') }}</p>
                <p class="text-xs font-bold text-slate-800 mb-8">{{ $webinar->cert_signatory_role ?: 'Kepala Pusat Pelatihan SDM' }}</p>
                <p class="text-xs font-black text-slate-900 underline">{{ $webinar->cert_signatory_name ?: 'Dr. H. Ahmad Fauzi, M.Kes' }}</p>
                @if($webinar->cert_signatory_nip)
                    <p class="text-[10px] font-mono text-slate-500">NIP: {{ $webinar->cert_signatory_nip }}</p>
                @endif
            </div>
        </div>

    </div>

</body>
</html>
