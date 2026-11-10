@extends('layouts.app')

@section('title', $webinar->title . ' | Webinar — ' . config('app.name', 'LMS KAB Berau'))

@section('content')
<div class="space-y-8 pb-16 bg-slate-50/60 min-h-screen">

    {{-- ── HERO SECTION ─────────────────────────────────────── --}}
    <div class="relative bg-gradient-to-br from-slate-950 via-slate-900 to-teal-950 text-white overflow-hidden border-b border-teal-900/40">
        
        {{-- Cover background image (Jelas & Terlihat Nyata) --}}
        @if($webinar->cover_image)
            <div class="absolute inset-0 bg-cover bg-center opacity-60 transition-opacity duration-300"
                 style="background-image: url('{{ $webinar->cover_image }}')"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-900/70 to-slate-950/45"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#14b8a6_1px,transparent_1px)] [background-size:24px_24px] opacity-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 relative z-10">
            
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-teal-300/80 mb-5">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <span>/</span>
                <a href="{{ route('webinars.index') }}" class="hover:text-white transition">Webinar & Agenda</a>
                <span>/</span>
                <span class="text-white truncate max-w-xs sm:max-w-md font-medium">{{ $webinar->title }}</span>
            </nav>

            {{-- Main Hero Row: Left Content + Right Floating Registration Card --}}
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-8 lg:gap-10">
                
                {{-- LEFT COLUMN: Badges, Title, Description, Speaker, Schedule Meta --}}
                <div class="flex-1 space-y-4">
                    
                    {{-- Badges Strip --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $isOnline = $webinar->delivery_mode === 'online';
                            $jpCount = (int) ($webinar->jp ?: ($webinar->course?->jp ?? 2));
                        @endphp

                        {{-- Delivery Mode Badge --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $isOnline ? 'bg-indigo-600 text-white shadow-xs' : 'bg-amber-600 text-white shadow-xs' }}">
                            @if($isOnline)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Live Webinar
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ ucfirst($webinar->delivery_mode) }} In-Class
                            @endif
                        </span>

                        {{-- Status Badge --}}
                        @if($webinar->status === 'ongoing')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-xs animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                Sedang Berlangsung (Live)
                            </span>
                        @elseif($webinar->status === 'completed')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-300">
                                Selesai
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-400/30">
                                Akan Datang
                            </span>
                        @endif

                        {{-- JP Badge (Crucial for ASN) --}}
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-400/20 text-amber-300 border border-amber-400/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>{{ $jpCount }} JP</span>
                        </span>

                        {{-- Certificate Badge --}}
                        @if($webinar->cert_available)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 text-white border border-white/15">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>E-Sertifikat</span>
                            </span>
                        @endif
                    </div>

                    {{-- Title (Judul Besar) --}}
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-snug">
                        {{ $webinar->title }}
                    </h1>

                    {{-- Deskripsi Tepat di Bawah Judul yang Besar (Poin 4) --}}
                    <p class="text-slate-200/90 text-xs sm:text-sm lg:text-base leading-relaxed max-w-3xl">
                        {{ $webinar->description ?: 'Kegiatan pelatihan dan pengembangan kompetensi bagi Aparatur Sipil Negara (ASN) Kabupaten Berau dalam rangka peningkatan kapasitas sumber daya manusia di lingkungan Pemerintah Daerah.' }}
                    </p>

                    {{-- Speaker Pill in Hero --}}
                    @if($webinar->speaker_name)
                        <div class="inline-flex items-center gap-3 p-2.5 pr-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md">
                            @if($webinar->speaker_avatar)
                                <img src="{{ $webinar->speaker_avatar }}" alt="{{ $webinar->speaker_name }}" class="w-10 h-10 rounded-xl object-cover border border-teal-400/30">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-400/30 text-teal-300 font-bold flex items-center justify-center text-sm">
                                    {{ substr($webinar->speaker_name, 0, 2) }}
                                </div>
                            @endif
                            <div class="text-xs">
                                <p class="text-teal-200/80 text-[10px] uppercase font-bold">Narasumber / Fasilitator</p>
                                <p class="font-extrabold text-white text-xs sm:text-sm">{{ $webinar->speaker_name }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Schedule & Venue Meta Grid: Bahasa Indonesia & Tidak Terpotong (Poin 2) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 pt-2">
                        
                        {{-- Card 1: Hari & Tanggal (Nama Hari Bahasa Indonesia) --}}
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-teal-200 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-teal-200/80 text-[10px] uppercase font-extrabold tracking-wider">Hari & Tanggal</p>
                                <p class="font-bold text-white text-xs sm:text-sm whitespace-normal leading-snug">
                                    {{ \Carbon\Carbon::parse($webinar->event_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Card 2: Waktu Pelaksanaan --}}
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-teal-200 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-teal-200/80 text-[10px] uppercase font-extrabold tracking-wider">Waktu Pelaksanaan</p>
                                <p class="font-bold text-white text-xs sm:text-sm font-mono whitespace-normal leading-snug">
                                    {{ $webinar->time_start }} - {{ $webinar->time_end }} WITA
                                </p>
                            </div>
                        </div>

                        {{-- Card 3: Lokasi / Ruang --}}
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-teal-200 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-teal-200/80 text-[10px] uppercase font-extrabold tracking-wider">Ruang / Platform</p>
                                <p class="font-bold text-white text-xs sm:text-sm whitespace-normal leading-snug">
                                    {{ $webinar->location_type ?: ($isOnline ? 'Online Live via Zoom' : 'Sasana Praja Berau') }}
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- RIGHT COLUMN: Compact Registration & Action Card --}}
                <div class="w-full lg:w-96 shrink-0 self-start bg-white rounded-3xl p-5 text-slate-900 shadow-2xl border border-slate-200/90 space-y-4">
                    
                    {{-- Thumbnail Flyer Banner (100% Jelas & Tajam) --}}
                    @if($webinar->cover_image)
                        <div class="relative aspect-video w-full rounded-2xl overflow-hidden shadow-sm border border-slate-100 group">
                            <img src="{{ $webinar->cover_image }}" alt="{{ $webinar->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent pointer-events-none"></div>
                            <div class="absolute bottom-2.5 left-2.5 right-2.5 flex items-center justify-between text-white text-[10px] font-bold">
                                <span class="px-2.5 py-0.5 rounded-full bg-teal-600/90 backdrop-blur-md shadow-xs">Poster Resmi</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-900/80 backdrop-blur-md shadow-xs">{{ $isOnline ? 'Daring (Zoom)' : 'Tatap Muka' }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Pricing Header --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-xs text-slate-500 font-semibold block">Investasi Pelatihan</span>
                            <span class="text-xl sm:text-2xl font-black {{ $webinar->price > 0 ? 'text-slate-900' : 'text-emerald-600' }}">
                                {{ $webinar->price > 0 ? 'Rp ' . number_format($webinar->price, 0, ',', '.') : 'GRATIS' }}
                            </span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $webinar->price > 0 ? 'Berbayar' : 'Bebas Biaya' }}
                        </span>
                    </div>

                    {{-- Kuota Progress Bar --}}
                    @php
                        $regCount = $webinar->registrations->count();
                        $percent = $webinar->quota > 0 ? min(100, round(($regCount / $webinar->quota) * 100)) : 0;
                    @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600 font-semibold">Sisa Kuota:</span>
                            <span class="font-mono font-black text-slate-900">{{ $webinar->remaining_quota }} Kursi</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $regCount }} Terdaftar</span>
                            <span>Total Kuota: {{ $webinar->quota }}</span>
                        </div>
                    </div>

                    {{-- ACTION / REGISTRATION / ATTENDANCE BUTTONS --}}
                    @auth
                        @if($registration)
                            {{-- User Already Registered Card --}}
                            <div class="p-4 bg-emerald-50/90 border border-emerald-200 rounded-2xl text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 text-emerald-800 font-bold">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Status: Terdaftar
                                    </span>
                                    <span class="font-mono font-black text-slate-900 text-xs">{{ $registration->ticket_code }}</span>
                                </div>
                                @if($registration->is_attended)
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-200/80 text-emerald-900">
                                        ✓ Presensi Tercatat: {{ $registration->attended_at?->format('H:i') }} WITA
                                    </div>
                                @endif
                            </div>

                            {{-- Virtual Room (Zoom) Button --}}
                            @if($webinar->webinar_url || $webinar->registration_link)
                                <a href="{{ $webinar->webinar_url ?: $webinar->registration_link }}" target="_blank"
                                   class="w-full py-3.5 px-4 rounded-xl text-xs font-black text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 transition shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Masuk Ruang Sesi (Zoom / Live)</span>
                                </a>
                            @endif

                            {{-- Self Attendance Button (KAK Poin 9) --}}
                            @if(!$registration->is_attended)
                                <form action="{{ route('webinars.attendance', $webinar->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full py-3 px-4 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300 transition flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Catat Presensi Kehadiran Saya</span>
                                    </button>
                                </form>
                            @endif

                        @else
                            {{-- Not Registered Yet --}}
                            @if($webinar->remaining_quota > 0)
                                <form action="{{ route('webinars.register', $webinar->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full py-3.5 px-4 rounded-xl text-xs font-black text-white bg-gradient-to-r from-teal-600 via-teal-700 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 transition shadow-lg shadow-teal-600/25 flex items-center justify-center gap-2 group">
                                        <span>Daftar Sesi Pelatihan Ini</span>
                                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full py-3 px-4 rounded-xl text-xs font-bold text-slate-400 bg-slate-100 cursor-not-allowed">
                                    Kuota Peserta Penuh
                                </button>
                            @endif
                        @endif

                    @else
                        {{-- Guest Prompt --}}
                        <a href="{{ route('login') }}"
                           class="w-full py-3.5 px-4 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-teal-700 transition shadow-md flex items-center justify-center gap-2">
                            <span>Masuk / Daftar untuk Mengikuti</span>
                        </a>
                    @endauth

                    {{-- Organizer Footer Note --}}
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Penyelenggara:</span>
                        <span class="font-bold text-slate-700 truncate">{{ $webinar->speaker_institution ?: 'BKPSDM Kabupaten Berau' }}</span>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- ── MAIN CONTENT: 2-COLUMN STRUCTURE ────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- LEFT 2 COLUMNS: Profile, Objectives, Materials, Video, Evaluation --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. TUJUAN PEMBELAJARAN & MANFAAT MATERI BAGI PESERTA --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
                    <div>
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Tentang Sesi & Tujuan Pembelajaran</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {{ $webinar->description ?: 'Kegiatan webinar ini dirancang untuk meningkatkan kompetensi teknis dan manajerial aparatur sipil negara di lingkungan Pemerintah Kabupaten Berau.' }}
                        </p>
                    </div>

                    {{-- Manfaat Materi Webinar bagi Peserta (Kompetensi & Nilai Tambah) --}}
                    <div class="pt-5 border-t border-slate-100 space-y-3">
                        <h3 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-teal-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <span>Manfaat Mempelajari Materi Ini bagi Peserta</span>
                        </h3>
                        <p class="text-xs text-slate-500">
                            Melalui pemahaman materi yang komprehensif pada webinar ini, peserta dan instansi akan memperoleh manfaat strategis berupa:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div class="p-3.5 rounded-2xl bg-teal-50/40 border border-teal-100 flex items-start gap-3">
                                <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 shadow-xs">1</span>
                                <div class="space-y-0.5">
                                    <p class="text-xs font-bold text-slate-900">Pemahaman Regulasi & Interoperabilitas</p>
                                    <p class="text-[11px] text-slate-600 leading-relaxed">Menguasai regulasi dan kebijakan terkait pengembangan kompetensi ASN serta standar nasional pelayanan publik yang berlaku.</p>
                                </div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-teal-50/40 border border-teal-100 flex items-start gap-3">
                                <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 shadow-xs">2</span>
                                <div class="space-y-0.5">
                                    <p class="text-xs font-bold text-slate-900">Akselerasi Kesiapan Akreditasi Faskes</p>
                                    <p class="text-[11px] text-slate-600 leading-relaxed">Memenuhi indikator wajib penilaian akreditasi fasilitas pelayanan kesehatan (Puskesmas, RSUD, dan Klinik Daerah).</p>
                                </div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-teal-50/40 border border-teal-100 flex items-start gap-3">
                                <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 shadow-xs">3</span>
                                <div class="space-y-0.5">
                                    <p class="text-xs font-bold text-slate-900">Keterampilan Integrasi Teknis API</p>
                                    <p class="text-[11px] text-slate-600 leading-relaxed">Mampu melakukan mapping kamus data FHIR, implementasi keamanan kredensial, dan pemecahan kendala sinkronisasi.</p>
                                </div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-teal-50/40 border border-teal-100 flex items-start gap-3">
                                <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 shadow-xs">4</span>
                                <div class="space-y-0.5">
                                    <p class="text-xs font-bold text-slate-900">Peningkatan Indeks Profesionalitas ASN</p>
                                    <p class="text-[11px] text-slate-600 leading-relaxed">Mendukung pemenuhan hak minimal 20 Jam Pelajaran per tahun bagi aparatur serta penguatan talenta digital daerah.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. PROFIL NARASUMBER / INSTRUKTUR AHLI (KAK Poin 4 & 6) --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-7">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profil Narasumber / Fasilitator Ahli</span>
                    </h3>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        @if($webinar->speaker_avatar)
                            <img src="{{ $webinar->speaker_avatar }}" alt="{{ $webinar->speaker_name }}"
                                 class="w-16 h-16 rounded-2xl object-cover border-2 border-teal-500 shadow-sm shrink-0">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-200 text-teal-700 font-black text-xl flex items-center justify-center shrink-0">
                                {{ substr($webinar->speaker_name, 0, 2) }}
                            </div>
                        @endif

                        <div class="space-y-1">
                            <h4 class="text-base sm:text-lg font-black text-slate-900">{{ $webinar->speaker_name }}</h4>
                            <p class="text-xs font-semibold text-teal-700">{{ $webinar->speaker_title ?: 'Pakar / Fasilitator Pembelajaran' }}</p>
                            @if($webinar->speaker_institution)
                                <p class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span>{{ $webinar->speaker_institution }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($webinar->speaker_bio)
                        <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 leading-relaxed">
                            {{ $webinar->speaker_bio }}
                        </div>
                    @endif
                </div>

                {{-- 3. MATERI & DOKUMEN PEMBELAJARAN (KAK Poin 10) --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Materi & Dokumen Pembelajaran ({{ $webinar->materials->count() }})</span>
                        </h3>
                    </div>

                    @if($webinar->materials->isEmpty())
                        <div class="p-8 text-center text-slate-400 text-xs rounded-2xl bg-slate-50/60 border border-dashed border-slate-200">
                            Materi pembelajaran akan diunggah oleh fasilitator sebelum atau setelah sesi berlangsung.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($webinar->materials as $mat)
                                <div class="py-3.5 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center text-base shrink-0 font-bold">
                                            {{ $mat->type_icon ?? '📄' }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-900">{{ $mat->title }}</p>
                                            <span class="text-[10px] text-slate-400 font-mono uppercase">{{ $mat->type }}</span>
                                        </div>
                                    </div>

                                    @auth
                                        <a href="{{ route('webinars.materials.download', [$webinar->slug, $mat->id]) }}"
                                           target="_blank"
                                           class="px-3 py-1.5 rounded-xl text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition flex items-center gap-1.5 shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>Unduh</span>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-slate-400">Masuk untuk unduh</span>
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- 4. REKAMAN VIDEO SETELAH KEGIATAN (KAK Poin 11) --}}
                @if($webinar->recording_url)
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Rekaman Sesi & Video Pembelajaran</span>
                            </h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Playback Tersedia</span>
                        </div>
                        <p class="text-xs text-slate-500">Bagi peserta yang telah terdaftar, Anda dapat menonton kembali rekaman sesi webinar ini untuk pendalaman materi.</p>
                        
                        <div class="aspect-video w-full rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 shadow-inner flex items-center justify-center">
                            @if(str_contains($webinar->recording_url, 'youtube.com') || str_contains($webinar->recording_url, 'youtu.be'))
                                @php
                                    $ytId = '';
                                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $webinar->recording_url, $matches)) {
                                        $ytId = $matches[1];
                                    }
                                @endphp
                                @if($ytId)
                                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen></iframe>
                                @else
                                    <a href="{{ $webinar->recording_url }}" target="_blank" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold hover:bg-purple-700 transition">
                                        Buka Video Rekaman Eksternal ↗
                                    </a>
                                @endif
                            @else
                                <a href="{{ $webinar->recording_url }}" target="_blank" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold hover:bg-purple-700 transition">
                                    Buka Tautan Rekaman Video Sesi ↗
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 5. EVALUASI PEMBELAJARAN (KAK Poin 12 - Kirkpatrick Level 1) --}}
                @auth
                    @if($registration)
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-4"
                             x-data="{
                                ratingMateri: {{ $evaluation->rating_materi ?? 5 }},
                                ratingNarasumber: {{ $evaluation->rating_narasumber ?? 5 }},
                                ratingPlatform: {{ $evaluation->rating_platform ?? 5 }},
                                ratingRelevansi: {{ $evaluation->rating_relevansi ?? 5 }},
                                ratingOverall: {{ $evaluation->rating_overall ?? 5 }},
                             }">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span>Evaluasi & Umpan Balik Kegiatan</span>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Penilaian Anda merupakan syarat penerbitan E-Sertifikat dan pencatatan Learning Record.</p>
                                </div>
                                @if($evaluation)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Evaluasi Terisi
                                    </span>
                                @endif
                            </div>

                            <form action="{{ route('webinars.evaluation', $webinar->slug) }}" method="POST" class="space-y-4 text-xs">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                        <label class="font-bold text-slate-800">1. Kualitas Materi Pelatihan</label>
                                        <div class="flex items-center gap-1 text-lg">
                                            <template x-for="star in [1,2,3,4,5]">
                                                <button type="button" @click="ratingMateri = star" :class="star <= ratingMateri ? 'text-yellow-400' : 'text-slate-300'">★</button>
                                            </template>
                                            <input type="hidden" name="rating_materi" :value="ratingMateri">
                                        </div>
                                    </div>

                                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                        <label class="font-bold text-slate-800">2. Penyampaian Narasumber</label>
                                        <div class="flex items-center gap-1 text-lg">
                                            <template x-for="star in [1,2,3,4,5]">
                                                <button type="button" @click="ratingNarasumber = star" :class="star <= ratingNarasumber ? 'text-yellow-400' : 'text-slate-300'">★</button>
                                            </template>
                                            <input type="hidden" name="rating_narasumber" :value="ratingNarasumber">
                                        </div>
                                    </div>

                                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                        <label class="font-bold text-slate-800">3. Fasilitas & Platform Daring</label>
                                        <div class="flex items-center gap-1 text-lg">
                                            <template x-for="star in [1,2,3,4,5]">
                                                <button type="button" @click="ratingPlatform = star" :class="star <= ratingPlatform ? 'text-yellow-400' : 'text-slate-300'">★</button>
                                            </template>
                                            <input type="hidden" name="rating_platform" :value="ratingPlatform">
                                        </div>
                                    </div>

                                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                        <label class="font-bold text-slate-800">4. Relevansi dengan Tugas ASN</label>
                                        <div class="flex items-center gap-1 text-lg">
                                            <template x-for="star in [1,2,3,4,5]">
                                                <button type="button" @click="ratingRelevansi = star" :class="star <= ratingRelevansi ? 'text-yellow-400' : 'text-slate-300'">★</button>
                                            </template>
                                            <input type="hidden" name="rating_relevansi" :value="ratingRelevansi">
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                    <label class="font-bold text-slate-800">5. Kepuasan Keseluruhan</label>
                                    <div class="flex items-center gap-1 text-xl">
                                        <template x-for="star in [1,2,3,4,5]">
                                            <button type="button" @click="ratingOverall = star" :class="star <= ratingOverall ? 'text-yellow-400' : 'text-slate-300'">★</button>
                                        </template>
                                        <input type="hidden" name="rating_overall" :value="ratingOverall">
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Saran / Masukan untuk Penyelenggara</label>
                                    <textarea name="saran" rows="2" placeholder="Tuliskan saran untuk penyempurnaan sesi berikutnya..."
                                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500">{{ $evaluation->saran ?? '' }}</textarea>
                                </div>

                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-teal-600 transition shadow-sm">
                                    {{ $evaluation ? 'Perbarui Evaluasi' : 'Kirim Evaluasi Sesi' }}
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth

            </div>

            {{-- RIGHT COLUMN: Learning Record & Ketentuan Pelatihan (Poin 3 - Rapi, Tidak Menggunakan List Standar) --}}
            <div class="space-y-6">

                {{-- LEARNING RECORD ASN WIDGET (KAK Poin 13 & 14) --}}
                @auth
                    @if($learningRecord)
                        <div class="bg-gradient-to-br from-slate-900 to-teal-950 text-white rounded-3xl p-6 shadow-xl space-y-5 border border-teal-800/60">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-400/30 uppercase tracking-wider">
                                    Learning Record ASN
                                </span>
                                <h3 class="text-base font-black text-white mt-1">Rekam Jejak Belajar</h3>
                                <p class="text-xs text-teal-200/80">Aktivitas pembelajaran Anda tercatat secara otomatis.</p>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-slate-300">Progres Kegiatan:</span>
                                    <span class="text-teal-300 font-mono">{{ $learningRecord->progress_percent }}%</span>
                                </div>
                                <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-teal-400 to-emerald-400 rounded-full transition-all duration-500"
                                         style="width: {{ $learningRecord->progress_percent }}%"></div>
                                </div>
                            </div>

                            <!-- Checkpoints -->
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-slate-300">1. Registrasi Peserta</span>
                                    <span class="font-bold text-emerald-400">✓ Selesai</span>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-slate-300">2. Presensi Kehadiran</span>
                                    @if($learningRecord->attended)
                                        <span class="font-bold text-emerald-400">✓ Hadir</span>
                                    @else
                                        <span class="text-amber-300 font-semibold">Belum Hadir</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-slate-300">3. Unduh Materi Sesi</span>
                                    @if($learningRecord->material_downloaded)
                                        <span class="font-bold text-emerald-400">✓ Diunduh</span>
                                    @else
                                        <span class="text-slate-400">Belum</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-slate-300">4. Evaluasi Pembelajaran</span>
                                    @if($learningRecord->evaluation_submitted)
                                        <span class="font-bold text-emerald-400">✓ Terisi</span>
                                    @else
                                        <span class="text-amber-300 font-semibold">Belum Mengisi</span>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 border-t border-white/15 flex items-center justify-between text-xs">
                                <span class="text-teal-200">Perolehan Jam Pembelajaran:</span>
                                <span class="text-base font-black text-amber-300 font-mono">+{{ $learningRecord->jp_earned ?: $jpCount }} JP</span>
                            </div>

                            <!-- E-Certificate Action (KAK Poin 13) -->
                            @if($webinar->cert_available)
                                <div class="pt-2">
                                    @if($learningRecord->certificate_issued)
                                        <a href="{{ route('webinars.certificate', $webinar->slug) }}" target="_blank"
                                           class="w-full py-3 px-4 rounded-xl text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-300 transition shadow-lg flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                            <span>Unduh E-Sertifikat Digital</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl text-center text-[11px] text-slate-300">
                                            Lakukan presensi dan kirim evaluasi sesi untuk menerbitkan sertifikat.
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>
                    @endif
                @endauth

                {{-- 1. FASILITAS KEGIATAN (Urutan 1 Sesuai Permintaan) --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-4 text-xs text-slate-600">
                    <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Fasilitas Kegiatan</span>
                    </h4>
                    
                    <div class="space-y-3 pt-1">
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Diakui <strong>{{ $jpCount }} Jam Pelajaran (JP)</strong> resmi pengembangan kompetensi ASN.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                <strong>E-Sertifikat Digital resmi</strong> dengan nomor seri terverifikasi BKPSDM.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Akses unduh <strong>materi presentasi & salindia bahan ajar</strong> narasumber secara lengkap.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Akses playback <strong>rekaman video pembelajaran</strong> untuk belajar mandiri pasca kegiatan.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-lg bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Pencatatan otomatis ke dalam <strong>Learning Record & Portofolio ASN</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 2. KETENTUAN & TATA TERTIB SESI (Urutan 2 Sesuai Permintaan) --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-4 text-xs text-slate-600">
                    <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Ketentuan & Tata Tertib Sesi</span>
                    </h4>
                    
                    <div class="space-y-3 pt-1">
                        <div class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-teal-500 mt-1.5 shrink-0"></span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Peserta wajib mengikuti sesi minimal <strong>80% dari total durasi</strong> pelaksanaan kegiatan.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-teal-500 mt-1.5 shrink-0"></span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Presensi mandiri dibuka mulai <strong>30 menit sebelum jadwal</strong> hingga sesi berakhir melalui portal LMS.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-teal-500 mt-1.5 shrink-0"></span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Sertifikat elektronik resmi dilengkapi nomor seri unik dan tanda tangan digital instansi BKPSDM.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-teal-500 mt-1.5 shrink-0"></span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Learning Record dan akumulasi Jam Pelajaran (JP) otomatis terekam ke Dashboard Profil Pembelajaran ASN.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- BANTUAN & INFORMASI --}}
                <div class="bg-slate-100/70 rounded-3xl border border-slate-200 p-5 text-xs text-slate-600 space-y-2">
                    <p class="font-bold text-slate-800">Butuh Bantuan Teknis?</p>
                    <p class="text-slate-500 leading-relaxed">Jika mengalami kendala saat mengikuti webinar daring, silakan hubungi tim IT Corporate University BKPSDM Kabupaten Berau.</p>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection
