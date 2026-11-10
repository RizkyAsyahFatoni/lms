@extends('layouts.app')

@section('title', 'Dashboard Eksekutif Corporate University | Pemerintah Kabupaten Berau')

@section('content')
<div class="min-h-screen bg-slate-50/70 pb-16">

    {{-- Executive Top Hero Header --}}
    <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 text-white relative overflow-hidden border-b border-indigo-900/40">
        {{-- Ambient decorative glow & pattern --}}
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                
                {{-- Left: User & Title --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
                            👔 DASHBOARD EKSEKUTIF KORPU
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-indigo-200 border border-white/10">
                            Pemerintah Kabupaten Berau
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white flex items-center gap-3">
                        Pusat Kendali Pengembangan Kompetensi ASN
                    </h1>

                    <p class="text-xs sm:text-sm text-indigo-200/80 max-w-2xl leading-relaxed">
                        Monitoring terpadu pemenuhan target hak pengembangan minimal <strong class="text-white">20 Jam Pelajaran (JP)</strong> per tahun per ASN se-Kabupaten Berau sesuai amanat PP No. 17/2020.
                    </p>

                    <div class="pt-1 flex items-center gap-4 text-xs text-indigo-300/70">
                        <span>Pejabat: <strong class="text-white">{{ $user->name }}</strong></span>
                        @if($user->jabatan)
                            <span>• {{ $user->jabatan }}</span>
                        @endif
                    </div>
                </div>

                {{-- Right: Actions & Filter --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    {{-- Year Selector --}}
                    <form method="GET" action="{{ route('dashboard.pimpinan') }}" class="flex items-center">
                        <div class="relative w-full sm:w-auto">
                            <select name="year" onchange="this.form.submit()" 
                                    class="w-full sm:w-auto pl-3 pr-8 py-2 text-xs font-bold bg-white/10 hover:bg-white/15 border border-indigo-400/30 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-rose-400 transition cursor-pointer appearance-none">
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }} class="bg-slate-900 text-white">
                                        Tahun Anggaran {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="w-4 h-4 text-indigo-300 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </form>

                    {{-- Switch to Learner Mode --}}
                    <a href="{{ route('dashboard.learner') }}" 
                       class="inline-flex items-center justify-center gap-2 px-3.5 py-2 text-xs font-bold text-indigo-100 bg-indigo-900/60 hover:bg-indigo-800/80 border border-indigo-700/50 rounded-xl shadow-sm transition group"
                       title="Beralih ke mode belajar untuk memantau kursus dan sertifikat pribadi">
                        <span class="group-hover:rotate-12 transition-transform">🎓</span>
                        <span>Mode Belajar Saya</span>
                    </a>

                    {{-- Print / Export --}}
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-white/90 bg-white/10 hover:bg-white/20 border border-white/15 rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span class="hidden sm:inline">Cetak</span>
                        </button>

                        <a href="{{ route('admin.jp.export.opd', ['year' => $currentYear]) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-900 bg-gradient-to-r from-emerald-400 to-teal-300 hover:from-emerald-300 hover:to-teal-200 rounded-xl shadow-lg transition whitespace-nowrap">
                            <svg class="w-4 h-4 text-emerald-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Ekspor Excel</span>
                        </a>
                    </div>
                </div>

            </div>

            {{-- 5 High-Impact Executive KPI Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5 mt-8">
                
                {{-- Card 1: Total ASN --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-indigo-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Total ASN Terdaftar</span>
                        <span class="text-base group-hover:scale-110 transition-transform">👥</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ number_format($totalAsnKabupaten) }}
                    </div>
                    <p class="text-[11px] text-indigo-200/70 mt-1 flex items-center gap-1">
                        <span class="text-emerald-400 font-semibold">● Aktif</span> di {{ $opdReport['total_opd'] ?? 0 }} Perangkat Daerah
                    </p>
                </div>

                {{-- Card 2: Kepatuhan 20 JP --}}
                <div class="bg-gradient-to-br from-rose-500/20 to-purple-600/20 backdrop-blur-md border border-rose-400/30 p-4 rounded-2xl relative overflow-hidden group hover:border-rose-400/50 transition shadow-sm">
                    <div class="flex items-center justify-between text-rose-200 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Kepatuhan 20 JP</span>
                        <span class="text-base group-hover:scale-110 transition-transform">🎯</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black tracking-tight text-rose-300">
                            {{ $opdReport['grand_compliance'] ?? 0 }}%
                        </span>
                        <span class="text-[11px] text-white/70 font-semibold">
                            ({{ number_format($opdReport['grand_achieved'] ?? 0) }}/{{ number_format($opdReport['grand_total_asn'] ?? 0) }})
                        </span>
                    </div>
                    {{-- Mini Progress Bar --}}
                    <div class="w-full bg-white/20 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-rose-400 to-amber-300 h-full rounded-full transition-all duration-1000" style="width: {{ min(100, $opdReport['grand_compliance'] ?? 0) }}%"></div>
                    </div>
                </div>

                {{-- Card 3: Rata-Rata JP --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-indigo-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Rata-Rata JP / ASN</span>
                        <span class="text-base group-hover:scale-110 transition-transform">⚡</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-amber-300">
                        {{ number_format($opdReport['grand_avg_jp'] ?? 0, 1) }} <span class="text-xs font-semibold text-white/60">JP</span>
                    </div>
                    <p class="text-[11px] text-indigo-200/70 mt-1">
                        Target minimal: <strong class="text-white">20.0 JP/tahun</strong>
                    </p>
                </div>

                {{-- Card 4: Total JP Kumulatif --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-indigo-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Akumulasi Jam Pelatihan</span>
                        <span class="text-base group-hover:scale-110 transition-transform">⏱️</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-teal-300">
                        {{ number_format($opdReport['grand_total_jp'] ?? 0) }} <span class="text-xs font-semibold text-white/60">JP</span>
                    </div>
                    <p class="text-[11px] text-indigo-200/70 mt-1">
                        Total serapan jam se-Berau
                    </p>
                </div>

                {{-- Card 5: Total Pelatihan --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm col-span-2 sm:col-span-1">
                    <div class="flex items-center justify-between text-indigo-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Program Pelatihan</span>
                        <span class="text-base group-hover:scale-110 transition-transform">📚</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $totalCoursesActive + $totalWebinarsYear }}
                    </div>
                    <p class="text-[11px] text-indigo-200/70 mt-1 truncate">
                        {{ $totalCoursesActive }} Kursus • {{ $totalWebinarsYear }} Webinar
                    </p>
                </div>

            </div>

        </div>
    </div>

    {{-- Main Body Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

        {{-- Row 2: Metode Pembelajaran & Status KorpU --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Komposisi Metode Pelatihan (2 Cols) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <span>📊</span> Distribusi Jam Pelajaran (JP) Berdasarkan Metode
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Analisis serapan pembelajaran daring mandiri, klasikal, maupun eksternal</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2 py-1 bg-slate-100 text-slate-600 rounded-lg">
                        Tahun {{ $currentYear }}
                    </span>
                </div>

                @php
                    $grandJp = max(1, $opdReport['grand_total_jp'] ?? 1);
                    $methods = [
                        [
                            'label' => 'E-Learning Mandiri (Asynchronous)',
                            'key' => 'elearning',
                            'jp' => $typeSums['elearning'] ?? 0,
                            'color' => 'bg-indigo-600',
                            'textColor' => 'text-indigo-600',
                            'bgLight' => 'bg-indigo-50 border-indigo-100',
                            'icon' => '💻',
                            'desc' => 'Modul materi online, kuis, dan video interaktif LMS',
                        ],
                        [
                            'label' => 'Webinar & Live Workshop (Synchronous)',
                            'key' => 'webinar',
                            'jp' => $typeSums['webinar'] ?? 0,
                            'color' => 'bg-teal-600',
                            'textColor' => 'text-teal-600',
                            'bgLight' => 'bg-teal-50 border-teal-100',
                            'icon' => '📡',
                            'desc' => 'Seminar daring, narasumber ahli, dan telekonferensi',
                        ],
                        [
                            'label' => 'Klasikal / Bimtek / Workshop Tatap Muka',
                            'key' => 'in_class',
                            'jp' => $typeSums['in_class'] ?? 0,
                            'color' => 'bg-amber-600',
                            'textColor' => 'text-amber-600',
                            'bgLight' => 'bg-amber-50 border-amber-100',
                            'icon' => '🏛️',
                            'desc' => 'Pelatihan tatap muka dan bimbingan teknis OPD',
                        ],
                        [
                            'label' => 'Hybrid Learning (Blended)',
                            'key' => 'hybrid',
                            'jp' => $typeSums['hybrid'] ?? 0,
                            'color' => 'bg-purple-600',
                            'textColor' => 'text-purple-600',
                            'bgLight' => 'bg-purple-50 border-purple-100',
                            'icon' => '🔄',
                            'desc' => 'Kombinasi pembelajaran daring dan luring',
                        ],
                        [
                            'label' => 'Diklat Mandiri Eksternal',
                            'key' => 'external',
                            'jp' => $typeSums['external'] ?? 0,
                            'color' => 'bg-blue-600',
                            'textColor' => 'text-blue-600',
                            'bgLight' => 'bg-blue-50 border-blue-100',
                            'icon' => '📜',
                            'desc' => 'Pelatihan di luar instansi diverifikasi BKPSDM',
                        ],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach($methods as $m)
                        @php
                            $pct = round(($m['jp'] / $grandJp) * 100, 1);
                        @endphp
                        <div class="p-3.5 rounded-xl border {{ $m['bgLight'] }} transition hover:shadow-xs">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">{{ $m['icon'] }}</span>
                                    <span class="text-xs font-bold text-slate-800">{{ $m['label'] }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-black {{ $m['textColor'] }}">{{ number_format($m['jp']) }} JP</span>
                                    <span class="text-[11px] font-mono text-slate-500 font-bold ml-1.5">({{ $pct }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-200/70 h-2 rounded-full overflow-hidden">
                                <div class="{{ $m['color'] }} h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1.5">{{ $m['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Status Ringkasan Eksekutif KorpU (1 Col) --}}
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-indigo-800/50 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            STANDAR LAN RI & BKN
                        </span>
                        <span class="text-xl">🏆</span>
                    </div>

                    <h3 class="text-lg font-black text-white">Status Capaian KorpU Berau</h3>
                    <p class="text-xs text-indigo-200/80 mt-1 leading-relaxed">
                        Evaluasi kepatuhan pengembangan talenta berbasis Indeks Profesionalitas ASN (IP-ASN).
                    </p>

                    <div class="mt-6 p-4 rounded-xl bg-white/10 border border-white/15">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-indigo-200">Indeks Kepatuhan Kumulatif</span>
                            <span class="text-sm font-extrabold text-amber-300">{{ $opdReport['grand_compliance'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-black/30 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-400 to-emerald-400 h-full rounded-full" style="width: {{ min(100, $opdReport['grand_compliance'] ?? 0) }}%"></div>
                        </div>
                        <p class="text-[11px] text-indigo-200/70 mt-2">
                            {{ $opdReport['grand_achieved'] ?? 0 }} dari {{ $opdReport['grand_total_asn'] ?? 0 }} ASN telah memenuhi standar minimal 20 JP tahun {{ $currentYear }}.
                        </p>
                    </div>

                    <div class="mt-4 space-y-2 text-xs">
                        <div class="flex items-center justify-between p-2 rounded-lg bg-white/5">
                            <span class="text-indigo-200">Predikat Kinerja KorpU</span>
                            <span class="font-extrabold text-emerald-300">
                                @if(($opdReport['grand_compliance'] ?? 0) >= 80)
                                    Sangat Baik (A)
                                @elseif(($opdReport['grand_compliance'] ?? 0) >= 60)
                                    Baik (B)
                                @elseif(($opdReport['grand_compliance'] ?? 0) >= 40)
                                    Cukup (C)
                                @else
                                    Perlu Perhatian (D)
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-white/5">
                            <span class="text-indigo-200">Pelatihan Eksternal Disahkan</span>
                            <span class="font-bold text-white">{{ $totalApprovedExt }} Sertifikasi</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between">
                    <span class="text-[11px] text-indigo-300">Laporan Resmi BKPSDM</span>
                    <a href="{{ route('admin.jp.print', ['year' => $currentYear]) }}" target="_blank"
                       class="text-xs font-bold text-amber-300 hover:text-amber-200 flex items-center gap-1">
                        <span>Cetak Rinci</span> &rarr;
                    </a>
                </div>
            </div>

        </div>

        {{-- Row 3: Tabel Peringkat Kepatuhan 20 JP per Perangkat Daerah (OPD) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden" x-data="{ searchOpd: '' }">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🏛️</span> Rekapitulasi & Peringkat Kinerja Perangkat Daerah (OPD)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pemeringkatan berdasarkan persentase kepatuhan pegawai yang telah memenuhi kewajiban 20 JP
                    </p>
                </div>

                {{-- Search Filter & Actions --}}
                <div class="flex items-center gap-3">
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchOpd" placeholder="Cari nama OPD..." 
                               class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <a href="{{ route('admin.jp.opd', ['year' => $currentYear]) }}" 
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition">
                        <span>Detail Tabel</span> &rarr;
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/70">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">Peringkat</th>
                            <th class="py-3.5 px-4">Nama Perangkat Daerah (OPD)</th>
                            <th class="py-3.5 px-4 text-center">Jumlah ASN</th>
                            <th class="py-3.5 px-4 text-right">Total JP OPD</th>
                            <th class="py-3.5 px-4 text-right">Rata-Rata JP</th>
                            <th class="py-3.5 px-4 w-44">Tingkat Kepatuhan (≥ 20 JP)</th>
                            <th class="py-3.5 px-4 text-center">Predikat</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($opdRows as $idx => $opd)
                            <tr class="hover:bg-slate-50/80 transition"
                                x-show="searchOpd === '' || '{{ strtolower($opd['opd_name']) }}'.includes(searchOpd.toLowerCase())">
                                {{-- Rank --}}
                                <td class="py-3.5 px-4 text-center">
                                    @if($idx == 0)
                                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[11px] inline-flex items-center justify-center border border-amber-300 shadow-2xs">🥇</span>
                                    @elseif($idx == 1)
                                        <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] inline-flex items-center justify-center border border-slate-300 shadow-2xs">🥈</span>
                                    @elseif($idx == 2)
                                        <span class="w-6 h-6 rounded-full bg-amber-50 text-amber-900 font-extrabold text-[11px] inline-flex items-center justify-center border border-amber-200 shadow-2xs">🥉</span>
                                    @else
                                        <span class="font-mono text-slate-400 font-bold">#{{ $idx + 1 }}</span>
                                    @endif
                                </td>

                                {{-- OPD Name --}}
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $opd['opd_name'] }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        E-Learning: {{ number_format($opd['type_sums']['elearning'] ?? 0) }} JP • Webinar: {{ number_format($opd['type_sums']['webinar'] ?? 0) }} JP
                                    </div>
                                </td>

                                {{-- Total ASN --}}
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono font-bold bg-slate-100 text-slate-700">
                                        {{ $opd['total_asn'] }} ASN
                                    </span>
                                </td>

                                {{-- Total JP --}}
                                <td class="py-3.5 px-4 text-right font-mono font-extrabold text-slate-900">
                                    {{ number_format($opd['total_jp']) }} <span class="text-[10px] text-slate-400">JP</span>
                                </td>

                                {{-- Average JP --}}
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-indigo-700">
                                    {{ number_format($opd['avg_jp'], 1) }}
                                </td>

                                {{-- Compliance % & Bar --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="font-extrabold {{ $opd['compliance_rate'] >= 80 ? 'text-emerald-700' : ($opd['compliance_rate'] >= 50 ? 'text-amber-700' : 'text-rose-700') }}">
                                            {{ $opd['compliance_rate'] }}%
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            {{ $opd['achieved_count'] }}/{{ $opd['total_asn'] }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $opd['compliance_rate'] >= 80 ? 'bg-emerald-500' : ($opd['compliance_rate'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                             style="width: {{ min(100, $opd['compliance_rate']) }}%"></div>
                                    </div>
                                </td>

                                {{-- Predicate Badge --}}
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $opd['predicate_badge'] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $opd['predicate'] }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('admin.jp.asn', ['opd_name' => $opd['opd_name'], 'year' => $currentYear]) }}" 
                                       class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/60 transition whitespace-nowrap">
                                        Lihat ASN
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Belum ada data rekapitulasi JP perangkat daerah untuk tahun {{ $currentYear }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Footer Summary --}}
            <div class="p-4 bg-slate-50 border-t border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-500 gap-2">
                <div>
                    Menampilkan <strong>{{ count($opdRows) }}</strong> Perangkat Daerah di Kabupaten Berau.
                </div>
                <div class="flex items-center gap-4 text-[11px]">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> ≥80% Sangat Baik</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> 60-79% Baik</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> 40-59% Cukup</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> &lt;40% Perhatian</span>
                </div>
            </div>
        </div>

        {{-- Row 4: Top Kursus & Top ASN Leaderboard --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Top 5 Kursus Terpopuler --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <span>⭐</span> Top 5 Kursus Paling Banyak Diselesaikan ASN
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Materi pembelajaran dengan tingkat kelulusan tertinggi</p>
                    </div>
                    <a href="{{ route('courses.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Katalog &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($topCourses as $idx => $tc)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/60 hover:border-slate-300 transition">
                            <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-800 font-extrabold text-xs flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 truncate">{{ $tc->title }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-2">
                                    <span>{{ $tc->category->name ?? 'Umum' }}</span>
                                    <span>•</span>
                                    <span class="font-mono font-bold text-amber-600">{{ $tc->jp ?? 2 }} JP</span>
                                    <span>•</span>
                                    <span>{{ $tc->instructor->name ?? 'BKPSDM' }}</span>
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $tc->completions_count }} Lulus
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada data penyelesaian kursus.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top 5 ASN Berprestasi (Leaderboard Capaian JP) --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <span>🎖️</span> Leaderboard ASN Paling Berdedikasi (Top JP)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pegawai dengan akumulasi jam pelajaran tertinggi tahun ini</p>
                    </div>
                    <a href="{{ route('admin.jp.asn', ['year' => $currentYear]) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Semua ASN &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($topLearners as $idx => $tl)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/60 hover:border-slate-300 transition">
                            <span class="w-6 h-6 rounded-full {{ $idx == 0 ? 'bg-amber-400 text-amber-950 font-black' : 'bg-slate-200 text-slate-700 font-bold' }} text-xs flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 truncate">{{ $tl['name'] ?? 'ASN' }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5 truncate">
                                    {{ $tl['opd_name'] ?? '-' }} • NIP: {{ $tl['nip'] ?? '-' }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-sm font-black text-indigo-700 font-mono">{{ $tl['total_jp'] }}</span>
                                <span class="text-[10px] text-slate-400">JP</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada data capaian JP ASN.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
