@extends('layouts.app')

@section('title', 'Dashboard Pengelola Pelatihan OPD | ' . $opdName)

@section('content')
<div class="min-h-screen bg-slate-50/70 pb-16">

    {{-- Hero Header --}}
    <div class="bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 text-white relative overflow-hidden border-b border-blue-900/40">
        {{-- Ambient decorative glow --}}
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                {{-- Left: User & OPD Title --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/30 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                            🏛️ ADMIN PERANGKAT DAERAH (OPD)
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-blue-200 border border-white/10">
                            Unit Kerja: {{ $opdName }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Pusat Kendali Pelatihan ASN — {{ $opdName }}
                    </h1>

                    <p class="text-xs sm:text-sm text-blue-200/80 max-w-2xl leading-relaxed">
                        Kelola usulan kegiatan pembelajaran mandiri unit kerja ke BKPSDM dan pantau ketercapaian kewajiban <strong class="text-white">20 Jam Pelajaran (JP)</strong> seluruh pegawai di instansi Anda.
                    </p>

                    <div class="pt-1 flex items-center gap-4 text-xs text-blue-300/70">
                        <span>Pengelola: <strong class="text-white">{{ $user->name }}</strong></span>
                        @if($user->nip)
                            <span>• NIP: {{ $user->nip }}</span>
                        @endif
                        @if($user->jabatan)
                            <span>• {{ $user->jabatan }}</span>
                        @endif
                    </div>
                </div>

                {{-- Right: Quick Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    {{-- Switch to Learner Mode --}}
                    <a href="{{ route('dashboard.learner') }}" 
                       class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-bold text-blue-100 bg-blue-900/60 hover:bg-blue-800/80 border border-blue-700/50 rounded-xl shadow-sm transition group"
                       title="Buka portal belajar untuk melihat kursus dan sertifikat pribadi Anda sebagai ASN">
                        <span class="group-hover:rotate-12 transition-transform">🎓</span>
                        <span>Mode Belajar ASN Saya</span>
                    </a>

                    {{-- Calendar --}}
                    <a href="{{ route('calendar.index') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 text-xs font-semibold text-white/90 bg-white/10 hover:bg-white/20 border border-white/15 rounded-xl transition">
                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Kalender</span>
                    </a>

                    {{-- Primary CTA: Propose Activity --}}
                    <a href="{{ route('admin.opd-requests.create') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-900 bg-gradient-to-r from-blue-300 via-teal-300 to-emerald-300 hover:from-blue-200 hover:to-emerald-200 rounded-xl shadow-lg transition whitespace-nowrap">
                        <svg class="w-4 h-4 text-blue-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>+ Usulkan Kegiatan Baru</span>
                    </a>
                </div>

            </div>

            {{-- 4 OPD Key Metric Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mt-8">

                {{-- Card 1: Total ASN di OPD --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-blue-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Total Pegawai OPD</span>
                        <span class="text-base group-hover:scale-110 transition-transform">👥</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $opdAsnReport['total_asn'] ?? 0 }} <span class="text-xs font-semibold text-white/60">ASN</span>
                    </div>
                    <p class="text-[11px] text-blue-200/70 mt-1">
                        Pegawai terdaftar di unit kerja ini
                    </p>
                </div>

                {{-- Card 2: Kepatuhan 20 JP OPD --}}
                <div class="bg-gradient-to-br from-emerald-500/20 to-teal-600/20 backdrop-blur-md border border-emerald-400/30 p-4 rounded-2xl relative overflow-hidden group hover:border-emerald-400/50 transition shadow-sm">
                    <div class="flex items-center justify-between text-emerald-200 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Kepatuhan 20 JP Pegawai</span>
                        <span class="text-base group-hover:scale-110 transition-transform">🎯</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black tracking-tight text-emerald-300">
                            {{ $opdAsnReport['compliance_percent'] ?? 0 }}%
                        </span>
                        <span class="text-[11px] text-white/70 font-semibold">
                            ({{ $opdAsnReport['achieved_count'] ?? 0 }}/{{ $opdAsnReport['total_asn'] ?? 0 }})
                        </span>
                    </div>
                    <div class="w-full bg-white/20 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-400 to-teal-300 h-full rounded-full transition-all duration-1000" 
                             style="width: {{ min(100, $opdAsnReport['compliance_percent'] ?? 0) }}%"></div>
                    </div>
                </div>

                {{-- Card 3: Total JP Terkumpul --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-blue-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Total JP Kumulatif</span>
                        <span class="text-base group-hover:scale-110 transition-transform">⏱️</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-teal-300">
                        {{ number_format($opdAsnReport['total_jp_sum'] ?? 0) }} <span class="text-xs font-semibold text-white/60">JP</span>
                    </div>
                    <p class="text-[11px] text-blue-200/70 mt-1">
                        Rata-rata: <strong class="text-white">{{ $opdAsnReport['avg_jp'] ?? 0 }} JP</strong>/pegawai
                    </p>
                </div>

                {{-- Card 4: Usulan Kegiatan OPD --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-blue-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Usulan Kegiatan Mandiri</span>
                        <span class="text-base group-hover:scale-110 transition-transform">📋</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-amber-300">
                        {{ $requestStats['total'] }}
                    </div>
                    <p class="text-[11px] text-blue-200/70 mt-1">
                        {{ $requestStats['pending'] }} Menunggu • {{ $requestStats['approved'] }} Disetujui
                    </p>
                </div>

            </div>

        </div>
    </div>

    {{-- Main Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8" x-data="{ activeTab: 'requests' }">

        {{-- Navigation Tabs --}}
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
            <button @click="activeTab = 'requests'" 
                    :class="activeTab === 'requests' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>📋</span>
                <span>Usulan Kegiatan OPD (KAK VII)</span>
                @if($requestStats['pending'] > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-400 text-amber-950">
                        {{ $requestStats['pending'] }}
                    </span>
                @endif
            </button>

            <button @click="activeTab = 'pegawai'" 
                    :class="activeTab === 'pegawai' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>👥</span>
                <span>Monitoring Capaian 20 JP Pegawai</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-200 text-slate-700">
                    {{ $opdAsnReport['total_asn'] ?? 0 }}
                </span>
            </button>
        </div>

        {{-- TAB 1: USULAN KEGIATAN OPD (KAK PASAL VII) --}}
        <div x-show="activeTab === 'requests'" class="space-y-6">

            {{-- Info Notice KAK VII --}}
            <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200/80 text-xs text-blue-900 flex items-start gap-3">
                <span class="text-lg">ℹ️</span>
                <div class="flex-1">
                    <p class="font-bold">Mekanisme Pengajuan Kegiatan Pembelajaran oleh Perangkat Daerah (KAK Bab VII)</p>
                    <p class="text-blue-700/80 mt-0.5">
                        OPD dapat membuat dan mengusulkan kegiatan bimbingan teknis / workshop / modul pembelajaran mandiri. Kegiatan akan dipublikasikan ke kalender dan katalog Corporate University setelah memperoleh persetujuan (approval) Admin BKPSDM.
                    </p>
                </div>
                <a href="{{ route('admin.opd-requests.create') }}" 
                   class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition">
                    + Ajukan Sekarang
                </a>
            </div>

            {{-- Table Usulan Kegiatan OPD --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Daftar Usulan Kegiatan Diklat Mandiri</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Riwayat pengajuan program pelatihan {{ $opdName }}</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-500">
                        Total: {{ $opdRequests->count() }} Usulan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/70">
                            <tr>
                                <th class="py-3 px-4">Nama Kegiatan</th>
                                <th class="py-3 px-4">Metode & Durasi</th>
                                <th class="py-3 px-4">Sasaran Peserta</th>
                                <th class="py-3 px-4">Jadwal Rencana</th>
                                <th class="py-3 px-4 text-center">Status BKPSDM</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($opdRequests as $req)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $req->title }}</div>
                                        @if($req->speaker_name)
                                            <div class="text-[10px] text-slate-400 mt-0.5">Narasumber: {{ $req->speaker_name }}</div>
                                        @endif
                                        @if($req->review_notes)
                                            <div class="mt-1 p-1.5 rounded-lg bg-amber-50 text-amber-800 text-[10px] border border-amber-200/60">
                                                <strong>Catatan Reviewer:</strong> {{ $req->review_notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-md font-semibold text-[10px] uppercase {{ $req->learning_method === 'in_class' ? 'bg-amber-100 text-amber-800' : ($req->learning_method === 'hybrid' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $req->learning_method ?? 'In-Class' }}
                                        </span>
                                        <div class="font-mono text-slate-500 mt-1 font-bold">{{ $req->jp ?? 0 }} JP</div>
                                    </td>

                                    <td class="py-3.5 px-4">
                                        <div class="text-slate-700 truncate max-w-xs">{{ $req->target_audience ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Est. {{ $req->estimated_participants ?? 0 }} Peserta</div>
                                    </td>

                                    <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                        {{ $req->proposed_start_date ?? '-' }}
                                        @if($req->proposed_end_date && $req->proposed_end_date !== $req->proposed_start_date)
                                            <span class="text-slate-400">s/d</span> {{ $req->proposed_end_date }}
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        @if($req->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span>✓</span> Disetujui BKPSDM
                                            </span>
                                        @elseif($req->status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Approval
                                            </span>
                                        @elseif($req->status === 'revision')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                                <span>⚠️</span> Perlu Revisi
                                            </span>
                                        @elseif($req->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                <span>✕</span> Ditolak
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                                Draft
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('admin.opd-requests.show', $req->id) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                                            Rincian &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center">
                                        <div class="text-3xl mb-2">📋</div>
                                        <p class="font-bold text-slate-700">Belum Ada Usulan Kegiatan Diklat dari {{ $opdName }}</p>
                                        <p class="text-slate-400 text-xs mt-1">Mulai usulkan pelatihan atau workshop teknis untuk kebutuhan aparatur di unit kerja Anda.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('admin.opd-requests.create') }}" 
                                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                                                + Buat Usulan Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- TAB 2: MONITORING CAPAIAN 20 JP PEGAWAI OPD --}}
        <div x-show="activeTab === 'pegawai'" class="space-y-6" x-data="{ searchAsn: '' }">

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Pantauan Pemenuhan Target 20 JP ASN</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh pegawai {{ $opdName }} beserta perolehan jam pembelajaran</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative w-full sm:w-64">
                            <input type="text" x-model="searchAsn" placeholder="Cari nama atau NIP pegawai..." 
                                   class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <a href="{{ route('admin.jp.export.asn', ['opd_name' => $opdName, 'year' => $currentYear]) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Ekspor Excel</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/70">
                            <tr>
                                <th class="py-3 px-4">Nama Pegawai & NIP</th>
                                <th class="py-3 px-4">Jabatan</th>
                                <th class="py-3 px-4 text-center">E-Learning</th>
                                <th class="py-3 px-4 text-center">Webinar/Live</th>
                                <th class="py-3 px-4 text-center">Klasikal/Bimtek</th>
                                <th class="py-3 px-4 text-center">Eksternal</th>
                                <th class="py-3 px-4 text-right">Total JP</th>
                                <th class="py-3 px-4 w-36">Status 20 JP</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($opdAsnReport['rows'] as $asn)
                                <tr class="hover:bg-slate-50/80 transition"
                                    x-show="searchAsn === '' || '{{ strtolower($asn['name'] ?? '') }}'.includes(searchAsn.toLowerCase()) || '{{ $asn['nip'] ?? '' }}'.includes(searchAsn)">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $asn['name'] ?? 'ASN' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">NIP: {{ $asn['nip'] ?? '-' }}</div>
                                    </td>

                                    <td class="py-3.5 px-4 text-slate-700">
                                        {{ $asn['jabatan'] ?? '-' }}
                                    </td>

                                    <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-700">
                                        {{ $asn['jp_elearning'] }}
                                    </td>

                                    <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-700">
                                        {{ $asn['jp_webinar'] }}
                                    </td>

                                    <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-700">
                                        {{ $asn['jp_in_class'] }}
                                    </td>

                                    <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-700">
                                        {{ $asn['jp_external'] }}
                                    </td>

                                    <td class="py-3.5 px-4 text-right font-mono font-extrabold text-slate-900 text-sm">
                                        {{ $asn['total_jp'] }} <span class="text-[10px] text-slate-400">JP</span>
                                    </td>

                                    <td class="py-3.5 px-4">
                                        @if($asn['achieved'])
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span>✓</span> Memenuhi (≥20)
                                            </span>
                                        @else
                                            <div class="space-y-1">
                                                <div class="flex items-center justify-between text-[10px]">
                                                    <span class="text-amber-700 font-bold">{{ $asn['percentage'] }}%</span>
                                                    <span class="text-slate-400 font-mono">{{ $asn['remaining'] }} JP lagi</span>
                                                </div>
                                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, $asn['percentage']) }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('admin.jp.asn.detail', $asn['user_id']) }}" 
                                           class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 transition whitespace-nowrap">
                                            Rincian JP
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-400">
                                        Belum ada data pegawai terdaftar untuk {{ $opdName }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
