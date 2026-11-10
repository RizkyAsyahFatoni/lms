@extends('layouts.admin')

@section('page_title', 'Rekapitulasi Jam Pelajaran (JP) ASN')

@section('content')
<div class="space-y-6">

    {{-- ── HERO / HEADER SECTION ────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #134e4a 60%, #0f766e 100%);">
        <div class="relative z-10 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/20 text-white uppercase tracking-wider">
                        PENGEMBANGAN KOMPETENSI PEGAWAI
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-400/20 text-teal-200 border border-teal-300/30">
                        TARGET MIN. 20 JP / TAHUN
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Pencatatan & Rekapitulasi Jam Pelajaran (JP)
                </h1>
                <p class="text-teal-100 text-xs sm:text-sm leading-relaxed">
                    Pemantauan otomatis akumulasi jam pelajaran seluruh ASN, evaluasi kepatuhan pengembangan kompetensi per Perangkat Daerah (OPD), dan analisis multi-metode pembelajaran digital.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.jp.asn', request()->query()) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-slate-800 hover:bg-slate-100 transition shadow-sm flex items-center gap-2">
                    <span>👥 Rekap per ASN</span>
                </a>
                <a href="{{ route('admin.jp.opd', request()->query()) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition shadow-sm flex items-center gap-2">
                    <span>🏛️ Rekap per OPD</span>
                </a>
                <a href="{{ route('admin.jp.print', request()->query()) }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold bg-teal-500 hover:bg-teal-400 text-slate-900 transition shadow-sm flex items-center gap-2">
                    <span>🖨️ Cetak Laporan</span>
                </a>
            </div>
        </div>

        {{-- Background pattern --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    {{-- ── TAB NAVIGATION BAR ──────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-2">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.jp.index', request()->query()) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white shadow-sm flex items-center gap-1.5">
                <span>📊 Dashboard JP</span>
            </a>
            <a href="{{ route('admin.jp.asn', request()->query()) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition flex items-center gap-1.5">
                <span>👥 Rekap per ASN</span>
                <span class="px-1.5 py-0.2 rounded-full bg-white text-[10px] font-mono text-slate-600 border border-slate-200">
                    {{ $asnReport['total_asn'] }}
                </span>
            </a>
            <a href="{{ route('admin.jp.opd', request()->query()) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition flex items-center gap-1.5">
                <span>🏛️ Rekap per OPD</span>
                <span class="px-1.5 py-0.2 rounded-full bg-white text-[10px] font-mono text-slate-600 border border-slate-200">
                    {{ $opdReport['total_opd'] }}
                </span>
            </a>
        </div>

        <div class="text-xs text-slate-500 font-medium">
            Periode Aktif: <strong class="text-slate-800">{{ $asnReport['range']['label'] }}</strong>
        </div>
    </div>

    {{-- ── FILTER PERIODE & OPD CARD ───────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.jp.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Tahun Evaluasi</label>
                <select name="year" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                    @php $currY = (int) date('Y'); @endphp
                    @for($y = $currY; $y >= $currY - 4; $y--)
                        <option value="{{ $y }}" {{ ($filters['year'] ?? $currY) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Sub-Periode</label>
                <select name="period_type" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                    <option value="all" {{ ($filters['period_type'] ?? 'all') === 'all' ? 'selected' : '' }}>Setahun Penuh</option>
                    <option value="s1" {{ ($filters['period_type'] ?? '') === 's1' ? 'selected' : '' }}>Semester 1 (Jan–Jun)</option>
                    <option value="s2" {{ ($filters['period_type'] ?? '') === 's2' ? 'selected' : '' }}>Semester 2 (Jul–Des)</option>
                    <option value="q1" {{ ($filters['period_type'] ?? '') === 'q1' ? 'selected' : '' }}>Triwulan I (Jan–Mar)</option>
                    <option value="q2" {{ ($filters['period_type'] ?? '') === 'q2' ? 'selected' : '' }}>Triwulan II (Apr–Jun)</option>
                    <option value="q3" {{ ($filters['period_type'] ?? '') === 'q3' ? 'selected' : '' }}>Triwulan III (Jul–Sep)</option>
                    <option value="q4" {{ ($filters['period_type'] ?? '') === 'q4' ? 'selected' : '' }}>Triwulan IV (Okt–Des)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Perangkat Daerah (OPD)</label>
                <select name="opd_name" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                    <option value="all">Semua Perangkat Daerah</option>
                    @foreach($asnReport['available_opds'] as $opd)
                        <option value="{{ $opd }}" {{ ($filters['opd_name'] ?? '') === $opd ? 'selected' : '' }}>{{ $opd }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Status Target JP</label>
                <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="achieved" {{ ($filters['status'] ?? '') === 'achieved' ? 'selected' : '' }}>Memenuhi (>= 20 JP)</option>
                    <option value="not_achieved" {{ ($filters['status'] ?? '') === 'not_achieved' ? 'selected' : '' }}>Belum Memenuhi (&lt; 20 JP)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>🔍 Terapkan</span>
                </button>
                <a href="{{ route('admin.jp.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Reset Filter">
                    <span>↺</span>
                </a>
            </div>
        </form>
    </div>

    {{-- ── 4 KPI CARDS UTAMA ────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Akumulasi JP --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-teal-300 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Akumulasi JP</span>
                <span class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg">
                    ⏱️
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($asnReport['total_jp_sum']) }}</p>
                <span class="text-xs font-bold text-teal-700">JP</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
                Akumulasi seluruh kegiatan terverifikasi pada periode ini
            </p>
            <div class="h-1 w-full bg-slate-100 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-teal-500 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        {{-- Card 2: Rata-rata JP per ASN --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-indigo-300 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Rata-rata JP / ASN</span>
                <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg">
                    📈
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $asnReport['avg_jp'] }}</p>
                <span class="text-xs font-bold text-indigo-700">JP / ASN</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
                Target LAN RI: <strong class="text-slate-700">20 JP / tahun</strong>
            </p>
            @php
                $avgTargetPercent = min(100, round(($asnReport['avg_jp'] / 20) * 100));
            @endphp
            <div class="h-1 w-full bg-slate-100 rounded-full mt-3 overflow-hidden">
                <div class="h-full {{ $asnReport['avg_jp'] >= 20 ? 'bg-emerald-500' : 'bg-amber-500' }} rounded-full" style="width: {{ $avgTargetPercent }}%"></div>
            </div>
        </div>

        {{-- Card 3: Tingkat Kepatuhan ASN --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-emerald-300 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tingkat Kepatuhan</span>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg">
                    🏅
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $asnReport['compliance_percent'] }}%</p>
                <span class="text-[11px] text-emerald-700 font-bold">({{ $asnReport['achieved_count'] }}/{{ $asnReport['total_asn'] }} ASN)</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
                {{ $asnReport['not_achieved_count'] }} ASN belum mencapai 20 JP
            </p>
            <div class="h-1 w-full bg-slate-100 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $asnReport['compliance_percent'] }}%"></div>
            </div>
        </div>

        {{-- Card 4: Total OPD Terpantau --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-purple-300 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Perangkat Daerah</span>
                <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-lg">
                    🏛️
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $opdReport['total_opd'] }}</p>
                <span class="text-xs font-bold text-purple-700">OPD</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
                Kepatuhan Pemda: <strong class="text-slate-700">{{ $opdReport['grand_compliance'] }}%</strong>
            </p>
            <div class="h-1 w-full bg-slate-100 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-purple-500 rounded-full" style="width: {{ $opdReport['grand_compliance'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- ── 5 BREAKDOWN JENIS PEMBELAJARAN (KAK POIN 7) ─────────── --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span>📚</span> Rincian Jam Pelajaran Berdasarkan Metode Pembelajaran
                </h2>
                <p class="text-xs text-slate-500">
                    Distribusi JP ASN yang diakui platform sesuai standar Perka LAN RI
                </p>
            </div>
            <div class="text-xs font-bold text-slate-600 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                Total: {{ number_format($asnReport['total_jp_sum']) }} JP
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 pt-1">
            @foreach($learningTypes as $key => $type)
                @php
                    $val = $asnReport['type_sums'][$key] ?? 0;
                    $pct = $asnReport['total_jp_sum'] > 0 ? round(($val / $asnReport['total_jp_sum']) * 100, 1) : 0;
                @endphp
                <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200 hover:bg-white hover:shadow-xs transition group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xl group-hover:scale-110 transition-transform">{{ $type['icon'] }}</span>
                        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded {{ $type['badge_color'] }} border">
                            {{ $pct }}%
                        </span>
                    </div>
                    <div class="text-xs font-bold text-slate-800 line-clamp-1 mb-1">
                        {{ $type['label'] }}
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-slate-900">{{ number_format($val) }}</span>
                        <span class="text-[11px] font-bold text-slate-500">JP</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                        {{ $type['description'] }}
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('admin.jp.asn', array_merge(request()->query(), ['learning_type' => $key])) }}"
                           class="text-[11px] font-bold text-teal-700 hover:text-teal-900 flex items-center gap-1">
                            <span>Lihat ASN →</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── DUAL PREVIEW SECTIONS: TOP OPD & TOP ASN ─────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Section 1: Ranking Kepatuhan Perangkat Daerah --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span>🏛️</span> Kepatuhan JP per Perangkat Daerah (OPD)
                        </h3>
                        <p class="text-[11px] text-slate-400">Peringkat berdasarkan persentase ASN mencapai 20 JP</p>
                    </div>
                    <a href="{{ route('admin.jp.opd', request()->query()) }}" class="text-xs font-bold text-teal-700 hover:underline">
                        Lihat Semua OPD →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($opdReport['rows']->take(5) as $idx => $opd)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-6 h-6 rounded-full {{ $idx === 0 ? 'bg-amber-400 text-slate-900' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : 'bg-slate-200 text-slate-700') }} font-black text-xs flex items-center justify-center shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ $opd['opd_name'] }}</h4>
                                    <p class="text-[10px] text-slate-400">
                                        {{ $opd['total_asn'] }} ASN &bull; {{ number_format($opd['total_jp']) }} JP &bull; Rata-rata {{ $opd['avg_jp'] }} JP/ASN
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $opd['predicate_badge'] }}">
                                    {{ $opd['compliance_rate'] }}% Kepatuhan
                                </span>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">
                                    {{ $opd['achieved_count'] }}/{{ $opd['total_asn'] }} ASN
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Belum ada data perangkat daerah tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Menampilkan 5 OPD terbaik</span>
                <a href="{{ route('admin.jp.export.opd', request()->query()) }}" class="font-bold text-teal-700 hover:text-teal-900 flex items-center gap-1">
                    <span>📥 Ekspor CSV OPD</span>
                </a>
            </div>
        </div>

        {{-- Section 2: Pemantauan Capaian ASN --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span>👥</span> Sampel Capaian Jam Pelajaran ASN
                        </h3>
                        <p class="text-[11px] text-slate-400">Daftar ASN dengan perolehan JP terkini</p>
                    </div>
                    <a href="{{ route('admin.jp.asn', request()->query()) }}" class="text-xs font-bold text-teal-700 hover:underline">
                        Lihat Semua ASN →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($asnReport['rows']->sortByDesc('total_jp')->take(5) as $asn)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ $asn['name'] }}</h4>
                                    @if($asn['achieved'])
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ✓ TUNTAS
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            -{{ $asn['remaining'] }} JP
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-slate-400 truncate">
                                    NIP: {{ $asn['nip'] }} &bull; {{ $asn['opd_name'] }}
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <div class="text-xs font-black text-slate-900">
                                    {{ $asn['total_jp'] }} <span class="text-[10px] font-bold text-slate-500">/ 20 JP</span>
                                </div>
                                <a href="{{ route('admin.jp.asn.detail', $asn['user_id']) }}" class="text-[10px] font-bold text-teal-700 hover:underline">
                                    Rincian →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Belum ada data ASN tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Menampilkan 5 ASN dengan JP tertinggi</span>
                <a href="{{ route('admin.jp.export.asn', request()->query()) }}" class="font-bold text-teal-700 hover:text-teal-900 flex items-center gap-1">
                    <span>📥 Ekspor CSV ASN</span>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
