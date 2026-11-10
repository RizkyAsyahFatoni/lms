<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi {{ $entityMeta['label'] }} - Pemerintah Kabupaten Berau</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff;
                color: #000000;
                font-size: 10pt;
                padding: 0;
            }
            .page-break {
                page-break-after: always;
            }
            table {
                page-break-inside: auto;
                width: 100%;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            @page {
                size: A4 landscape;
                margin: 1.2cm 1.2cm 1.2cm 1.2cm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    {{-- ── FLOATING TOOLBAR (NO PRINT) ─────────────────────────── --}}
    <div class="no-print max-w-6xl mx-auto mb-6 bg-slate-900 text-white rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🖨️</span>
            <div>
                <h4 class="font-bold text-sm">Pratinjau Cetak Laporan Resmi (PDF)</h4>
                <p class="text-xs text-slate-300">Format telah dioptimalkan untuk standar kertas A4 Landscape (Laporan Berau Corpu).</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-teal-400 hover:bg-teal-300 text-slate-950 font-black text-xs transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition">
                Tutup
            </button>
        </div>
    </div>

    {{-- ── PRINT PAPER SHEET (A4 LANDSCAPE) ─────────────────────── --}}
    <div class="max-w-6xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-200/80 shadow-sm print:border-none print:shadow-none print:p-0">
        
        <!-- Official Government Letterhead (Kop Surat Pemkab Berau) -->
        <div class="border-b-4 border-double border-slate-900 pb-4 mb-6 flex items-center gap-5">
            <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                @if($appSetting && $appSetting->logo_path)
                    <img src="{{ asset('storage/' . $appSetting->logo_path) }}" class="h-20 w-auto object-contain" alt="Logo Berau">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white font-black text-2xl flex items-center justify-center">
                        🏛️
                    </div>
                @endif
            </div>

            <div class="flex-1 text-center">
                <h3 class="text-sm font-extrabold uppercase tracking-widest text-slate-800 leading-tight">
                    Pemerintah Kabupaten Berau
                </h3>
                <h1 class="text-lg font-black uppercase tracking-wider text-slate-900 leading-tight">
                    Badan Kepegawaian, Pendidikan dan Pelatihan
                </h1>
                <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">
                    Jl. Murjani II No. 1, Tanjung Redeb, Kabupaten Berau, Kalimantan Timur 77312
                </p>
                <p class="text-[10px] text-slate-500 font-mono">
                    Laman Resmi LMS: lms.beraukab.go.id • Pos-el: bkpsdm@beraukab.go.id
                </p>
            </div>

            <div class="w-20 text-right font-mono text-[9px] text-slate-400">
                <span>DOKUMEN RESMI</span>
            </div>
        </div>

        <!-- Document Title & Meta -->
        <div class="text-center mb-6">
            <h2 class="text-base font-black uppercase tracking-tight text-slate-900">
                LAPORAN {{ strtoupper($entityMeta['label']) }}
            </h2>
            <p class="text-xs text-slate-600 mt-1 font-medium">
                Sistem Tata Kelola Berau Corporate University & Learning Record ASN
            </p>
            <div class="inline-flex items-center gap-3 text-[11px] font-mono text-slate-500 mt-2 bg-slate-50 px-3 py-1 rounded-lg border border-slate-200">
                <span>Tahun Anggaran: {{ $filters['year'] ?? date('Y') }}</span>
                <span>•</span>
                <span>Perangkat Daerah: {{ ($filters['opd_name'] ?? 'all') === 'all' ? 'Seluruh OPD' : $filters['opd_name'] }}</span>
                <span>•</span>
                <span>Waktu Tarik: {{ now()->format('d/m/Y H:i') }} WITA</span>
            </div>
        </div>

        <!-- Main Data Table -->
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-left text-xs border-collapse border border-slate-300">
                <thead class="bg-slate-100 text-slate-800 font-extrabold border-b-2 border-slate-300 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-2.5 border border-slate-300 text-center w-10">No</th>
                        @foreach($selectedColumns as $colKey)
                            <th class="p-2.5 border border-slate-300 whitespace-nowrap">
                                {{ $allColumns[$colKey] ?? $colKey }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-[11px]">
                    @forelse($rows as $idx => $row)
                        <tr class="{{ $idx % 2 === 1 ? 'bg-slate-50/50' : 'bg-white' }}">
                            <td class="p-2 border border-slate-300 text-center font-mono font-medium text-slate-500">
                                {{ $idx + 1 }}
                            </td>
                            @foreach($selectedColumns as $colKey)
                                <td class="p-2 border border-slate-300 text-slate-800">
                                    {{ $row[$colKey] ?? '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($selectedColumns) + 1 }}" class="p-6 text-center text-slate-400 italic">
                                Tidak ada data yang ditemukan untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Document Footer & Validation Stamp -->
        <div class="flex items-end justify-between pt-6 border-t border-slate-200 text-xs">
            <div class="space-y-1 text-slate-500 text-[10px] font-mono">
                <p>Dokumen ini digenerate secara otomatis oleh Sistem LMS Berau Corpu.</p>
                <p>Dicetak oleh: {{ $user->name }} (NIP: {{ $user->nip ?: '-' }})</p>
                <p>Hash Verifikasi: {{ strtoupper(substr(md5(now() . $user->id), 0, 16)) }}</p>
            </div>

            <!-- Signature Column -->
            <div class="text-center w-64 space-y-1">
                <p class="text-xs text-slate-700">Tanjung Redeb, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="text-xs font-bold text-slate-900 leading-tight">Kepala BKPSDM Kabupaten Berau</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="text-[10px] font-mono text-slate-400 border border-dashed border-slate-300 px-3 py-1 rounded">
                        [Tanda Tangan Elektronik]
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-900 underline">DRA. SRI EKAWATI</p>
                <p class="text-[10px] font-mono text-slate-600">NIP. 19680512 199303 2 004</p>
            </div>
        </div>

    </div>

</body>
</html>
