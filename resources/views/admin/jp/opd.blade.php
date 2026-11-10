@extends('layouts.admin')

@section('page_title', 'Rekapitulasi JP per OPD')

@section('content')
<div class="space-y-6">

    {{-- ── HEADER SECTION ──────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.jp.index') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                    ← Kembali ke Dashboard JP
                </a>
                <span class="text-slate-400">•</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase">
                    Rekap Unit Kerja
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Rekapitulasi Jam Pelajaran (JP) per Perangkat Daerah (OPD)
            </h1>
            <p class="text-xs text-slate-500">
                Peringkat evaluasi kepatuhan pemenuhan minimal 20 JP ASN antar instansi / perangkat daerah di lingkungan pemerintah daerah.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.jp.export.opd', request()->query()) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm flex items-center gap-1.5">
                <span>📥 Ekspor CSV Excel</span>
            </a>
            <a href="{{ route('admin.jp.print', array_merge(request()->query(), ['type' => 'opd'])) }}" target="_blank"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition shadow-sm flex items-center gap-1.5">
                <span>🖨️ Cetak Resmi</span>
            </a>
        </div>
    </div>

    {{-- ── TAB NAVIGATION ──────────────────────────────────────── --}}
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.jp.index', request()->query()) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
            📊 Dashboard JP
        </a>
        <a href="{{ route('admin.jp.asn', request()->query()) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
            👥 Rekap per ASN
        </a>
        <a href="{{ route('admin.jp.opd', request()->query()) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white shadow-sm flex items-center gap-1.5">
            <span>🏛️ Rekap per OPD</span>
            <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-[10px] font-mono text-purple-300">
                {{ $report['total_opd'] }}
            </span>
        </a>
    </div>

    {{-- ── FILTER CARD ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.jp.opd') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
            {{-- Search OPD --}}
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Cari Nama Perangkat Daerah</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Dinas, Badan, Sekretariat, Inspektorat..."
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>

            {{-- Tahun --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Tahun</label>
                <select name="year" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                    @php $currY = (int) date('Y'); @endphp
                    @for($y = $currY; $y >= $currY - 4; $y--)
                        <option value="{{ $y }}" {{ ($filters['year'] ?? $currY) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- Sub-Periode --}}
            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Sub-Periode</label>
                    <select name="period_type" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                        <option value="all" {{ ($filters['period_type'] ?? 'all') === 'all' ? 'selected' : '' }}>Setahun Penuh</option>
                        <option value="s1" {{ ($filters['period_type'] ?? '') === 's1' ? 'selected' : '' }}>Semester 1 (Jan–Jun)</option>
                        <option value="s2" {{ ($filters['period_type'] ?? '') === 's2' ? 'selected' : '' }}>Semester 2 (Jul–Des)</option>
                        <option value="q1" {{ ($filters['period_type'] ?? '') === 'q1' ? 'selected' : '' }}>TW 1 (Jan–Mar)</option>
                        <option value="q2" {{ ($filters['period_type'] ?? '') === 'q2' ? 'selected' : '' }}>TW 2 (Apr–Jun)</option>
                        <option value="q3" {{ ($filters['period_type'] ?? '') === 'q3' ? 'selected' : '' }}>TW 3 (Jul–Sep)</option>
                        <option value="q4" {{ ($filters['period_type'] ?? '') === 'q4' ? 'selected' : '' }}>TW 4 (Okt–Des)</option>
                    </select>
                </div>
                <div class="pt-5">
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white transition shadow-sm">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ── SUMMARY KPI STRIP ───────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Perangkat Daerah</span>
            <p class="text-xl font-black text-slate-900 mt-1">{{ number_format($report['total_opd']) }} OPD</p>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Akumulasi JP OPD</span>
            <p class="text-xl font-black text-purple-700 mt-1">{{ number_format($report['grand_total_jp']) }} JP</p>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata Kepatuhan Pemda</span>
            <p class="text-xl font-black text-emerald-600 mt-1">{{ $report['grand_compliance'] }}%</p>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total ASN Tuntas 20 JP</span>
            <p class="text-xl font-black text-teal-700 mt-1">{{ $report['grand_achieved'] }}/{{ $report['grand_total_asn'] }} ASN</p>
        </div>
    </div>

    {{-- ── TABLE REKAPITULASI PER OPD (KAK POIN 5) ─────────────── --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    Peringkat Kepatuhan JP Antar Perangkat Daerah
                </h2>
                <p class="text-xs text-slate-400">
                    Diurutkan berdasarkan persentase ASN di OPD yang telah memenuhi ketentuan wajib minimal 20 JP
                </p>
            </div>
            <span class="text-xs font-semibold text-slate-500">
                Periode: <strong class="text-slate-800">{{ $report['range']['label'] }}</strong>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-3 text-center w-12">Rank</th>
                        <th class="py-3 px-4 min-w-[240px]">Perangkat Daerah (OPD)</th>
                        <th class="py-3 px-3 text-center">Jumlah ASN</th>
                        <th class="py-3 px-3 text-center bg-purple-50/70 font-black text-purple-900">Total JP OPD</th>
                        <th class="py-3 px-3 text-center">Rata-rata JP</th>
                        <th class="py-3 px-3 text-center">ASN Tuntas (>=20 JP)</th>
                        <th class="py-3 px-3 text-center">ASN Belum Tuntas</th>
                        <th class="py-3 px-3 text-center min-w-[140px]">Tingkat Kepatuhan</th>
                        <th class="py-3 px-3 text-center">Predikat</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($report['rows'] as $index => $row)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 text-center">
                                <span class="w-6 h-6 rounded-full {{ $index === 0 ? 'bg-amber-400 text-slate-900 font-black' : ($index === 1 ? 'bg-slate-300 text-slate-800 font-bold' : ($index === 2 ? 'bg-amber-200 text-amber-900 font-bold' : 'bg-slate-100 text-slate-500')) }} text-[11px] inline-flex items-center justify-center shadow-2xs">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block text-xs">
                                    {{ $row['opd_name'] }}
                                </span>
                                <div class="flex items-center gap-1.5 text-[9px] text-slate-400 mt-0.5">
                                    <span>💻 {{ $row['type_sums']['elearning'] }} JP</span>
                                    <span>•</span>
                                    <span>📡 {{ $row['type_sums']['webinar'] }} JP</span>
                                    <span>•</span>
                                    <span>🏛️ {{ $row['type_sums']['in_class'] }} JP</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-slate-700">
                                {{ $row['total_asn'] }} Pegawai
                            </td>
                            <td class="py-3 px-3 text-center bg-purple-50/40">
                                <span class="text-sm font-black text-purple-950">
                                    {{ number_format($row['total_jp']) }}
                                </span>
                                <span class="text-[9px] text-purple-700 block font-bold">JP</span>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-slate-800">
                                {{ $row['avg_jp'] }} JP/ASN
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-emerald-700">
                                {{ $row['achieved_count'] }} ASN
                            </td>
                            <td class="py-3 px-3 text-center font-bold {{ $row['not_achieved_count'] > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                                {{ $row['not_achieved_count'] }} ASN
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-between text-[10px] font-semibold text-slate-600 mb-1">
                                    <span>{{ $row['compliance_rate'] }}%</span>
                                    <span class="text-slate-400 font-mono">{{ $row['achieved_count'] }}/{{ $row['total_asn'] }}</span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $row['compliance_rate'] >= 80 ? 'bg-emerald-500' : ($row['compliance_rate'] >= 60 ? 'bg-blue-500' : ($row['compliance_rate'] >= 40 ? 'bg-amber-500' : 'bg-rose-500')) }} rounded-full"
                                         style="width: {{ $row['compliance_rate'] }}%"></div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $row['predicate_badge'] }} shadow-2xs whitespace-nowrap">
                                    {{ $row['predicate'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('admin.jp.asn', ['opd_name' => $row['opd_name'], 'year' => $filters['year'] ?? date('Y'), 'period_type' => $filters['period_type'] ?? 'all']) }}"
                                   class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-purple-600 hover:text-white text-slate-700 transition shadow-2xs inline-flex items-center gap-1">
                                    <span>👥 Lihat ASN</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada data perangkat daerah yang cocok dengan kata kunci pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
