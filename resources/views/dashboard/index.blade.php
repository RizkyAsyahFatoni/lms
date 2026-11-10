@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] pb-16" x-data="{ 
    activeTab: '{{ request('tab', 'dashboard') }}',
    filterPelatihan: 'all',
    filterIdpTab: 'all',
    photoPreview: null,
    showModalEksternal: {{ ($errors->any() && (old('training_name') || old('jp_count'))) ? 'true' : 'false' }},
    certFileName: '',
    supportFileName: '',
    externalTrainingTitle: '{{ old('training_name', '') }}',
    openExternalSubmission(title) {
        this.externalTrainingTitle = title;
        this.activeTab = 'eksternal';
        this.showModalEksternal = true;
    }
}">

    {{-- HEADER BANNER --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        
        @if($user->isPimpinan() || $user->isAdminOpd() || $user->isNarasumber() || $user->isReviewerTalent() || $user->isSuperAdmin())
            <div class="mb-4 px-5 py-2.5 rounded-2xl border border-slate-200 bg-white shadow-xs flex items-center justify-between gap-3 flex-wrap text-xs">
                <span class="text-slate-700 flex items-center gap-1.5 font-semibold">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Akses Khusus Manajemen / Pengajar:</span>
                </span>
                <div class="flex items-center gap-2 flex-wrap">
                    @if($user->isPimpinan())
                        <a href="{{ route('dashboard.pimpinan') }}" class="px-3 py-1 rounded-lg font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">Dashboard Eksekutif</a>
                    @elseif($user->isAdminOpd())
                        <a href="{{ route('dashboard.admin_opd') }}" class="px-3 py-1 rounded-lg font-bold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">Dashboard OPD</a>
                    @elseif($user->isNarasumber())
                        <a href="{{ route('dashboard.narasumber') }}" class="px-3 py-1 rounded-lg font-bold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition">Panel Pengajar</a>
                    @elseif($user->isReviewerTalent())
                        <a href="{{ route('dashboard.reviewer_talent') }}" class="px-3 py-1 rounded-lg font-bold bg-teal-50 text-teal-700 border border-teal-200 hover:bg-teal-100 transition">Panel Talenta</a>
                    @elseif($user->isSuperAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1 rounded-lg font-bold bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition">Konsol Admin</a>
                    @endif
                </div>
            </div>
        @endif

        {{-- HERO BANNER: Foto + Nama Besar dengan Gradasi Kaya & Gambar Transparan Orang Belajar di Depan Laptop --}}
        <div class="relative rounded-3xl shadow-xl overflow-hidden text-white border border-blue-800/60"
             style="background: radial-gradient(circle at 75% 45%, rgba(14, 165, 233, 0.25) 0%, rgba(20, 184, 166, 0.15) 35%, transparent 68%), linear-gradient(130deg, #071527 0%, #0d2745 35%, #103c64 70%, #0a4655 100%);">
            
            {{-- Background Ambient Glow Lighting --}}
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/4 -bottom-16 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Ornamen Vektor Transparan Sisi Kiri: Pola Grid Pembelajaran Digital --}}
            <div class="absolute left-0 top-0 bottom-0 w-52 pointer-events-none opacity-[0.07] overflow-hidden select-none">
                <svg class="w-full h-full text-white" viewBox="0 0 220 300" fill="currentColor">
                    <defs>
                        <pattern id="dot-learning" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="3" cy="3" r="1.5" fill="currentColor" />
                        </pattern>
                    </defs>
                    <rect width="220" height="300" fill="url(#dot-learning)" />
                    <path d="M20,60 L80,120 L160,90 L200,160" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4" opacity="0.6"/>
                    <circle cx="20" cy="60" r="4" fill="currentColor" />
                    <circle cx="80" cy="120" r="5" fill="currentColor" />
                    <circle cx="160" cy="90" r="4" fill="currentColor" />
                    <circle cx="200" cy="160" r="6" fill="currentColor" />
                </svg>
            </div>

            {{-- GAMBAR ILUSTRASI: Layar Laptop E-Course Murni (Tanpa Orang, Tanpa Topi Toga, Tanpa Medali) --}}
            <div class="absolute right-0 top-0 bottom-0 w-full sm:w-[68%] lg:w-[60%] pointer-events-none select-none overflow-hidden flex items-center justify-end">
                <img src="{{ asset('images/banner-laptop-pure-ecourse.jpg') }}" 
                     alt="Layar Laptop E-Course" 
                     class="w-full h-full object-cover object-[80%_center] opacity-45 mix-blend-screen transition-opacity duration-300"
                     style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 20%, rgba(0,0,0,0.75) 50%, rgba(0,0,0,0.9) 80%); mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 20%, rgba(0,0,0,0.75) 50%, rgba(0,0,0,0.9) 80%);">
            </div>

            {{-- Subtle Gradient Overlay on Left to preserve maximum text contrast --}}
            <div class="absolute inset-y-0 left-0 w-full sm:w-1/2 bg-gradient-to-r from-[#071527] via-[#071527]/90 to-transparent pointer-events-none"></div>

            <div class="relative z-10 px-7 sm:px-10 py-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                
                {{-- Foto Profil + Nama Besar --}}
                <div class="flex items-center gap-5">
                    <div class="relative shrink-0 group cursor-pointer" @click="activeTab = 'profil'">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 rounded-full border-[3px] border-white/80 shadow-2xl overflow-hidden bg-gradient-to-tr from-blue-700 via-blue-500 to-teal-400 flex items-center justify-center">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <span class="hidden w-full h-full items-center justify-center text-white font-black text-2xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            @else
                                <span class="text-white font-black text-2xl lg:text-3xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="text-white text-[10px] font-bold">✎ Edit</span>
                        </div>
                        {{-- Indikator kamera kecil --}}
                        <div class="absolute -bottom-0.5 -right-0.5 w-7 h-7 rounded-full bg-white/20 backdrop-blur border border-white/40 flex items-center justify-center text-white shadow-md">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>

                    <div class="space-y-1.5 min-w-0">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight tracking-tight drop-shadow-xs">
                            {{ $user->name }}
                        </h1>
                        <div class="flex items-center gap-2 text-sm font-semibold text-white flex-wrap">
                            @if($user->jabatan)
                                <span class="text-white font-bold">{{ $user->jabatan }}</span>
                                @if($user->opd_name) <span class="text-teal-300">•</span> @endif
                            @endif
                            @if($user->opd_name)
                                <span class="text-blue-100 font-medium truncate max-w-xs">{{ $user->opd_name }}</span>
                            @endif
                            @if($user->nip)
                                <span class="text-teal-300">•</span>
                                <span class="font-mono text-xs text-white font-bold">NIP: {{ $user->nip }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 pt-0.5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold {{ $jpData['achieved'] ? 'bg-emerald-500/25 text-emerald-200 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-200 border border-amber-500/30' }}">
                                @if($jpData['achieved'])
                                    ✓ {{ $jpData['total'] }}/{{ $jpData['target'] }} JP — Target Tercapai!
                                @else
                                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>{{ $jpData['total'] }}/{{ $jpData['target'] }} JP Tahun {{ $currentYear }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi Kanan --}}
                <div class="shrink-0 flex flex-col sm:items-end gap-2.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('learning-record.my-transcript') }}" target="_blank" 
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-teal-200 bg-white/10 hover:bg-white/20 border border-teal-400/30 backdrop-blur-md transition whitespace-nowrap"
                           title="Cetak Buku Catatan Pembelajaran / Transkrip JP Resmi">
                            <svg class="w-4 h-4 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Transkrip JP</span>
                        </a>

                        <a href="{{ route('courses.index') }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-black text-white bg-blue-600 hover:bg-blue-500 shadow-xl shadow-blue-950/50 hover:-translate-y-0.5 transition duration-200 whitespace-nowrap">
                            <span>Jelajahi Katalog</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    <button @click="activeTab = 'profil'" class="text-xs font-semibold text-blue-200 hover:text-white transition">
                        Edit Profil & Foto →
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- TATA LETAK 2 KOLOM --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-7">
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            
            {{-- SIDEBAR KIRI --}}
            <aside class="w-full lg:w-64 lg:shrink-0">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden sticky top-20">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/40">
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">MENU PEMBELAJARAN</p>
                    </div>

                    <nav class="divide-y divide-slate-100">
                        
                        <div>
                            <button @click="activeTab = 'dashboard'" 
                                    :class="activeTab === 'dashboard' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span>Dashboard</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $jpData['achieved'] ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $jpData['percent'] }}% JP
                                </span>
                            </button>
                        </div>

                        <div>
                            <button @click="activeTab = 'statistik'" 
                                    :class="activeTab === 'statistik' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    <span>Statistik Pembelajaran</span>
                                </div>
                                <span class="text-[11px] font-bold text-slate-400">3 Thn</span>
                            </button>
                        </div>

                        <div>
                            <button @click="activeTab = 'pelatihan'" 
                                    :class="activeTab === 'pelatihan' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <span>Pelatihan Saya</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700">{{ $enrollments->count() }}</span>
                            </button>
                        </div>

                        <div>
                            <button @click="activeTab = 'webinar'" 
                                    :class="activeTab === 'webinar' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Webinar & Live Sesi</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700">{{ $userWebinars->count() }}</span>
                            </button>
                        </div>

                        <div>
                            <button @click="activeTab = 'presensi'" 
                                    :class="activeTab === 'presensi' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <span>Riwayat Presensi</span>
                                </div>
                                <span class="text-emerald-600 font-bold text-sm">✓</span>
                            </button>
                        </div>

                        <div>
                            <button @click="activeTab = 'sertifikat'" 
                                    :class="activeTab === 'sertifikat' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>
                                    <span>Sertifikat Digital</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-pink-50 text-pink-700">{{ $certificates->count() }}</span>
                            </button>
                        </div>

                        <div>
                            <button @click="activeTab = 'eksternal'" 
                                    :class="activeTab === 'eksternal' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span>Pengajuan JP Eksternal</span>
                                </div>
                                @if($pendingExternalCount > 0)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">{{ $pendingExternalCount }}</span>
                                @endif
                            </button>
                        </div>

                        @if($idpRecommendations->isNotEmpty())
                            <div>
                                <button @click="activeTab = 'idp'" 
                                        :class="activeTab === 'idp' ? 'text-indigo-700 font-black bg-indigo-50/80 shadow-2xs border-l-4 border-indigo-600' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-50 font-semibold'"
                                        class="w-full flex items-center justify-between px-5 py-4 text-sm sm:text-base transition duration-150 text-left">
                                    <div class="flex items-center gap-3.5">
                                        <svg class="w-5 h-5 shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                        <span>Roadmap IDP</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-100 text-indigo-900 border border-indigo-200">ASN</span>
                                </button>
                            </div>
                        @endif

                        <div>
                            <button @click="activeTab = 'profil'" 
                                    :class="activeTab === 'profil' ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                                    class="w-full flex items-center justify-between px-5 py-4 text-sm transition duration-150 text-left">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Profil & Akun</span>
                                </div>
                            </button>
                        </div>

                    </nav>
                </div>
            </aside>

            {{-- KONTEN KANAN --}}
            <main class="flex-1 min-w-0 space-y-6 pt-1">

                <div class="flex items-center justify-between pt-2">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight"
                        x-text="activeTab === 'dashboard' ? 'Dashboard' :
                               (activeTab === 'statistik' ? 'Statistik Pembelajaran' :
                               (activeTab === 'pelatihan' ? 'Pelatihan Saya' :
                               (activeTab === 'webinar' ? 'Webinar & Live Sesi' :
                               (activeTab === 'presensi' ? 'Riwayat Presensi' :
                               (activeTab === 'sertifikat' ? 'Sertifikat Digital' :
                               (activeTab === 'eksternal' ? 'Pengajuan JP Eksternal' :
                               (activeTab === 'idp' ? 'Roadmap IDP' :
                               (activeTab === 'profil' ? 'Profil & Akun' : 'Dashboard'))))))))">
                        Dashboard
                    </h2>
                    <a href="{{ route('courses.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition">
                        + Cari Pelatihan Baru
                    </a>
                </div>

                {{-- KARTU STATISTIK (hanya tampil di tab dashboard) --}}
                <div x-show="activeTab === 'dashboard'" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div @click="activeTab = 'pelatihan'" class="cursor-pointer rounded-2xl p-6 text-center flex flex-col items-center justify-center transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 group" style="background-color: #ebf3ff; border: 1px solid #d5e6ff;">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform" style="background-color: #cfe3ff; color: #3b82f6;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <span class="text-3xl sm:text-4xl font-black text-blue-900 tracking-tight leading-none">{{ $enrollments->count() }}</span>
                        <span class="text-[11px] font-black uppercase tracking-wider text-blue-600/90 mt-2">PELATIHAN TERDAFTAR</span>
                    </div>
                    <div @click="activeTab = 'pelatihan'; filterPelatihan = 'active'" class="cursor-pointer rounded-2xl p-6 text-center flex flex-col items-center justify-center transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 group" style="background-color: #f7efff; border: 1px solid #ebdaff;">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform" style="background-color: #ebd3ff; color: #a855f7;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-3xl sm:text-4xl font-black text-purple-900 tracking-tight leading-none">{{ $inProgressCount }}</span>
                        <span class="text-[11px] font-black uppercase tracking-wider text-purple-600/90 mt-2">SEDANG DIIKUTI</span>
                    </div>
                    <div @click="activeTab = 'pelatihan'; filterPelatihan = 'completed'" class="cursor-pointer rounded-2xl p-6 text-center flex flex-col items-center justify-center transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 group" style="background-color: #fbf0f4; border: 1px solid #f6dbe5;">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform" style="background-color: #f5d0de; color: #ec4899;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>
                        </div>
                        <span class="text-3xl sm:text-4xl font-black text-pink-900 tracking-tight leading-none">{{ $completedCount }}</span>
                        <span class="text-[11px] font-black uppercase tracking-wider text-pink-600/90 mt-2">PELATIHAN SELESAI</span>
                    </div>
                    <div class="rounded-2xl p-6 text-center flex flex-col items-center justify-center transition-all duration-200 hover:shadow-md" style="background-color: #edfcf2; border: 1px solid #d2f7dd;">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3" style="background-color: #c7f3d6; color: #10b981;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="flex items-baseline justify-center">
                            <span class="text-3xl sm:text-4xl font-black text-emerald-900 tracking-tight leading-none">{{ $jpData['total'] ?? 0 }}</span>
                            <span class="text-sm font-bold text-emerald-600 ml-1">/{{ $jpData['target'] ?? 20 }} JP</span>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-600/90 mt-2">TARGET JP {{ $currentYear }}</span>
                    </div>
                </div>


                {{-- TAB: DASHBOARD --}}
                <div x-show="activeTab === 'dashboard'" class="space-y-6">
                    
                    {{-- TARGET JP --}}
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 sm:p-7">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-7 items-center">
                            @php
                                $percent = $jpData['percent'];
                                $radius = 70;
                                $circumference = 2 * pi() * $radius;
                                $offset = $circumference - ($percent / 100) * $circumference;
                                $gaugeColor = $jpData['achieved'] ? '#059669' : ($percent >= 50 ? '#4f46e5' : '#f59e0b');
                            @endphp
                            <div class="md:col-span-5 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-slate-100 pb-6 md:pb-0 md:pr-6">
                                <div class="relative w-48 h-48 sm:w-52 sm:h-52">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 160 160">
                                        <circle cx="80" cy="80" r="{{ $radius }}" stroke="#f1f5f9" stroke-width="12" fill="none"/>
                                        <circle cx="80" cy="80" r="{{ $radius }}" stroke="{{ $gaugeColor }}" stroke-width="12" fill="none"
                                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                                                stroke-linecap="round" class="transition-all duration-1000 ease-out"/>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                        <span class="text-4xl font-black text-slate-900 tracking-tight">{{ $jpData['total'] }}</span>
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">DARI {{ $jpData['target'] }} JP</span>
                                        <span class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full mt-1 {{ $jpData['achieved'] ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $percent }}% Terpenuhi
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-7 space-y-4">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">CAPAIAN JP TAHUN {{ $currentYear }}</span>
                                        @if($jpData['achieved'])
                                            <span class="text-xs font-bold text-emerald-600">✓ Target 20 JP Tercapai! 🎉</span>
                                        @else
                                            <span class="text-xs font-bold text-amber-600">⏳ Kurang {{ $jpData['target'] - $jpData['total'] }} JP lagi</span>
                                        @endif
                                    </div>
                                    <h3 class="text-base sm:text-lg font-black text-slate-900 mt-2">Kewajiban Jam Pembelajaran Pegawai ASN</h3>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Setiap ASN berhak dan wajib memenuhi minimal 20 JP per tahun kalender melalui kursus mandiri e-learning, kehadiran webinar terverifikasi, dan pelatihan luar instansi.</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3 pt-1">
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase">JP Pelatihan Internal</p>
                                        <p class="text-base sm:text-lg font-black text-slate-900 mt-0.5">{{ $jpData['internal'] }} JP</p>
                                    </div>
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase">JP Pelatihan Eksternal</p>
                                        <p class="text-base sm:text-lg font-black text-slate-900 mt-0.5">{{ $jpData['external'] }} JP</p>
                                    </div>
                                </div>
                                <div class="pt-2 flex items-center gap-2.5 flex-wrap">
                                    <button type="button" @click="activeTab = 'eksternal'; showModalEksternal = true" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        <span>+ Ajukan JP Eksternal</span>
                                    </button>
                                    <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Cari Pelatihan</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- AGENDA WEBINAR TERDEKAT --}}
                    @if(isset($userWebinars) && $userWebinars->isNotEmpty())
                        <div class="space-y-3.5 pt-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                    Webinar & Sesi Live Terdaftar
                                </h3>
                                <button @click="activeTab = 'webinar'" class="text-xs font-bold text-teal-600 hover:text-teal-800 transition">Semua Webinar ({{ $userWebinars->count() }}) →</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($userWebinars->take(2) as $reg)
                                    @php $wb = $reg->webinar; @endphp
                                    @if($wb)
                                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 flex flex-col justify-between hover:border-teal-300 transition">
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-teal-50 text-teal-700 border border-teal-200">{{ $wb->delivery_mode === 'online' ? '📹 Webinar Live' : '🏢 In-Class' }}</span>
                                                    <span class="text-[10px] font-mono font-bold text-slate-400">{{ $reg->ticket_code }}</span>
                                                </div>
                                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-2"><a href="{{ route('webinars.show', $wb->slug) }}">{{ $wb->title }}</a></h4>
                                                <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                    <span>📅</span>
                                                    <span>{{ $wb->event_date->translatedFormat('d M Y') }} • {{ $wb->time_start }}-{{ $wb->time_end }} WIB</span>
                                                </p>
                                            </div>
                                            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between">
                                                <div>
                                                    @if($reg->is_attended)
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">✓ Hadir</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700">Terdaftar</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('webinars.show', $wb->slug) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-teal-600 transition">Ruang Belajar →</a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- TAB: STATISTIK --}}
                <div x-show="activeTab === 'statistik'" class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                Capaian JP Tahunan (3 Tahun Terakhir)
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">Riwayat pemenuhan kewajiban pengembangan kompetensi pegawai</p>
                        </div>
                        <div class="space-y-4 pt-2">
                            @foreach($yearlyStats as $stat)
                                @php
                                    $barPercent = $stat['target'] > 0 ? min(100, ($stat['total'] / $stat['target']) * 100) : 0;
                                    $barColor = $stat['achieved'] ? 'from-emerald-500 to-teal-400' : ($barPercent >= 50 ? 'from-indigo-600 to-purple-500' : 'from-amber-400 to-amber-500');
                                @endphp
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-slate-800 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-500"></span>Tahun {{ $stat['year'] }}</span>
                                        <span class="text-slate-700">{{ $stat['total'] }} dari {{ $stat['target'] }} JP
                                            @if($stat['achieved'])<span class="text-emerald-600 ml-1">✓ Memenuhi Target</span>@else<span class="text-amber-600 ml-1">({{ round($barPercent) }}%)</span>@endif
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r {{ $barColor }} h-3 rounded-full transition-all duration-700" style="width: {{ $barPercent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs"><span class="text-2xl">⏱️</span><h4 class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalLearningHours, 1) }}</h4><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Total Jam Belajar</p></div>
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs"><span class="text-2xl">🎓</span><h4 class="text-2xl font-black text-slate-900 mt-2">{{ $certificates->count() }}</h4><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Sertifikat Kelulusan</p></div>
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs"><span class="text-2xl">📝</span><h4 class="text-2xl font-black text-slate-900 mt-2">{{ $trainingHistory->count() }}</h4><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Total Kegiatan Belajar</p></div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Semua Riwayat Kegiatan Pembelajaran</h4>
                        <div class="divide-y divide-slate-100">
                            @forelse($trainingHistory as $item)
                                <div class="py-3 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-xs font-bold text-slate-900 truncate">{{ $item['title'] }}</p>
                                            <span class="px-1.5 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-600">{{ $item['category'] ?? 'Umum' }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $item['institution'] }} • {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}</p>
                                    </div>
                                    <div class="text-right shrink-0"><span class="px-2 py-0.5 rounded text-xs font-black bg-indigo-50 text-indigo-700">+{{ $item['jp'] }} JP</span></div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat kegiatan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- TAB: PELATIHAN SAYA --}}
                <div x-show="activeTab === 'pelatihan'" class="space-y-6">

                    {{-- Pelatihan Sedang Diikuti --}}
                    <div class="space-y-3.5">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            Pelatihan yang Sedang Diikuti
                        </h3>
                        @if($enrollments->where('progress_percent', '<', 100)->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($enrollments->where('progress_percent', '<', 100)->take(4) as $enrollment)
                                    @php $c = $enrollment->course; @endphp
                                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 flex flex-col justify-between hover:border-indigo-300 hover:shadow-md transition">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700">{{ $c->category->name ?? 'Umum' }}</span>
                                                <span class="text-[10px] font-bold text-indigo-600 font-mono">{{ $c->jp ?? 2 }} JP</span>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-900 line-clamp-2 hover:text-indigo-600"><a href="{{ route('classroom.show', $c->slug) }}">{{ $c->title }}</a></h4>
                                            <div class="space-y-1">
                                                <div class="flex items-center justify-between text-[11px] font-bold">
                                                    <span class="text-slate-400">Kemajuan:</span>
                                                    <span class="text-indigo-600">{{ $enrollment->progress_percent }}%</span>
                                                </div>
                                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-2 rounded-full transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between">
                                            <span class="text-[11px] text-slate-400">{{ $c->course_type_label }}</span>
                                            <a href="{{ route('classroom.show', $c->slug) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                                <span>Lanjutkan Belajar</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-white rounded-2xl border border-slate-200/90 p-8 text-center">
                                <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-500 mx-auto flex items-center justify-center text-xl mb-2">🎉</div>
                                <h4 class="text-sm font-bold text-slate-800">Tidak ada pelatihan yang sedang berjalan</h4>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Semua kursus terdaftar telah Anda selesaikan, atau Anda belum mendaftar kelas baru.</p>
                                <a href="{{ route('courses.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">Cari Kursus Baru</a>
                            </div>
                        @endif
                    </div>

                    {{-- Riwayat Pelatihan Terkini --}}
                    @if($trainingHistory->isNotEmpty())
                        <div class="space-y-3 pt-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    Riwayat Pelatihan Terkini
                                </h3>
                                <button @click="activeTab = 'statistik'" class="text-xs font-bold text-purple-600 hover:text-purple-800 transition">Lihat Riwayat Lengkap →</button>
                            </div>
                            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs divide-y divide-slate-100 overflow-hidden">
                                @foreach($trainingHistory->take(4) as $item)
                                    <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $item['type'] === 'internal' ? 'bg-indigo-50 text-indigo-600' : 'bg-teal-50 text-teal-600' }}">
                                                @if($item['type'] === 'internal') 📚 @else 🌐 @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 truncate">{{ $item['title'] }}</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $item['institution'] }} • {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-100">+{{ $item['jp'] }} JP</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Rekomendasi Pelatihan Berdasarkan IDP --}}
                    @if($idpRecommendations->isNotEmpty())
                        <div class="space-y-4 pt-1">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 animate-pulse"></span>
                                        <span>Rekomendasi Pelatihan Berdasarkan IDP</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Rencana Pengembangan ASN
                                        </span>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Disusun otomatis sesuai kesenjangan kompetensi Individual Development Plan (IDP) untuk pemenuhan minimal 20 JP tahunan.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button @click="activeTab = 'idp'" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-indigo-700 bg-indigo-50/80 hover:bg-indigo-100 border border-indigo-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                        <span>Roadmap IDP Lengkap →</span>
                                    </button>
                                </div>
                            </div>

                            @foreach($idpRecommendations as $group)
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                            <span>Rekomendasi Periode Tahun {{ $group['tahun'] }}</span>
                                            @if(!empty($group['is_custom']))
                                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Tervalidasi SIMPEG</span>
                                            @endif
                                        </span>
                                        <span class="text-xs text-slate-400 font-medium">{{ $group['materi']->count() }} Materi Pelatihan</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($group['materi'] as $item)
                                            @php $matched = $item['matched_course']; @endphp
                                            @if($matched)
                                                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:border-indigo-400 hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                                                    <div>
                                                        {{-- Thumbnail & Badges --}}
                                                        <div class="relative aspect-video w-full overflow-hidden bg-slate-100">
                                                            <img src="{{ $matched->thumbnail }}" alt="{{ $matched->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                                            
                                                            {{-- Top badges --}}
                                                            <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 flex-wrap">
                                                                <span class="px-2 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-indigo-600 text-white shadow-xs">
                                                                    IDP {{ $group['tahun'] }}
                                                                </span>
                                                                <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-white/95 text-slate-800 shadow-xs">
                                                                    {{ $matched->category->name ?? 'Kompetensi ASN' }}
                                                                </span>
                                                            </div>

                                                            <div class="absolute top-2.5 right-2.5">
                                                                @if($item['is_completed'])
                                                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-emerald-500 text-white shadow-xs">✓ Selesai</span>
                                                                @elseif($item['is_enrolled'])
                                                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-purple-600 text-white shadow-xs">{{ $item['progress'] }}% Aktif</span>
                                                                @else
                                                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-amber-400 text-slate-950 shadow-xs flex items-center gap-1">
                                                                        <span>🎯 Rekomendasi</span>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Content --}}
                                                        <div class="p-4 space-y-2.5">
                                                            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                                                                <span class="inline-flex items-center gap-1 text-indigo-700 font-semibold bg-indigo-50/80 px-2 py-0.5 rounded-md border border-indigo-100">
                                                                    <span>📌</span>
                                                                    <span>{{ $item['gap_type'] }}</span>
                                                                </span>
                                                                <span class="font-mono font-bold text-indigo-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">
                                                                    +{{ $matched->jp ?? 2 }} JP
                                                                </span>
                                                            </div>

                                                            <h4 class="font-bold text-slate-900 text-sm sm:text-base leading-snug line-clamp-2 group-hover:text-indigo-600 transition">
                                                                <a href="{{ route('courses.show', $matched->slug) }}">
                                                                    {{ $matched->title }}
                                                                </a>
                                                            </h4>

                                                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 text-xs space-y-1">
                                                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Target Materi IDP:</p>
                                                                <p class="text-slate-700 font-medium line-clamp-2 leading-relaxed">
                                                                    {{ $item['judul_materi'] }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Actions --}}
                                                    <div class="p-4 pt-0">
                                                        @if($item['is_completed'])
                                                            <a href="{{ route('classroom.show', $matched->slug) }}" class="w-full flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                                                                <span>✓ Pelatihan Selesai Terpenuhi</span>
                                                            </a>
                                                        @elseif($item['is_enrolled'])
                                                            <a href="{{ route('classroom.show', $matched->slug) }}" class="w-full flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-xs">
                                                                <span>Lanjutkan Pelatihan ({{ $item['progress'] }}%)</span>
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('courses.show', $matched->slug) }}" class="w-full flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-slate-900 hover:bg-indigo-600 transition shadow-xs">
                                                                <span>Daftar Pelatihan IDP</span>
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Materi IDP Jalur Eksternal / Swasta / LAN --}}
                                                <div class="bg-gradient-to-br from-amber-50/70 to-orange-50/40 rounded-2xl border border-amber-200/90 p-4.5 flex flex-col justify-between space-y-3.5 hover:shadow-md hover:border-amber-400 transition-all">
                                                    <div class="space-y-2.5">
                                                        <div class="flex items-center justify-between text-xs gap-1 flex-wrap">
                                                            <span class="px-2 py-0.5 rounded-md font-bold bg-amber-200 text-amber-900 uppercase">
                                                                IDP {{ $group['tahun'] }}
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded text-xs font-semibold text-amber-900 bg-amber-100 border border-amber-300 flex items-center gap-1">
                                                                <span>{{ $item['external_recommendation']['icon'] ?? '🏛️' }}</span>
                                                                <span>Pelatihan Eksternal diluar LMS Berau</span>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold text-sm sm:text-base text-slate-900 leading-snug">
                                                                {{ $item['judul_materi'] }}
                                                            </h4>
                                                            <p class="text-xs text-amber-950 font-medium mt-1">
                                                                Penyelenggara: <strong class="text-slate-900">{{ $item['external_recommendation']['provider'] ?? 'LAN RI / Provider Swasta' }}</strong>
                                                            </p>
                                                        </div>
                                                        <div class="p-2.5 rounded-xl bg-white/90 border border-amber-200 text-xs text-slate-600 leading-relaxed shadow-2xs">
                                                            💡 <em>Belum tersedia di katalog LMS. Peserta dapat mengikuti di provider swasta/LAN, lalu sertifikat diajukan untuk diakui angka Jam Pelajaran (JP).</em>
                                                        </div>
                                                    </div>
                                                    <div class="pt-1">
                                                        <button type="button" 
                                                                @click="openExternalSubmission('{{ addslashes($item['judul_materi']) }}')"
                                                                class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-2xs transition">
                                                            <span>📄 Klaim / Ajukan JP Eksternal</span>
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Semua Kursus dengan Filter --}}
                    <div>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Semua Kursus Terdaftar
                        </h3>
                        <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 flex-wrap">
                            <button @click="filterPelatihan = 'all'" :class="filterPelatihan === 'all' ? 'bg-slate-900 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 font-medium'" class="px-4 py-1.5 rounded-xl text-xs transition border border-slate-200/80">Semua ({{ $enrollments->count() }})</button>
                            <button @click="filterPelatihan = 'active'" :class="filterPelatihan === 'active' ? 'bg-purple-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 font-medium'" class="px-4 py-1.5 rounded-xl text-xs transition border border-slate-200/80">Sedang Diikuti ({{ $inProgressCount }})</button>
                            <button @click="filterPelatihan = 'completed'" :class="filterPelatihan === 'completed' ? 'bg-emerald-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 font-medium'" class="px-4 py-1.5 rounded-xl text-xs transition border border-slate-200/80">Selesai ({{ $completedCount }})</button>
                        </div>
                    </div>

                    @if($enrollments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($enrollments as $enrollment)
                                @php 
                                    $c = $enrollment->course; 
                                    $isComp = $enrollment->progress_percent >= 100 || $enrollment->attended_at;
                                    $cert = $certificates->firstWhere('course_id', $c->id);
                                @endphp
                                <div x-show="filterPelatihan === 'all' || (filterPelatihan === 'active' && {{ $isComp ? 'false' : 'true' }}) || (filterPelatihan === 'completed' && {{ $isComp ? 'true' : 'false' }})"
                                     class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between hover:border-indigo-300 hover:shadow-md transition group">
                                    <div>
                                        <div class="relative aspect-video w-full overflow-hidden bg-slate-100">
                                            <img src="{{ $c->thumbnail }}" alt="{{ $c->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                            <div class="absolute top-3 left-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-white/95 text-slate-800 shadow-xs">{{ $c->category->name ?? 'Umum' }}</span></div>
                                            @if($isComp)<div class="absolute top-3 right-3"><span class="px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-500 text-white shadow-xs">✓ Selesai</span></div>@endif
                                        </div>
                                        <div class="p-5 space-y-3">
                                            <h3 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2">{{ $c->title }}</h3>
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between text-xs font-semibold">
                                                    <span class="text-slate-500">Progres Belajar:</span>
                                                    <span class="text-indigo-600 font-mono">{{ $enrollment->progress_percent }}%</span>
                                                </div>
                                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                                                </div>
                                            </div>
                                            <p class="text-[11px] text-slate-400">{{ $c->jp ?? 2 }} JP • {{ $c->duration_hours ?? 2 }} Jam Pembelajaran</p>
                                        </div>
                                    </div>
                                    <div class="p-5 pt-0 space-y-2">
                                        @if($isComp && $cert)
                                            <a href="{{ route('certificates.show', $cert->certificate_number) }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-xs">
                                                <span>Lihat Sertifikat</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('classroom.show', $c->slug) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-indigo-600 transition shadow-xs">
                                            <span>{{ $isComp ? 'Buka Ruang Materi' : 'Lanjutkan Belajar' }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-10 text-center">
                            <p class="text-xs text-slate-400">Anda belum mendaftar pelatihan manapun.</p>
                            <a href="{{ route('courses.index') }}" class="inline-block mt-3 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600">Jelajahi Katalog Kursus</a>
                        </div>
                    @endif
                </div>

                {{-- TAB: WEBINAR --}}
                <div x-show="activeTab === 'webinar'" class="space-y-6">
                    @if(isset($userWebinars) && $userWebinars->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($userWebinars as $reg)
                                @php $wb = $reg->webinar; $rec = isset($webinarRecords) ? $webinarRecords->firstWhere('webinar_id', $wb->id) : null; @endphp
                                @if($wb)
                                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 flex flex-col justify-between space-y-4">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $wb->delivery_mode === 'online' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-800' }}">{{ $wb->delivery_mode === 'online' ? '📹 Live Webinar' : '🏢 In-Class' }}</span>
                                                <span class="text-xs font-mono font-bold text-slate-400">Tiket: {{ $reg->ticket_code }}</span>
                                            </div>
                                            <h3 class="text-sm font-black text-slate-900 leading-snug"><a href="{{ route('webinars.show', $wb->slug) }}">{{ $wb->title }}</a></h3>
                                            <div class="text-xs text-slate-500 space-y-1">
                                                <p>📅 {{ $wb->event_date->translatedFormat('d M Y') }} ({{ $wb->time_start }}-{{ $wb->time_end }} WIB)</p>
                                                <p>👤 Pemateri: {{ $wb->speaker_name }}</p>
                                                @if($wb->location)<p>📍 Lokasi: {{ $wb->location }}</p>@endif
                                            </div>
                                        </div>
                                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                            <div>
                                                @if($reg->is_attended)<span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700">✓ Presensi Hadir</span>
                                                @else<span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700">Belum Presensi</span>@endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if($rec && $rec->certificate_issued)
                                                    <a href="{{ route('webinars.certificate', $wb->slug) }}" target="_blank" class="px-3 py-1.5 rounded-xl text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200">🏅 Sertifikat</a>
                                                @endif
                                                <a href="{{ route('webinars.show', $wb->slug) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-teal-600 transition">Masuk Ruang Sesi →</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-10 text-center">
                            <p class="text-xs text-slate-400">Anda belum mendaftar webinar.</p>
                            <a href="{{ route('webinars.index') }}" class="inline-block mt-3 px-4 py-2 rounded-xl text-xs font-bold text-white bg-teal-600">Lihat Jadwal Webinar</a>
                        </div>
                    @endif
                </div>

                {{-- TAB: RIWAYAT PRESENSI (DETAIL LENGKAP) --}}
                <div x-show="activeTab === 'presensi'" class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Catatan Kehadiran & Presensi Pelatihan</h3>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">QR & Digital Verified</span>
                        </div>

                        @php
                            $presensiList = collect();
                            foreach($enrollments->whereNotNull('attended_at') as $enr) {
                                $presensiList->push([
                                    'title'    => $enr->course->title,
                                    'date'     => $enr->attended_at,
                                    'type'     => 'Kursus E-Learning',
                                    'jp'       => $enr->course->jp ?? 0,
                                    'location' => null,
                                    'speaker'  => $enr->course->instructor->name ?? null,
                                    'tanggal_kegiatan' => null,
                                ]);
                            }
                            foreach($userWebinars->where('is_attended', true) as $wReg) {
                                if($wReg->webinar) {
                                    $presensiList->push([
                                        'title'    => $wReg->webinar->title,
                                        'date'     => $wReg->attended_at ?? $wReg->updated_at,
                                        'type'     => $wReg->webinar->delivery_mode === 'online' ? 'Webinar / Live Online' : 'In-Class / Tatap Muka',
                                        'jp'       => $wReg->webinar->jp ?? 0,
                                        'location' => $wReg->webinar->location ?? null,
                                        'speaker'  => $wReg->webinar->speaker_name ?? null,
                                        'tanggal_kegiatan' => $wReg->webinar->event_date ?? null,
                                    ]);
                                }
                            }
                            $presensiList = $presensiList->sortByDesc('date');
                        @endphp

                        @if($presensiList->isEmpty())
                            <div class="py-8 text-center text-xs text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-50 mx-auto flex items-center justify-center text-2xl mb-3">📋</div>
                                Belum ada catatan presensi yang terkonfirmasi.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($presensiList as $p)
                                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/40 p-4 hover:bg-white hover:shadow-sm transition">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 
                                                    {{ str_contains($p['type'], 'Webinar') || str_contains($p['type'], 'Live') ? 'bg-teal-50 text-teal-600' : 
                                                       (str_contains($p['type'], 'In-Class') ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-600') }}">
                                                    @if(str_contains($p['type'], 'Webinar') || str_contains($p['type'], 'Live')) 📹
                                                    @elseif(str_contains($p['type'], 'In-Class')) 🏢
                                                    @else 💻 @endif
                                                </div>
                                                <div class="min-w-0 space-y-1">
                                                    <p class="text-sm font-bold text-slate-900 leading-snug">{{ $p['title'] }}</p>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Hadir</span>
                                                        <span class="text-[11px] text-slate-500 font-medium">{{ $p['type'] }}</span>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-0.5 pt-1">
                                                        @if(isset($p['tanggal_kegiatan']) && $p['tanggal_kegiatan'])
                                                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                                <span class="text-slate-400">📅</span>
                                                                <span class="font-medium">Tanggal:</span>
                                                                {{ \Carbon\Carbon::parse($p['tanggal_kegiatan'])->translatedFormat('d M Y') }}
                                                            </p>
                                                        @endif
                                                        <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                            <span class="text-slate-400">🕐</span>
                                                            <span class="font-medium">Presensi:</span>
                                                            {{ \Carbon\Carbon::parse($p['date'])->format('d M Y, H:i') }} WIB
                                                        </p>
                                                        @if($p['location'])
                                                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                                <span class="text-slate-400">📍</span>
                                                                <span class="font-medium">Lokasi:</span>
                                                                {{ $p['location'] }}
                                                            </p>
                                                        @endif
                                                        @if($p['speaker'])
                                                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                                <span class="text-slate-400">👤</span>
                                                                <span class="font-medium">Pemateri:</span>
                                                                {{ $p['speaker'] }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($p['jp'] > 0)
                                                <div class="shrink-0">
                                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-100">+{{ $p['jp'] }} JP</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- TAB: SERTIFIKAT DIGITAL (CARD DETAIL + DOWNLOAD) --}}
                <div x-show="activeTab === 'sertifikat'" class="space-y-6">
                    @if($certificates->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($certificates as $cert)
                                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden hover:shadow-md hover:border-pink-300 transition group">
                                    <div class="h-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500"></div>
                                    <div class="p-5 space-y-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-2xl shrink-0">🎓</div>
                                            <span class="text-[10px] font-mono font-bold text-pink-700 bg-pink-50 px-2 py-1 rounded-lg border border-pink-200 text-right">{{ $cert->certificate_number }}</span>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-900 leading-snug line-clamp-2">{{ $cert->course->title }}</h4>
                                        <div class="grid grid-cols-2 gap-3 pt-1">
                                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Diterbitkan</p>
                                                <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $cert->issued_at->format('d M Y') }}</p>
                                            </div>
                                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jam Pembelajaran</p>
                                                <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $cert->course->jp ?? '-' }} JP</p>
                                            </div>
                                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Durasi</p>
                                                <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $cert->course->duration_hours ?? '-' }} Jam</p>
                                            </div>
                                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori</p>
                                                <p class="text-xs font-bold text-slate-800 mt-0.5 truncate">{{ $cert->course->category->name ?? 'Umum' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 pt-1">
                                            <a href="{{ route('certificates.show', $cert->certificate_number) }}" target="_blank"
                                               class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>Unduh Sertifikat</span>
                                            </a>
                                            <a href="{{ route('certificates.show', $cert->certificate_number) }}" target="_blank" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Lihat</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-10 text-center">
                            <div class="w-14 h-14 rounded-full bg-pink-50 mx-auto flex items-center justify-center text-3xl mb-3">🎓</div>
                            <p class="text-sm font-bold text-slate-700">Belum ada sertifikat digital</p>
                            <p class="text-[11px] text-slate-400 mt-1">Selesaikan seluruh materi kursus atau webinar untuk mendapatkan e-sertifikat resmi.</p>
                            <a href="{{ route('courses.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">Cari Pelatihan</a>
                        </div>
                    @endif
                </div>

                {{-- TAB: PENGAJUAN JP EKSTERNAL (IN-PAGE WORKFLOW) --}}
                <div x-show="activeTab === 'eksternal'" class="space-y-6">

                    {{-- Feedback Alert --}}
                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-3 shadow-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">✅</span>
                                <span>{{ session('success') }}</span>
                            </div>
                            <span class="text-[10px] text-emerald-600 bg-emerald-100/60 px-2 py-0.5 rounded-full font-semibold">Tersimpan</span>
                        </div>
                    @endif

                    @if($errors->any() && (old('training_name') || old('jp_count')))
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2.5 shadow-xs">
                            <span class="text-base">⚠️</span>
                            <div>
                                <p>Pengajuan belum lengkap. Silakan periksa kembali isian form Anda.</p>
                                <ul class="list-disc list-inside font-normal text-[11px] mt-1 text-rose-700">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- KPI Summary Card JP Eksternal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span>JP Disetujui</span>
                                <span class="text-base">🌟</span>
                            </div>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ $jpData['external'] }} <span class="text-xs font-medium text-slate-400">JP</span></p>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Terkonfirmasi ke profil ASN</p>
                        </div>
                        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span>Menunggu Verifikasi</span>
                                <span class="text-base">⏳</span>
                            </div>
                            <p class="text-2xl font-black text-amber-600 mt-1">{{ $pendingExternalCount }} <span class="text-xs font-medium text-slate-400">Pengajuan</span></p>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Dalam antrean verifikasi admin</p>
                        </div>
                        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span>Total Diajukan</span>
                                <span class="text-base">📋</span>
                            </div>
                            <p class="text-2xl font-black text-blue-600 mt-1">{{ $externalTrainings->count() }} <span class="text-xs font-medium text-slate-400">Pelatihan</span></p>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Riwayat pelatihan luar instansi</p>
                        </div>
                    </div>

                    {{-- Card Daftar Pengajuan & Tombol Ajukan In-Page --}}
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                    <span>🌐</span>
                                    <span>Daftar Pengajuan JP Pelatihan Eksternal</span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Klaim JP dari diklat, bimtek, workshop, atau sertifikasi yang Anda ikuti di luar instansi.</p>
                            </div>
                            <button type="button" 
                                    @click="showModalEksternal = true" 
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition duration-150 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Ajukan JP Baru</span>
                            </button>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse($externalTrainings as $ext)
                                <div class="py-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 rounded-xl px-2 transition">
                                    <div class="min-w-0 space-y-1">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <p class="text-sm font-bold text-slate-900">{{ $ext->training_name }}</p>
                                            @if($ext->status === 'approved')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span>✓</span> Disetujui
                                                </span>
                                            @elseif($ext->status === 'rejected')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span>✕</span> Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span>⏳</span> Menunggu Verifikasi
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 flex-wrap">
                                            <span class="font-medium text-slate-700">🏛️ {{ $ext->institution_name }}</span>
                                            <span>•</span>
                                            <span>📅 {{ \Carbon\Carbon::parse($ext->training_date)->format('d M Y') }}@if($ext->training_end_date) — {{ \Carbon\Carbon::parse($ext->training_end_date)->format('d M Y') }}@endif</span>
                                        </div>
                                        @if($ext->description)
                                            <p class="text-xs text-slate-500 italic pt-0.5">"{{ $ext->description }}"</p>
                                        @endif
                                        @if($ext->isRejected() && $ext->rejection_reason)
                                            <div class="p-2 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700 mt-1">
                                                <strong>Alasan Penolakan:</strong> {{ $ext->rejection_reason }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center sm:flex-col sm:items-end justify-between gap-2.5 shrink-0">
                                        <span class="text-xs font-black text-blue-700 bg-blue-50 border border-blue-200/60 px-3 py-1 rounded-lg">
                                            {{ $ext->jp_count }} JP
                                        </span>
                                            @if($ext->certificate_file)
                                                <a href="{{ route('external-trainings.file', [$ext->id, 'certificate']) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50/60 hover:bg-blue-100 px-2 py-1 rounded-md transition" title="Lihat Sertifikat (Terlindungi)">
                                                    <span>📄</span> Sertifikat
                                                </a>
                                            @endif
                                            @if($ext->supporting_file)
                                                <a href="{{ route('external-trainings.file', [$ext->id, 'supporting']) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded-md transition" title="Lihat Bukti Pendukung (Terlindungi)">
                                                    <span>📎</span> Bukti
                                                </a>
                                            @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center space-y-3">
                                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-3xl shadow-2xs">
                                        📜
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Pengajuan JP Eksternal</h4>
                                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Ikuti pelatihan di luar platform dan ajukan buktinya di sini untuk mempercepat pemenuhan 20 JP tahunan Anda.</p>
                                    <div class="pt-2">
                                        <button type="button" 
                                                @click="showModalEksternal = true" 
                                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>Ajukan Pelatihan Eksternal Sekarang</span>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- TAB: ROADMAP IDP --}}
                @if($idpRecommendations->isNotEmpty())
                    <div x-show="activeTab === 'idp'" class="space-y-6">

                        {{-- Panduan Dua Jalur Pemenuhan IDP (Proporsional & Seimbang, Font +1 pt) --}}
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                                        <span class="text-2xl">🧭</span>
                                        <span>Roadmap Pengembangan Individu (IDP) ASN</span>
                                    </h3>
                                    <p class="text-sm text-slate-600 font-normal mt-1 leading-relaxed">
                                        Daftar seluruh judul pelatihan & pengembangan kompetensi yang telah ditetapkan oleh Administrator / Tim Penilai Talenta untuk NIP Anda.
                                    </p>
                                </div>
                                @if(!empty($user->nip))
                                    <span class="px-3.5 py-1.5 rounded-full text-xs sm:text-sm font-mono font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200 self-start sm:self-auto shrink-0 shadow-2xs">
                                        NIP: {{ $user->nip }}
                                    </span>
                                @endif
                            </div>

                            {{-- 2 Jalur Banner Cards (Proporsional, Rapi, & Nyaman Dibaca) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/80 border border-emerald-300/80 shadow-2xs flex items-start gap-3.5">
                                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl shrink-0 font-bold shadow-2xs">
                                        💻
                                    </div>
                                    <div class="space-y-1 flex-1">
                                        <h4 class="text-sm sm:text-base font-bold text-emerald-950 tracking-tight leading-snug">
                                            Jalur 1 LMS Internal Kabupaten Berau
                                        </h4>
                                        <p class="text-xs sm:text-sm text-emerald-900 leading-relaxed font-normal">
                                            Materi yang telah tersedia modulnya di LMS Internal Kabupaten Berau. Anda cukup <strong>klik ikuti</strong> untuk langsung belajar online, pre-test, dan menerima e-sertifikat otomatis.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/80 border border-amber-300/80 shadow-2xs flex items-start gap-3.5">
                                    <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0 font-bold shadow-2xs">
                                        🏛️
                                    </div>
                                    <div class="space-y-1 flex-1">
                                        <h4 class="text-sm sm:text-base font-bold text-amber-950 tracking-tight leading-snug">
                                            Jalur 2 Pelatihan Eksternal diluar LMS Kabupaten Berau
                                        </h4>
                                        <p class="text-xs sm:text-sm text-amber-900 leading-relaxed font-normal">
                                            Materi belum ada di katalog LMS Kabupaten Berau? Ikuti di <strong>LAN RI, BPSDMD, atau Provider Swasta</strong>, lalu klaim sertifikatnya via tombol <em>Ajukan JP Eksternal</em>.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Filter Buttons (Proporsional, Elegan, Font +1 pt) --}}
                            <div class="flex items-center gap-2.5 border-t border-slate-100 pt-3.5 flex-wrap">
                                <button type="button" 
                                        @click="filterIdpTab = 'all'" 
                                        :class="filterIdpTab === 'all' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold'" 
                                        class="px-4 py-2 rounded-xl text-xs sm:text-sm transition border border-slate-200">
                                    Semua Materi IDP
                                </button>
                                <button type="button" 
                                        @click="filterIdpTab = 'internal'" 
                                        :class="filterIdpTab === 'internal' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-50 font-semibold'" 
                                        class="px-4 py-2 rounded-xl text-xs sm:text-sm transition border border-slate-200 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                    <span>Tersedia di LMS (Langsung Ikuti)</span>
                                </button>
                                <button type="button" 
                                        @click="filterIdpTab = 'external'" 
                                        :class="filterIdpTab === 'external' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-50 font-semibold'" 
                                        class="px-4 py-2 rounded-xl text-xs sm:text-sm transition border border-slate-200 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                    <span>Jalur 2: Pelatihan Eksternal diluar LMS Berau</span>
                                </button>
                            </div>
                        </div>

                        {{-- Daftar Materi IDP per Tahun --}}
                        <div class="space-y-6">
                            @foreach($idpRecommendations as $group)
                                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                                    <div class="px-5 py-3.5 bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                                            <span class="text-xs sm:text-sm font-bold uppercase tracking-wider">
                                                Tahun Perencanaan {{ $group['tahun'] }} — Rekomendasi IDP
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-semibold bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">
                                                Total: {{ $group['materi']->count() }} Materi
                                            </span>
                                        </div>
                                    </div>

                                    <div class="divide-y divide-slate-100">
                                        @foreach($group['materi'] as $item)
                                            @php 
                                                $matched = $item['matched_course'];
                                                $hasMatch = $matched !== null;
                                            @endphp

                                            {{-- Baris Materi IDP --}}
                                            <div x-show="filterIdpTab === 'all' || (filterIdpTab === 'internal' && {{ $hasMatch ? 'true' : 'false' }}) || (filterIdpTab === 'external' && {{ $hasMatch ? 'false' : 'true' }})"
                                                 class="p-4 sm:p-5 hover:bg-slate-50/70 transition space-y-3">
                                                
                                                {{-- Bagian Atas: Badges, Judul Materi & Tombol Aksi --}}
                                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                                    {{-- Info Kiri --}}
                                                    <div class="min-w-0 space-y-1.5 flex-1">
                                                        {{-- Badges Header Baris --}}
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            @if($hasMatch)
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                                    <span>✓</span>
                                                                    <span>Tersedia di LMS Internal</span>
                                                                </span>
                                                                <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                                    +{{ $matched->jp ?? 2 }} JP
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-900 border border-amber-300">
                                                                    <span>{{ $item['external_recommendation']['icon'] ?? '🏛️' }}</span>
                                                                    <span>{{ $item['external_recommendation']['badge'] ?? 'Pelatihan Eksternal diluar LMS Kabupaten Berau' }}</span>
                                                                </span>
                                                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                                    Belum Ada di LMS
                                                                </span>
                                                            @endif

                                                            <span class="text-xs font-medium text-slate-500 font-mono">
                                                                {{ $item['gap_type'] }}
                                                            </span>
                                                        </div>

                                                        {{-- Judul Materi Input Admin --}}
                                                        <h4 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                                            {{ $item['judul_materi'] }}
                                                        </h4>

                                                        @if($hasMatch)
                                                            <p class="text-xs sm:text-sm text-emerald-700 font-medium mt-1 flex items-center gap-1.5 flex-wrap">
                                                                <span>📚 Kursus Terkait:</span>
                                                                <strong class="text-slate-900">{{ $matched->title }}</strong>
                                                                <span class="text-slate-400">• {{ $matched->category->name ?? 'Kompetensi ASN' }}</span>
                                                            </p>
                                                        @endif
                                                    </div>

                                                    {{-- Aksi Kanan (Tombol) --}}
                                                    <div class="flex items-center gap-2 self-start lg:self-center shrink-0 w-full sm:w-auto pt-1 lg:pt-0">
                                                        @if($hasMatch)
                                                            @if($item['is_completed'])
                                                                <a href="{{ route('classroom.show', $matched->slug) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shadow-2xs w-full sm:w-auto text-center">
                                                                    <span>✓ Pelatihan Selesai Terpenuhi</span>
                                                                </a>
                                                            @elseif($item['is_enrolled'])
                                                                <a href="{{ route('classroom.show', $matched->slug) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-xs w-full sm:w-auto text-center">
                                                                    <span>▶ Lanjutkan Belajar ({{ $item['progress'] }}%)</span>
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('courses.show', $matched->slug) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-xs w-full sm:w-auto text-center">
                                                                    <span>🚀 Ikuti di LMS Sekarang</span>
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                                </a>
                                                            @endif
                                                        @else
                                                            {{-- Aksi untuk materi eksternal --}}
                                                            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                                                                <button type="button" 
                                                                        @click="openExternalSubmission('{{ addslashes($item['judul_materi']) }}')"
                                                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition shadow-xs w-full sm:w-auto text-center">
                                                                    <span>📄 Ajukan JP Pelatihan Eksternal</span>
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                                </button>
                                                                <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition w-full sm:w-auto text-center">
                                                                    <span>Cari di LMS</span>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Card Pertimbangan Jalur Eksternal: Membentang Panjang Penuh (w-full) & Rapi --}}
                                                @if(!$hasMatch)
                                                    <div class="w-full p-3.5 sm:p-4 rounded-xl bg-amber-50/70 border border-amber-200/90 text-xs sm:text-sm space-y-1.5 shadow-2xs">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="inline-flex items-center gap-1.5 font-bold text-amber-950">
                                                                <span>💡</span>
                                                                <span>Pertimbangan Jalur Peserta:</span>
                                                            </span>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-950 border border-amber-300">
                                                                {{ $item['external_recommendation']['provider'] ?? 'LAN RI / Provider Swasta' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs sm:text-sm text-amber-900 leading-relaxed font-normal">
                                                            Materi ini dapat Anda ikuti melalui penyelenggara eksternal / provider swasta atau LAN. Setelah lulus, ajukan sertifikat kelulusan agar tercatat resmi sebagai Jam Pelajaran (JP).
                                                        </p>
                                                    </div>
                                                @endif

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endif

                {{-- TAB: PROFIL & AKUN (INLINE — TIDAK MEMBUKA HALAMAN BARU) --}}
                <div x-show="activeTab === 'profil'" class="space-y-6">

                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                            <span>✅</span><span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                            <span>⚠️</span><span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                            <p class="font-bold">Terdapat kesalahan:</p>
                            <ul class="list-disc list-inside">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {{-- Kolom Kiri: Foto Profil & Ganti Kata Sandi --}}
                        <div class="space-y-5">
                            {{-- Card Foto Profil --}}
                            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 text-center space-y-4">
                                
                                {{-- Foto / Preview --}}
                                <div class="relative inline-block mx-auto">
                                    {{-- Lingkaran foto --}}
                                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto border-4 border-indigo-100 shadow-xl bg-gradient-to-br from-indigo-500 to-purple-600">
                                        {{-- Preview dari file yang baru dipilih (Alpine) --}}
                                        <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover" style="display:none;">
                                        
                                        {{-- Foto tersimpan di DB --}}
                                        @if($user->avatar)
                                            <img x-show="!photoPreview" src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'; document.getElementById('avatar-fallback-profil').style.display='flex';">
                                            <div id="avatar-fallback-profil" class="hidden w-full h-full items-center justify-center text-white font-black text-3xl">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @else
                                            {{-- Inisial nama jika belum ada foto --}}
                                            <div x-show="!photoPreview" class="w-full h-full flex items-center justify-center text-white font-black text-3xl">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Badge kamera --}}
                                    <div class="absolute -bottom-1 -right-1 w-9 h-9 rounded-full bg-indigo-600 border-2 border-white flex items-center justify-center text-lg shadow-md cursor-pointer">
                                        📷
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-black text-slate-900 text-base">{{ $user->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $user->jabatan ?? 'ASN' }}</p>
                                </div>

                                {{-- Upload Foto --}}
                                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ganti Foto Profil</label>
                                        <label class="cursor-pointer block">
                                            <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-4 text-center transition group">
                                                <div x-show="!photoPreview" class="space-y-1">
                                                    <p class="text-xs font-bold text-slate-500 group-hover:text-indigo-600 transition">📁 Klik untuk pilih foto</p>
                                                    <p class="text-[10px] font-semibold text-teal-700 bg-teal-50 py-0.5 px-2 rounded inline-block border border-teal-200">📐 Rekomendasi: 500 × 500 px (Rasio 1:1 Persegi)</p>
                                                    <p class="text-[10px] text-slate-400">JPG, PNG, WebP • Maks. 2MB</p>
                                                </div>
                                                <div x-show="photoPreview" class="space-y-1">
                                                    <p class="text-xs font-bold text-indigo-600">✓ Foto siap diunggah</p>
                                                    <p class="text-[10px] text-slate-400">Klik tombol Simpan di bawah</p>
                                                </div>
                                            </div>
                                            <input type="file" name="photo" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden"
                                                   @change="
                                                       const f = $event.target.files[0];
                                                       if(f) {
                                                           if(f.size > 2097152) { alert('Ukuran file maksimal 2MB'); $event.target.value=''; return; }
                                                           const r = new FileReader();
                                                           r.onload = e => photoPreview = e.target.result;
                                                           r.readAsDataURL(f);
                                                       }
                                                   ">
                                        </label>
                                    </div>
                                    <div x-show="photoPreview" class="flex gap-2">
                                        <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">💾 Simpan Foto</button>
                                        <button type="button" @click="photoPreview = null; $el.closest('form').querySelector('input[type=file]').value=''" class="px-3 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">✕</button>
                                    </div>
                                    @if($user->avatar)
                                        <p class="text-[10px] text-slate-400 text-center">Foto profil aktif tersimpan</p>
                                    @else
                                        <p class="text-[10px] text-slate-400 text-center">Belum ada foto profil</p>
                                    @endif
                                </form>
                            </div>

                            {{-- Card Ganti Kata Sandi (di bawah Foto Profil) --}}
                            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6">
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-1.5 h-4 bg-rose-500 rounded-full"></span>
                                    Ganti Kata Sandi
                                </h4>
                                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi Saat Ini</label>
                                        <input type="password" name="current_password" required placeholder="Masukkan sandi saat ini" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-rose-400 focus:ring-2 focus:ring-rose-100 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-rose-400 focus:ring-2 focus:ring-rose-100 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-rose-400 focus:ring-2 focus:ring-rose-100 outline-none transition">
                                    </div>
                                    <div class="pt-1">
                                        <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm flex items-center justify-center gap-1.5">
                                            <span>🔐</span> Perbarui Kata Sandi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>


                        {{-- Kolom Kanan: Form Biodata & Kepegawaian --}}
                        <div class="lg:col-span-2 space-y-5">
                            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6">
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-5 flex items-center gap-2">
                                    <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                                    Biodata & Kepegawaian
                                </h4>
                                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email *</label>
                                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">No. Telepon</label>
                                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIP</label>
                                            <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK</label>
                                            <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jabatan</label>
                                            <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pangkat / Golongan</label>
                                            <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', $user->pangkat_golongan) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama OPD / Instansi</label>
                                            <input type="text" name="opd_name" value="{{ old('opd_name', $user->opd_name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bio Singkat</label>
                                            <textarea name="bio" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition resize-none">{{ old('bio', $user->bio) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="pt-2">
                                        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">💾 Simpan Perubahan Profil</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    {{-- MODAL IN-PAGE: FORM PENGAJUAN JP EKSTERNAL (FIT ON SCREEN - NO SCROLL) --}}
    <div x-show="showModalEksternal" 
         x-cloak
         @keydown.escape.window="showModalEksternal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-hidden" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        {{-- Backdrop Blur --}}
        <div x-show="showModalEksternal"
             x-transition:enter="ease-out duration-250"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showModalEksternal = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

        {{-- Modal Dialog Card (Compact & Fit-on-screen) --}}
        <div x-show="showModalEksternal"
             x-transition:enter="ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-200 my-auto">
            
            {{-- System Palette Header (Clean, High Contrast, No Black/White Gradient) --}}
            <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200 border-t-4 border-t-blue-600 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        📜
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm sm:text-base font-black text-slate-950 tracking-tight" id="modal-title">
                                Pengajuan JP Pelatihan Eksternal
                            </h3>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-blue-600 text-white shadow-2xs">
                                Mandiri
                            </span>
                        </div>
                        <p class="text-xs text-slate-700 font-medium mt-0.5">Lengkapi formulir bukti pelatihan luar instansi untuk pemenuhan 20 JP tahunan</p>
                    </div>
                </div>
                <button type="button" 
                        @click="showModalEksternal = false" 
                        class="w-8 h-8 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 hover:text-black flex items-center justify-center text-xs font-bold transition">
                    ✕
                </button>
            </div>

            {{-- Modal Form Body (Optimized 2-Column & 3-Column Layout, No-Scroll) --}}
            <form action="{{ route('external-trainings.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5">
                @csrf
                <input type="hidden" name="source" value="dashboard">

                {{-- Baris 1: Nama Pelatihan & Lembaga Penyelenggara (2 Kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="modal_training_name" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                            Nama Pelatihan / Bimtek / Diklat <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" 
                               name="training_name" 
                               id="modal_training_name" 
                               x-model="externalTrainingTitle"
                               value="{{ old('training_name') }}" 
                               placeholder="Contoh: Bimtek Manajemen Kinerja ASN & Kepemimpinan" 
                               required
                               class="w-full px-3.5 py-2 rounded-xl text-xs text-slate-900 font-medium bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-none placeholder:text-slate-500">
                        @error('training_name') <p class="text-[10px] text-rose-600 mt-0.5 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="modal_institution_name" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                            Lembaga Penyelenggara <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" 
                               name="institution_name" 
                               id="modal_institution_name" 
                               value="{{ old('institution_name') }}" 
                               placeholder="Contoh: BPSDM Provinsi Jawa Timur / LAN RI / Kominfo" 
                               required
                               class="w-full px-3.5 py-2 rounded-xl text-xs text-slate-900 font-medium bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-none placeholder:text-slate-500">
                        @error('institution_name') <p class="text-[10px] text-rose-600 mt-0.5 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Baris 2: Tanggal Mulai, Tanggal Selesai & Jumlah JP (3 Kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label for="modal_training_date" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                            Tanggal Mulai <span class="text-rose-600">*</span>
                        </label>
                        <input type="date" 
                               name="training_date" 
                               id="modal_training_date" 
                               value="{{ old('training_date', date('Y-m-d')) }}" 
                               max="{{ date('Y-m-d') }}"
                               required
                               class="w-full px-3.5 py-2 rounded-xl text-xs text-slate-900 font-medium bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-none">
                        @error('training_date') <p class="text-[10px] text-rose-600 mt-0.5 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="modal_training_end_date" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                            Tanggal Selesai <span class="text-slate-600 font-semibold lowercase">(opsional)</span>
                        </label>
                        <input type="date" 
                               name="training_end_date" 
                               id="modal_training_end_date" 
                               value="{{ old('training_end_date') }}" 
                               class="w-full px-3.5 py-2 rounded-xl text-xs text-slate-900 font-medium bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-none">
                    </div>

                    <div>
                        <label for="modal_jp_count" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                            Jumlah Jam Pelajaran <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="jp_count" 
                                   id="modal_jp_count" 
                                   value="{{ old('jp_count', 2) }}" 
                                   min="1" 
                                   max="100" 
                                   required
                                   class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-none pr-10 font-black text-slate-950">
                            <span class="absolute right-3 top-2 text-[11px] font-bold text-slate-700">JP</span>
                        </div>
                        @error('jp_count') <p class="text-[10px] text-rose-600 mt-0.5 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Baris 3: Area Unggah Berkas Ringkas (2 Kolom Bersebelahan, Compact & Clean) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Upload Sertifikat Compact --}}
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                            Unggah Sertifikat / STTP <span class="text-slate-600 font-semibold lowercase">(PDF/JPG maks 5MB)</span>
                        </label>
                        <div class="relative flex items-center justify-between px-3 py-2 rounded-xl border border-dashed border-blue-400 bg-blue-50/50 hover:bg-blue-50 transition cursor-pointer">
                            <input type="file" 
                                   name="certificate_file" 
                                   id="modal_certificate_file" 
                                   accept=".pdf,.jpg,.jpeg,.png,.webp"
                                   @change="certFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="flex items-center gap-2 truncate pr-2">
                                <span class="text-blue-600 text-sm shrink-0">📄</span>
                                <span class="text-xs text-slate-800 font-semibold truncate" x-text="certFileName || 'Pilih dokumen sertifikat...'"></span>
                            </div>
                            <span class="shrink-0 px-2 py-1 rounded-lg text-[10px] font-extrabold text-blue-800 bg-white border border-blue-300 shadow-2xs pointer-events-none">
                                Cari File
                            </span>
                        </div>
                        @error('certificate_file') <p class="text-[10px] text-rose-600 mt-0.5 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Upload Bukti Pendukung Compact --}}
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                            Bukti Pendukung <span class="text-slate-600 font-semibold lowercase">(opsional — presensi / zoom)</span>
                        </label>
                        <div class="relative flex items-center justify-between px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                            <input type="file" 
                                   name="supporting_file" 
                                   id="modal_supporting_file" 
                                   accept=".pdf,.jpg,.jpeg,.png,.webp"
                                   @change="supportFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="flex items-center gap-2 truncate pr-2">
                                <span class="text-slate-600 text-sm shrink-0">📎</span>
                                <span class="text-xs text-slate-800 font-semibold truncate" x-text="supportFileName || 'Pilih bukti pendukung...'"></span>
                            </div>
                            <span class="shrink-0 px-2 py-1 rounded-lg text-[10px] font-extrabold text-slate-800 bg-white border border-slate-300 shadow-2xs pointer-events-none">
                                Cari File
                            </span>
                        </div>
                        @error('supporting_file') <p class="text-[10px] text-rose-600 mt-0.5 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Baris 4: Catatan / Deskripsi Kompetensi (Single Line Input) --}}
                <div>
                    <label for="modal_description" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                        Catatan / Ringkasan Materi <span class="text-slate-600 font-semibold lowercase">(opsional)</span>
                    </label>
                    <input type="text" 
                           name="description" 
                           id="modal_description" 
                           value="{{ old('description') }}" 
                           placeholder="Contoh: Pembelajaran modul penyusunan SKP dan penilaian kinerja ASN berbasis PermenPANRB"
                           class="w-full px-3.5 py-2 rounded-xl text-xs text-slate-900 font-medium bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-none placeholder:text-slate-500">
                </div>

                {{-- Baris 5: Info Singkat & Tombol Aksi (Footer Terpadu) --}}
                <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-200">
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-700 font-semibold">
                        <span class="text-blue-600 text-xs font-bold">ℹ️</span>
                        <span>Diverifikasi oleh Admin sebelum masuk ke 20 JP tahunan.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" 
                                @click="showModalEksternal = false" 
                                class="px-4 py-2 rounded-xl text-xs font-extrabold text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-xs font-black text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Kirim Pengajuan JP</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
