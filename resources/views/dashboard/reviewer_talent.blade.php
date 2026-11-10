@extends('layouts.app')

@section('title', 'Dashboard Reviewer Talenta & IDP ASN | ' . $user->name)

@section('content')
<div class="min-h-screen bg-slate-50/70 pb-16">

    {{-- Hero Header --}}
    <div class="bg-gradient-to-r from-slate-950 via-teal-950 to-slate-900 text-white relative overflow-hidden border-b border-teal-900/40">
        {{-- Ambient decorative glow --}}
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#14b8a6_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                {{-- Left: User & Title --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-500/30 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                            🔍 REVIEWER MANAJEMEN TALENTA & IDP
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-teal-200 border border-white/10">
                            Bidang Pengembangan Karier & Talenta BKPSDM
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Pusat Evaluasi Rencana Pengembangan (IDP) & Talenta ASN
                    </h1>

                    <p class="text-xs sm:text-sm text-teal-200/80 max-w-2xl leading-relaxed">
                        Monitoring keselarasan rencana pengembangan kompetensi individu (Individual Development Plan) dengan kurikulum kursus Corporate University Kabupaten Berau.
                    </p>

                    <div class="pt-1 flex items-center gap-4 text-xs text-teal-300/70">
                        <span>Reviewer: <strong class="text-white">{{ $user->name }}</strong></span>
                        @if($user->jabatan)
                            <span>• {{ $user->jabatan }}</span>
                        @endif
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    {{-- Switch to Learner Mode --}}
                    <a href="{{ route('dashboard.learner') }}" 
                       class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-bold text-teal-100 bg-teal-900/60 hover:bg-teal-800/80 border border-teal-700/50 rounded-xl shadow-sm transition group"
                       title="Buka portal belajar untuk melihat kursus dan sertifikat pribadi Anda sebagai ASN">
                        <span class="group-hover:rotate-12 transition-transform">🎓</span>
                        <span>Mode Belajar ASN Saya</span>
                    </a>

                    {{-- Manage IDP --}}
                    <a href="{{ route('admin.idp.index') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-900 bg-gradient-to-r from-teal-300 via-cyan-300 to-emerald-300 hover:from-teal-200 hover:to-emerald-200 rounded-xl shadow-lg transition whitespace-nowrap">
                        <svg class="w-4 h-4 text-teal-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Kelola Master IDP & Impor</span>
                    </a>
                </div>

            </div>

            {{-- 4 Talent & Competency Key Metric Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mt-8">

                {{-- Card 1: Total IDP Plans --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-teal-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Total Rencana IDP</span>
                        <span class="text-base group-hover:scale-110 transition-transform">📑</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ number_format($totalIdp) }}
                    </div>
                    <p class="text-[11px] text-teal-200/70 mt-1">
                        Butir rencana kompetensi ASN
                    </p>
                </div>

                {{-- Card 2: ASN Terpetakan --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-teal-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">ASN Memiliki IDP</span>
                        <span class="text-base group-hover:scale-110 transition-transform">👥</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-teal-300">
                        {{ number_format($totalAsnWithIdp) }} <span class="text-xs font-semibold text-white/60">ASN</span>
                    </div>
                    <p class="text-[11px] text-teal-200/70 mt-1">
                        Telah menyusun IDP tahunan
                    </p>
                </div>

                {{-- Card 3: Kursus Tersedia --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-teal-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Katalog Kursus Corpu</span>
                        <span class="text-base group-hover:scale-110 transition-transform">📚</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-amber-300">
                        {{ $publishedCourses->count() }} <span class="text-xs font-semibold text-white/60">Kursus</span>
                    </div>
                    <p class="text-[11px] text-teal-200/70 mt-1">
                        Siap memfasilitasi kebutuhan IDP
                    </p>
                </div>

                {{-- Card 4: OPD Terpetakan --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-teal-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Perangkat Daerah</span>
                        <span class="text-base group-hover:scale-110 transition-transform">🏛️</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $opdIdpSummary->count() }}
                    </div>
                    <p class="text-[11px] text-teal-200/70 mt-1">
                        OPD telah menginput data talenta
                    </p>
                </div>

            </div>

        </div>
    </div>

    {{-- Main Body --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

        {{-- Row 1: Rekapitulasi IDP per OPD --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🏛️</span> Pemetaan Kebutuhan Pembelajaran IDP per Perangkat Daerah
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Sebaran rencana pengembangan kompetensi ASN berdasarkan instansi</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($opdIdpSummary as $sum)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <h3 class="font-bold text-slate-900 text-xs truncate">{{ $sum['opd_name'] }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 shrink-0">
                                {{ $sum['asn_count'] }} ASN
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">
                            <strong>{{ $sum['item_count'] }}</strong> Rencana materi pengembangan didaftarkan.
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 col-span-3 text-center">Belum ada data IDP per OPD.</p>
                @endforelse
            </div>
        </div>

        {{-- Row 2: Tabel Rencana IDP ASN Terbaru & Status Matching Kursus --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden" x-data="{ searchIdp: '' }">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>📋</span> Daftar Rencana Pengembangan Individu (IDP) ASN
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Analisis kesesuaian materi IDP dengan ketersediaan kursus di platform Corporate University</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchIdp" placeholder="Cari nama, NIP, atau topik..." 
                               class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <a href="{{ route('admin.idp.index') }}" 
                       class="shrink-0 px-3 py-1.5 rounded-xl text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition">
                        Kelola Semua &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/70">
                        <tr>
                            <th class="py-3 px-4">Nama ASN & NIP</th>
                            <th class="py-3 px-4">Tahun</th>
                            <th class="py-3 px-4">Topik / Rencana Materi Pembelajaran</th>
                            <th class="py-3 px-4">Batch IDP</th>
                            <th class="py-3 px-4 text-center">Status Keselarasan Katalog</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($idpPlans as $plan)
                            @php
                                $matched = $plan->matchedCourse();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition"
                                x-show="searchIdp === '' || '{{ strtolower($plan->nama_asn) }}'.includes(searchIdp.toLowerCase()) || '{{ $plan->nip }}'.includes(searchIdp) || '{{ strtolower($plan->judul_materi) }}'.includes(searchIdp.toLowerCase())">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $plan->nama_asn }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">NIP: {{ $plan->nip }}</div>
                                </td>

                                <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                    {{ $plan->tahun }}
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800 text-xs">{{ $plan->judul_materi }}</div>
                                </td>

                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">
                                    {{ $plan->batch_id ?? '-' }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if($matched)
                                        <a href="{{ route('courses.show', $matched->slug) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200 transition">
                                            <span>✓</span> Kursus Tersedia: {{ Str::limit($matched->title, 25) }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span>⏳</span> Perlu Pengadaan Silabus Baru
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Belum ada data rencana pengembangan IDP yang diunggah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($idpPlans->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $idpPlans->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
