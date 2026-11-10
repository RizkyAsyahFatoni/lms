@extends('layouts.admin')

@section('page_title', 'Rekapitulasi JP per ASN')

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
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200 uppercase">
                    Rekap Pegawai
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Rekapitulasi Jam Pelajaran (JP) per ASN
            </h1>
            <p class="text-xs text-slate-500">
                Pencatatan akumulasi JP individual aparatur sipil negara terhubung dengan kegiatan internal, webinar, klasikal, dan diklat eksternal.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.jp.export.asn', request()->query()) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm flex items-center gap-1.5">
                <span>📥 Ekspor CSV Excel</span>
            </a>
            <a href="{{ route('admin.jp.print', array_merge(request()->query(), ['type' => 'asn'])) }}" target="_blank"
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
           class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white shadow-sm flex items-center gap-1.5">
            <span>👥 Rekap per ASN</span>
            <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-[10px] font-mono text-teal-300">
                {{ $report['total_asn'] }}
            </span>
        </a>
        <a href="{{ route('admin.jp.opd', request()->query()) }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
            🏛️ Rekap per OPD
        </a>
    </div>

    {{-- ── FILTER BOX ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.jp.asn') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                {{-- Search --}}
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Cari ASN / NIP</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama ASN, NIP, atau OPD..."
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                {{-- Tahun --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                        @php $currY = (int) date('Y'); @endphp
                        @for($y = $currY; $y >= $currY - 4; $y--)
                            <option value="{{ $y }}" {{ ($filters['year'] ?? $currY) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Periode --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Periode</label>
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

                {{-- OPD --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Perangkat Daerah</label>
                    <select name="opd_name" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                        <option value="all">Semua OPD</option>
                        @foreach($report['available_opds'] as $opd)
                            <option value="{{ $opd }}" {{ ($filters['opd_name'] ?? '') === $opd ? 'selected' : '' }}>{{ $opd }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Kepatuhan --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Status Target 20 JP</label>
                    <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold text-slate-800">
                        <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="achieved" {{ ($filters['status'] ?? '') === 'achieved' ? 'selected' : '' }}>Memenuhi (>= 20 JP)</option>
                        <option value="not_achieved" {{ ($filters['status'] ?? '') === 'not_achieved' ? 'selected' : '' }}>Belum Memenuhi (&lt; 20 JP)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Filter Metode:</span>
                    <button type="submit" name="learning_type" value="all"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ ($filters['learning_type'] ?? 'all') === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Metode
                    </button>
                    @foreach($learningTypes as $k => $t)
                        <button type="submit" name="learning_type" value="{{ $k }}"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ ($filters['learning_type'] ?? '') === $k ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $t['icon'] }} {{ $t['short_label'] }}
                        </button>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white transition shadow-sm">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.jp.asn') }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── SUMMARY KPI STRIP ───────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total ASN Terdaftar</span>
            <p class="text-xl font-black text-slate-900 mt-1">{{ number_format($report['total_asn']) }} ASN</p>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Akumulasi JP</span>
            <p class="text-xl font-black text-teal-700 mt-1">{{ number_format($report['total_jp_sum']) }} JP</p>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Memenuhi Target (>= 20 JP)</span>
            <p class="text-xl font-black text-emerald-600 mt-1">{{ $report['achieved_count'] }} ASN ({{ $report['compliance_percent'] }}%)</p>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Belum Memenuhi (&lt; 20 JP)</span>
            <p class="text-xl font-black text-amber-600 mt-1">{{ $report['not_achieved_count'] }} ASN</p>
        </div>
    </div>

    {{-- ── TABLE REKAPITULASI PER ASN (KAK POIN 4 & 8) ──────────── --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    Daftar Capaian Jam Pelajaran ASN
                </h2>
                <p class="text-xs text-slate-400">
                    Rekapitulasi otomatis berdasarkan riwayat pembelajaran internal, webinar, klasikal, dan diklat eksternal
                </p>
            </div>
            <span class="text-xs font-semibold text-slate-500">
                Menampilkan <strong>{{ $report['rows']->count() }}</strong> pegawai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-3 text-center w-10">No</th>
                        <th class="py-3 px-4 min-w-[200px]">Nama Pegawai / NIP</th>
                        <th class="py-3 px-4 min-w-[180px]">Perangkat Daerah (OPD)</th>
                        <th class="py-3 px-2 text-center" title="E-Learning Mandiri">💻 E-Learn</th>
                        <th class="py-3 px-2 text-center" title="Webinar & Live Session">📡 Webinar</th>
                        <th class="py-3 px-2 text-center" title="Klasikal / Bimtek">🏛️ Klasikal</th>
                        <th class="py-3 px-2 text-center" title="Hybrid Learning">🔄 Hybrid</th>
                        <th class="py-3 px-2 text-center" title="Diklat Eksternal">📜 Eksternal</th>
                        <th class="py-3 px-3 text-center bg-teal-50/70 font-black text-teal-900 min-w-[90px]">Total JP</th>
                        <th class="py-3 px-3 text-center min-w-[120px]">Target & Capaian</th>
                        <th class="py-3 px-3 text-center min-w-[120px]">Status Kepatuhan</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($report['rows'] as $index => $row)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 text-center font-mono text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.jp.asn.detail', $row['user_id']) }}" class="font-bold text-slate-900 hover:text-teal-700 transition block truncate">
                                    {{ $row['name'] }}
                                </a>
                                <div class="text-[10px] font-mono text-slate-500 mt-0.5">
                                    NIP: {{ $row['nip'] }}
                                </div>
                                @if($row['jabatan'] && $row['jabatan'] !== '-')
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs">
                                        {{ $row['jabatan'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800 line-clamp-2">
                                    {{ $row['opd_name'] }}
                                </div>
                                @if($row['golongan'] && $row['golongan'] !== '-')
                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-mono inline-block mt-0.5">
                                        {{ $row['golongan'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-center font-bold {{ $row['jp_elearning'] > 0 ? 'text-indigo-700 bg-indigo-50/40' : 'text-slate-300' }}">
                                {{ $row['jp_elearning'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold {{ $row['jp_webinar'] > 0 ? 'text-teal-700 bg-teal-50/40' : 'text-slate-300' }}">
                                {{ $row['jp_webinar'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold {{ $row['jp_in_class'] > 0 ? 'text-amber-700 bg-amber-50/40' : 'text-slate-300' }}">
                                {{ $row['jp_in_class'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold {{ $row['jp_hybrid'] > 0 ? 'text-purple-700 bg-purple-50/40' : 'text-slate-300' }}">
                                {{ $row['jp_hybrid'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold {{ $row['jp_external'] > 0 ? 'text-blue-700 bg-blue-50/40' : 'text-slate-300' }}">
                                {{ $row['jp_external'] ?: '-' }}
                            </td>
                            <td class="py-3 px-3 text-center bg-teal-50/50">
                                <div class="text-sm font-black text-slate-900">
                                    {{ $row['total_jp'] }}
                                </div>
                                <div class="text-[9px] text-teal-700 font-bold uppercase">Jam</div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-between text-[10px] font-semibold text-slate-600 mb-1">
                                    <span>{{ $row['total_jp'] }}/{{ $row['target_jp'] }} JP</span>
                                    <span class="font-bold {{ $row['achieved'] ? 'text-emerald-700' : 'text-amber-700' }}">{{ $row['percentage'] }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $row['achieved'] ? 'bg-emerald-500' : 'bg-amber-500' }} rounded-full"
                                         style="width: {{ min(100, $row['percentage']) }}%"></div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($row['achieved'])
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1 shadow-2xs">
                                        <span>✓</span> Memenuhi Target
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1 shadow-2xs">
                                        <span>⚠️</span> Kurang {{ $row['remaining'] }} JP
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('admin.jp.asn.detail', $row['user_id']) }}"
                                   class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-teal-600 hover:text-white text-slate-700 transition shadow-2xs inline-flex items-center gap-1">
                                    <span>🔍 Rincian</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada data ASN yang sesuai dengan kriteria filter pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
