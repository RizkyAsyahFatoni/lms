<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi Rekapitulasi Jam Pelajaran (JP) ASN - {{ $asnReport['range']['label'] }}</title>
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
                font-size: 11pt;
            }
            .page-break {
                page-break-after: always;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            @page {
                size: A4 landscape;
                margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    {{-- ── FLOATING TOOLBAR (NO PRINT) ─────────────────────────── --}}
    <div class="no-print max-w-5xl mx-auto mb-6 bg-slate-900 text-white rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🖨️</span>
            <div>
                <h4 class="font-bold text-sm">Mode Cetak Dokumen Resmi</h4>
                <p class="text-xs text-slate-300">Format cetak telah disesuaikan untuk standar kertas A4 Landscape (PDF ready).</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.jp.print', array_merge(request()->query(), ['type' => 'asn'])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $type === 'asn' ? 'bg-teal-500 text-slate-900' : 'bg-white/10 text-white hover:bg-white/20' }}">
                Format per ASN
            </a>
            <a href="{{ route('admin.jp.print', array_merge(request()->query(), ['type' => 'opd'])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $type === 'opd' ? 'bg-teal-500 text-slate-900' : 'bg-white/10 text-white hover:bg-white/20' }}">
                Format per OPD
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-black bg-emerald-500 hover:bg-emerald-400 text-slate-950 transition shadow-md flex items-center gap-1.5">
                <span>🖨️ Cetak / Simpan PDF</span>
            </button>
            <a href="{{ route('admin.jp.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 text-white transition">
                Tutup
            </a>
        </div>
    </div>

    {{-- ── PRINT PAPER SHEET ───────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-sm border border-slate-200 print:border-0 print:p-0 print:shadow-none">

        {{-- ── KOP SURAT PEMERINTAH DAERAH ─────────────────────── --}}
        <div class="border-b-4 border-double border-slate-800 pb-4 mb-6 text-center relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-16 h-16 flex items-center justify-center shrink-0">
                    <span class="text-4xl">🏛️</span>
                </div>
                <div class="flex-1 text-center px-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">
                        PEMERINTAH DAERAH PROVINSI / KABUPATEN / KOTA
                    </h2>
                    <h1 class="text-lg sm:text-xl font-black uppercase tracking-tight text-slate-950">
                        BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA
                    </h1>
                    <p class="text-xs text-slate-600 mt-0.5">
                        {{ $appSetting->app_name ?? 'Sistem Informasi Manajemen Pembelajaran ASN (LMS KAB Berau)' }}
                    </p>
                    <p class="text-[10px] text-slate-500">
                        Gedung Pusat Pemerintahan &bull; Kontak: {{ $appSetting->contact_phone ?? '0812-3456-7890' }} &bull; Email: {{ $appSetting->contact_email ?? 'bkpsdm@instansi.go.id' }}
                    </p>
                </div>
                <div class="w-16 h-16 flex items-center justify-center shrink-0">
                    <span class="text-4xl">🇮🇩</span>
                </div>
            </div>
        </div>

        {{-- ── JUDUL LAPORAN ───────────────────────────────────── --}}
        <div class="text-center mb-6 space-y-1">
            <h3 class="text-base font-black uppercase text-slate-900 tracking-tight">
                @if($type === 'opd')
                    LAPORAN REKAPITULASI KEPATUHAN JAM PELAJARAN (JP) PER PERANGKAT DAERAH
                @else
                    LAPORAN REKAPITULASI CAPAIAN JAM PELAJARAN (JP) APARATUR SIPIL NEGARA
                @endif
            </h3>
            <p class="text-xs font-semibold text-slate-700">
                PERIODE EVALUASI: {{ strtoupper($asnReport['range']['label']) }}
            </p>
            <p class="text-[11px] text-slate-500 font-mono">
                Sesuai Ketentuan Mandatori Minimal 20 JP / Tahun Berdasarkan Kebijakan Lembaga Administrasi Negara (LAN RI)
            </p>
        </div>

        {{-- ── RINGKASAN EKSEKUTIF METRIK ──────────────────────── --}}
        <div class="grid grid-cols-4 gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6 text-xs">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500">Total ASN Terdata</span>
                <p class="text-base font-black text-slate-900">{{ number_format($asnReport['total_asn']) }} Orang</p>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500">Total JP Terkumpul</span>
                <p class="text-base font-black text-slate-900">{{ number_format($asnReport['total_jp_sum']) }} JP</p>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500">Rata-rata JP / ASN</span>
                <p class="text-base font-black text-slate-900">{{ $asnReport['avg_jp'] }} JP</p>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500">Tingkat Kepatuhan Pemda</span>
                <p class="text-base font-black text-slate-900">{{ $asnReport['compliance_percent'] }}% ({{ $asnReport['achieved_count'] }} ASN Tuntas)</p>
            </div>
        </div>

        @if($type === 'opd')
            {{-- ── TABEL PER OPD ────────────────────────────────── --}}
            <table class="w-full text-left text-xs border-collapse border border-slate-300 mb-8">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-800 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="border border-slate-300 p-2 text-center w-10">No</th>
                        <th class="border border-slate-300 p-2">Nama Perangkat Daerah (OPD)</th>
                        <th class="border border-slate-300 p-2 text-center">Jumlah ASN</th>
                        <th class="border border-slate-300 p-2 text-center">Total JP</th>
                        <th class="border border-slate-300 p-2 text-center">Rata-rata JP</th>
                        <th class="border border-slate-300 p-2 text-center">Tuntas (>=20 JP)</th>
                        <th class="border border-slate-300 p-2 text-center">Belum Tuntas</th>
                        <th class="border border-slate-300 p-2 text-center">Tingkat Kepatuhan</th>
                        <th class="border border-slate-300 p-2 text-center">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($opdReport['rows'] as $idx => $r)
                        <tr>
                            <td class="border border-slate-300 p-2 text-center font-mono">{{ $idx + 1 }}</td>
                            <td class="border border-slate-300 p-2 font-bold text-slate-900">{{ $r['opd_name'] }}</td>
                            <td class="border border-slate-300 p-2 text-center">{{ $r['total_asn'] }}</td>
                            <td class="border border-slate-300 p-2 text-center font-black">{{ number_format($r['total_jp']) }} JP</td>
                            <td class="border border-slate-300 p-2 text-center font-semibold">{{ $r['avg_jp'] }}</td>
                            <td class="border border-slate-300 p-2 text-center text-emerald-800 font-bold">{{ $r['achieved_count'] }}</td>
                            <td class="border border-slate-300 p-2 text-center text-amber-800 font-bold">{{ $r['not_achieved_count'] }}</td>
                            <td class="border border-slate-300 p-2 text-center font-black">{{ $r['compliance_rate'] }}%</td>
                            <td class="border border-slate-300 p-2 text-center font-bold">{{ $r['predicate'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            {{-- ── TABEL PER ASN ────────────────────────────────── --}}
            <table class="w-full text-left text-xs border-collapse border border-slate-300 mb-8">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-800 font-bold uppercase text-[9px]">
                    <tr>
                        <th class="border border-slate-300 p-2 text-center w-8">No</th>
                        <th class="border border-slate-300 p-2 min-w-[150px]">Nama Pegawai / NIP</th>
                        <th class="border border-slate-300 p-2">Perangkat Daerah (OPD)</th>
                        <th class="border border-slate-300 p-1 text-center" title="E-Learning">E-Learn</th>
                        <th class="border border-slate-300 p-1 text-center" title="Webinar">Webinar</th>
                        <th class="border border-slate-300 p-1 text-center" title="Klasikal">Klasikal</th>
                        <th class="border border-slate-300 p-1 text-center" title="Hybrid">Hybrid</th>
                        <th class="border border-slate-300 p-1 text-center" title="Eksternal">Eksternal</th>
                        <th class="border border-slate-300 p-2 text-center font-black">Total JP</th>
                        <th class="border border-slate-300 p-2 text-center">Capaian</th>
                        <th class="border border-slate-300 p-2 text-center">Status Pemenuhan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($asnReport['rows'] as $idx => $r)
                        <tr>
                            <td class="border border-slate-300 p-1.5 text-center font-mono text-[10px]">{{ $idx + 1 }}</td>
                            <td class="border border-slate-300 p-1.5">
                                <span class="font-bold block text-slate-900 text-[11px]">{{ $r['name'] }}</span>
                                <span class="font-mono text-[10px] text-slate-600 block">NIP: {{ $r['nip'] }}</span>
                            </td>
                            <td class="border border-slate-300 p-1.5 text-[10px]">
                                <span class="font-semibold text-slate-800 block">{{ $r['opd_name'] }}</span>
                                @if($r['jabatan'] && $r['jabatan'] !== '-')
                                    <span class="text-slate-500 block text-[9px]">{{ $r['jabatan'] }}</span>
                                @endif
                            </td>
                            <td class="border border-slate-300 p-1 text-center font-mono">{{ $r['jp_elearning'] ?: '-' }}</td>
                            <td class="border border-slate-300 p-1 text-center font-mono">{{ $r['jp_webinar'] ?: '-' }}</td>
                            <td class="border border-slate-300 p-1 text-center font-mono">{{ $r['jp_in_class'] ?: '-' }}</td>
                            <td class="border border-slate-300 p-1 text-center font-mono">{{ $r['jp_hybrid'] ?: '-' }}</td>
                            <td class="border border-slate-300 p-1 text-center font-mono">{{ $r['jp_external'] ?: '-' }}</td>
                            <td class="border border-slate-300 p-1.5 text-center font-black text-slate-950">{{ $r['total_jp'] }} JP</td>
                            <td class="border border-slate-300 p-1.5 text-center font-bold">{{ $r['percentage'] }}%</td>
                            <td class="border border-slate-300 p-1.5 text-center font-bold text-[10px] {{ $r['achieved'] ? 'text-emerald-800' : 'text-amber-800' }}">
                                {{ $r['achieved'] ? 'MEMENUHI' : 'KURANG ' . $r['remaining'] . ' JP' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ── LEMBAR PENGESAHAN & TANDA TANGAN ────────────────── --}}
        <div class="mt-12 flex justify-between items-start text-xs pt-4">
            <div class="space-y-1">
                <p class="font-semibold text-slate-600">Catatan Auditor / Verifikator:</p>
                <p class="text-[10px] text-slate-500 max-w-sm">
                    Laporan ini dicetak secara otomatis melalui {{ $appSetting->app_name ?? 'Sistem Pembelajaran Terintegrasi' }} pada {{ now()->translatedFormat('l, d F Y H:i') }} WIB dan sah sebagai dokumen bukti akreditasi dan evaluasi kinerja instansi.
                </p>
            </div>

            <div class="text-center w-64 space-y-16">
                <div>
                    <p class="text-slate-600">Ditetapkan di Tempat,</p>
                    <p class="font-semibold text-slate-800">{{ now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-slate-900 mt-1">Kepala BKPSDM / Pejabat Berwenang,</p>
                </div>
                <div>
                    <p class="font-black text-slate-900 underline uppercase tracking-tight">DR. H. AHMAD FAUZI, M.SI</p>
                    <p class="text-[10px] text-slate-600 font-mono">NIP. 19780512 200312 1 002</p>
                    <p class="text-[9px] text-slate-500">Pembina Utama Muda (IV/c)</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
