@extends('layouts.admin')

@section('page_title', 'Detail Riwayat JP ASN: ' . $targetUser->name)

@section('content')
<div class="space-y-6">

    {{-- ── HEADER & BREADCRUMB ─────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.jp.asn') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                    ← Kembali ke Rekap ASN
                </a>
                <span class="text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Buku Catatan Pembelajaran ASN (Learning Record)</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Rincian Akumulasi JP: {{ $targetUser->name }}
            </h1>
            <p class="text-xs text-slate-500">
                Transkrip resmi riwayat pengembangan kompetensi per kegiatan sesuai Keputusan LAN RI & KAK LMS.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.jp.print', ['type' => 'asn', 'search' => $targetUser->nip ?: $targetUser->name, 'year' => $year]) }}" target="_blank"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition shadow-sm flex items-center gap-1.5">
                <span>🖨️ Cetak Transkrip JP</span>
            </a>
            <a href="{{ route('admin.users.show', $targetUser->id) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                Profil Akun
            </a>
        </div>
    </div>

    {{-- ── ASN IDENTITY HERO CARD ──────────────────────────────── --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-teal-700 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shrink-0 shadow-md">
                    {{ strtoupper(substr($targetUser->name, 0, 2)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-black text-slate-900">{{ $targetUser->name }}</h2>
                        @if($summary['achieved'])
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                ✓ Target 20 JP Tercapai
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                                ⚠️ Kurang {{ $summary['remaining'] }} JP
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                        <span>NIP: <strong class="text-slate-700 font-mono">{{ $targetUser->nip ?: '-' }}</strong></span>
                        <span>•</span>
                        <span>OPD: <strong class="text-slate-700">{{ $summary['opd_name'] }}</strong></span>
                        @if($targetUser->jabatan)
                            <span>•</span>
                            <span>Jabatan: <strong class="text-slate-700">{{ $targetUser->jabatan }}</strong></span>
                        @endif
                        @if($targetUser->pangkat_golongan)
                            <span>•</span>
                            <span>Gol: <strong class="text-slate-700 font-mono">{{ $targetUser->pangkat_golongan }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Period Filter for this ASN --}}
            <form method="GET" action="{{ route('admin.jp.asn.detail', $targetUser->id) }}" class="flex items-center gap-2 shrink-0">
                <select name="year" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700">
                    @php $currY = (int) date('Y'); @endphp
                    @for($y = $currY; $y >= $currY - 4; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
                <select name="period_type" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-700">
                    <option value="all" {{ $periodType === 'all' ? 'selected' : '' }}>Setahun Penuh</option>
                    <option value="s1" {{ $periodType === 's1' ? 'selected' : '' }}>Semester 1</option>
                    <option value="s2" {{ $periodType === 's2' ? 'selected' : '' }}>Semester 2</option>
                    <option value="q1" {{ $periodType === 'q1' ? 'selected' : '' }}>Triwulan 1</option>
                    <option value="q2" {{ $periodType === 'q2' ? 'selected' : '' }}>Triwulan 2</option>
                    <option value="q3" {{ $periodType === 'q3' ? 'selected' : '' }}>Triwulan 3</option>
                    <option value="q4" {{ $periodType === 'q4' ? 'selected' : '' }}>Triwulan 4</option>
                </select>
                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white transition shadow-sm">
                    Ganti
                </button>
            </form>
        </div>
    </div>

    {{-- ── 4 STAT CARDS ────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Akumulasi JP</span>
            <div class="flex items-baseline gap-1 mt-1">
                <p class="text-3xl font-black text-slate-900">{{ $summary['total_jp'] }}</p>
                <span class="text-xs font-bold text-teal-700">JP</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Periode {{ $summary['period_label'] }}</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Target Tahunan KAK</span>
            <div class="flex items-baseline gap-1 mt-1">
                <p class="text-3xl font-black text-slate-900">{{ $summary['target_jp'] }}</p>
                <span class="text-xs font-bold text-slate-500">JP / tahun</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Kewajiban regulasi LAN RI</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Persentase Capaian</span>
            <div class="flex items-baseline gap-1 mt-1">
                <p class="text-3xl font-black {{ $summary['achieved'] ? 'text-emerald-600' : 'text-amber-600' }}">{{ $summary['percentage'] }}%</p>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">{{ $summary['achieved'] ? 'Target terpenuhi sempurna' : 'Memerlukan tambahan JP' }}</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kekurangan Jam Pelajaran</span>
            <div class="flex items-baseline gap-1 mt-1">
                <p class="text-3xl font-black {{ $summary['remaining'] == 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $summary['remaining'] }}
                </p>
                <span class="text-xs font-bold text-slate-500">JP tersisa</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">
                {{ $summary['remaining'] == 0 ? 'Tidak ada kekurangan' : 'Harus dipenuhi sebelum akhir tahun' }}
            </p>
        </div>
    </div>

    {{-- ── MULTI-METHOD VISUAL BREAKDOWN BAR ────────────────────── --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                    Komposisi JP Berdasarkan Jenis Pembelajaran (KAK Poin 7)
                </h3>
                <p class="text-[11px] text-slate-400">Persebaran jam pelajaran yang diperoleh dari 5 metode pembelajaran</p>
            </div>
            <div class="text-xs font-bold text-slate-700">
                Total: {{ $summary['total_jp'] }} JP
            </div>
        </div>

        {{-- Progress segment bar --}}
        <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden flex">
            @php
                $tot = max(1, $summary['total_jp']);
                $pEle = round(($summary['jp_elearning'] / $tot) * 100);
                $pWeb = round(($summary['jp_webinar'] / $tot) * 100);
                $pInc = round(($summary['jp_in_class'] / $tot) * 100);
                $pHyb = round(($summary['jp_hybrid'] / $tot) * 100);
                $pExt = round(($summary['jp_external'] / $tot) * 100);
            @endphp
            @if($summary['jp_elearning'] > 0)
                <div class="h-full bg-indigo-500" style="width: {{ $pEle }}%" title="E-Learning: {{ $summary['jp_elearning'] }} JP"></div>
            @endif
            @if($summary['jp_webinar'] > 0)
                <div class="h-full bg-teal-500" style="width: {{ $pWeb }}%" title="Webinar: {{ $summary['jp_webinar'] }} JP"></div>
            @endif
            @if($summary['jp_in_class'] > 0)
                <div class="h-full bg-amber-500" style="width: {{ $pInc }}%" title="Klasikal: {{ $summary['jp_in_class'] }} JP"></div>
            @endif
            @if($summary['jp_hybrid'] > 0)
                <div class="h-full bg-purple-500" style="width: {{ $pHyb }}%" title="Hybrid: {{ $summary['jp_hybrid'] }} JP"></div>
            @endif
            @if($summary['jp_external'] > 0)
                <div class="h-full bg-blue-500" style="width: {{ $pExt }}%" title="Eksternal: {{ $summary['jp_external'] }} JP"></div>
            @endif
        </div>

        {{-- Legend pills --}}
        <div class="flex flex-wrap items-center gap-3 pt-1 text-xs">
            <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <span>💻 E-Learning: <strong>{{ $summary['jp_elearning'] }} JP</strong></span>
            </span>
            <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                <span>📡 Webinar: <strong>{{ $summary['jp_webinar'] }} JP</strong></span>
            </span>
            <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span>🏛️ Klasikal/Bimtek: <strong>{{ $summary['jp_in_class'] }} JP</strong></span>
            </span>
            <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                <span>🔄 Hybrid: <strong>{{ $summary['jp_hybrid'] }} JP</strong></span>
            </span>
            <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span>📜 Eksternal: <strong>{{ $summary['jp_external'] }} JP</strong></span>
            </span>
        </div>
    </div>

    {{-- ── ITEMIZED ACTIVITIES TABLE (KAK POIN 1 & 2) ───────────── --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900">
                    Riwayat Pembelajaran & Perolehan Jam Pelajaran (Itemized Log)
                </h3>
                <p class="text-xs text-slate-400">
                    Daftar kegiatan pembelajaran yang telah diselesaikan dan menghasilkan jam pelajaran
                </p>
            </div>
            <span class="text-xs font-semibold text-slate-500">
                {{ $activities->count() }} kegiatan tercatat
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-3 text-center w-10">No</th>
                        <th class="py-3 px-4 min-w-[130px]">Tanggal</th>
                        <th class="py-3 px-4 min-w-[280px]">Nama Kegiatan / Pelatihan</th>
                        <th class="py-3 px-3 min-w-[150px]">Penyelenggara / Narasumber</th>
                        <th class="py-3 px-3 text-center min-w-[140px]">Jenis Pembelajaran</th>
                        <th class="py-3 px-3 text-center bg-teal-50/70 font-black text-teal-900 min-w-[90px]">Bobot JP</th>
                        <th class="py-3 px-4 min-w-[150px]">No. Sertifikat / Dokumen</th>
                        <th class="py-3 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activities as $index => $act)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3 text-center font-mono text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                                {{ $act['date']->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block leading-tight">
                                    {{ $act['title'] }}
                                </span>
                                <span class="text-[10px] text-slate-400 mt-0.5 inline-block">
                                    Kategori: {{ $act['category'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-600">
                                {{ $act['organizer'] }}
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $act['type_info']['badge_color'] }} inline-flex items-center gap-1 shadow-2xs whitespace-nowrap">
                                    <span>{{ $act['type_info']['icon'] }}</span>
                                    <span>{{ $act['type_info']['short_label'] }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center bg-teal-50/50">
                                <span class="text-sm font-black text-teal-950">
                                    +{{ $act['jp'] }}
                                </span>
                                <span class="text-[9px] text-teal-700 block font-bold">JP</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">
                                @if($act['certificate_no'])
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-bold border border-slate-200">
                                        {{ $act['certificate_no'] }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[10px]">Tervalidasi Sistem</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $act['badge_status'] }}">
                                    {{ $act['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                Belum ada riwayat pembelajaran yang diselesaikan oleh ASN ini pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
