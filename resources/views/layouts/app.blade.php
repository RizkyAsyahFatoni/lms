<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? (($appSetting->app_name ?? 'LMS KAB Berau') . ' | ' . ($appSetting->tagline ?? 'Ekosistem Pembelajaran Mandiri Aparatur Sipil Negara Kabupaten Berau')) }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ $appSetting->favicon_url ?? asset('favicon.svg') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head_extra')
</head>
<body class="h-full flex flex-col bg-[#f8fafc] text-slate-800 font-sans antialiased selection:bg-brand-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-teal-900 text-brand-100 text-xs py-2 px-4 border-b border-brand-800/50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-300 border border-brand-400/30">
                    RESMI
                </span>
                <span class="truncate">Platform Pembelajaran & Kompetensi Digital Terintegrasi Berbasis Standar Nasional</span>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-brand-200 text-[11px]">
                <span class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Operasional Normal
                </span>
                <a href="{{ route('courses.index') }}" class="underline hover:text-white transition">Jelajahi 250+ Modul</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        @if($appSetting && $appSetting->logo_path)
                            <img src="{{ $appSetting->logo_url }}" class="h-10 w-auto max-w-[170px] object-contain" alt="{{ $appSetting->app_name }}">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 via-brand-600 to-teal-500 flex items-center justify-center text-white font-black text-xl shadow-sm group-hover:scale-105 transition-transform duration-200">
                                1
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-slate-900 tracking-tight text-lg leading-tight">
                                        ONE<span class="text-brand-600">LEARNING</span>
                                    </span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-teal-50 text-teal-700 border border-teal-200/60 uppercase">{{ $appSetting->badge_text ?? 'PRO' }}</span>
                                </div>
                                <span class="text-[10px] text-slate-500 font-medium tracking-wide">{{ $appSetting->tagline ?? 'Enterprise Learning Platform' }}</span>
                            </div>
                        @endif
                    </a>

                    <!-- Desktop Navigation Links -->
                    <nav class="hidden md:flex items-center gap-2 text-xs font-semibold">
                        <!-- 1. Beranda -->
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition whitespace-nowrap {{ request()->routeIs('home') ? 'text-brand-700 bg-brand-50 border-brand-200/70' : 'text-slate-600 bg-white border-slate-200 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Beranda
                        </a>

                        <!-- 2. Katalog -->
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition whitespace-nowrap {{ (request()->routeIs('courses.index') && !request('format') && !request('type')) ? 'text-brand-700 bg-brand-50 border-brand-200/70' : 'text-slate-600 bg-white border-slate-200 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Katalog
                        </a>

                        <!-- 3. Materi Pembelajaran (Dropdown: Ecourse, Inclass, Webinar) -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition whitespace-nowrap {{ (request('format') === 'ecourse' || request()->routeIs('in-class.*') || request()->routeIs('webinars.*')) ? 'text-brand-700 bg-brand-50 border-brand-200/70 font-bold' : 'text-slate-600 bg-white border-slate-200 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300' }}">
                                <svg class="w-4 h-4 flex-shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>Materi Pembelajaran</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 style="display: none;"
                                 class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-200/90 py-2 z-50 text-xs">
                                
                                <a href="{{ route('courses.index', ['format' => 'ecourse']) }}" 
                                   class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 transition {{ request('format') === 'ecourse' ? 'text-brand-700 bg-brand-50/60 font-bold' : 'text-slate-700' }}">
                                    <div class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 border border-brand-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-800">Ecourse</span>
                                        <span class="text-[10px] text-slate-400">Pembelajaran daring mandiri</span>
                                    </div>
                                </a>

                                <a href="{{ route('in-class.index') }}" 
                                   class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 transition {{ request()->routeIs('in-class.*') ? 'text-amber-800 bg-amber-50/60 font-bold' : 'text-slate-700' }}">
                                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-800">Inclass</span>
                                        <span class="text-[10px] text-slate-400">Tatap muka & bimbingan teknis</span>
                                    </div>
                                </a>

                                <a href="{{ route('webinars.index') }}" 
                                   class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 transition {{ request()->routeIs('webinars.*') ? 'text-teal-800 bg-teal-50/60 font-bold' : 'text-slate-700' }}">
                                    <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 border border-teal-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-800">Webinar</span>
                                        <span class="text-[10px] text-slate-400">Seminar daring & interaktif</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- 4. Kalender -->
                        <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition whitespace-nowrap {{ request()->routeIs('calendar.*') ? 'text-purple-700 bg-purple-50 border-purple-200/70 font-bold' : 'text-slate-600 bg-white border-slate-200 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300' }}">
                            <svg class="w-4 h-4 flex-shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Kalender
                        </a>
                    </nav>
                </div>

                <!-- Right Side Actions (Search, Auth, Dashboard) -->
                <div class="flex items-center gap-3">
                    
                    <!-- Search bar with v0-style Ctrl+K badge -->
                    <form action="{{ route('courses.index') }}" method="GET" class="hidden lg:flex items-center relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari materi atau topik..." 
                               class="w-64 pl-9 pr-14 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="absolute right-2.5 px-1.5 py-0.5 text-[10px] font-mono font-medium text-slate-400 bg-white border border-slate-200 rounded shadow-[0_1px_1px_rgba(0,0,0,0.04)]">⌘K</span>
                    </form>

                    @auth
                        <!-- Student Dashboard & Profile Menu -->
                        <div class="flex items-center gap-2">
                            {{-- Role-specific Quick Navigation Pill --}}
                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-purple-900 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition shadow-xs whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse flex-shrink-0"></span>
                                    <span>Konsol Admin</span>
                                </a>
                            @elseif(auth()->user()->isPimpinan())
                                <a href="{{ route('dashboard.pimpinan') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition shadow-xs whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse flex-shrink-0"></span>
                                    <span>Dashboard Eksekutif</span>
                                </a>
                            @elseif(auth()->user()->isAdminOpd())
                                <a href="{{ route('dashboard.admin_opd') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-900 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-xs whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse flex-shrink-0"></span>
                                    <span>Dashboard OPD</span>
                                </a>
                            @elseif(auth()->user()->isNarasumber())
                                <a href="{{ route('dashboard.narasumber') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition shadow-xs whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse flex-shrink-0"></span>
                                    <span>Panel Pengajar</span>
                                </a>
                            @elseif(auth()->user()->isReviewerTalent())
                                <a href="{{ route('dashboard.reviewer_talent') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-teal-900 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition shadow-xs whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-teal-600 animate-pulse flex-shrink-0"></span>
                                    <span>Panel Talenta</span>
                                </a>
                            @endif

                            <a href="{{ route('dashboard.learner') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100/80 border border-brand-200/70 transition whitespace-nowrap">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>Kelas Saya</span>
                            </a>

                            <!-- Lonceng Notifikasi In-App (KAK Bab XXIII & Bab IX) -->
                            @include('partials.notification-bell')

                            <!-- User Dropdown (Alpine) -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" type="button" class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100 transition focus:outline-none">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-600 to-teal-400 text-white font-bold text-xs flex items-center justify-center ring-2 ring-white shadow-sm overflow-hidden shrink-0">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr(auth()->user()->name, 0, 2) }}
                                        @endif
                                    </div>
                                    <span class="hidden md:block text-xs font-semibold text-slate-700 max-w-[120px] truncate">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200/80 py-1.5 z-50 text-xs">
                                    <div class="px-3 py-2 border-b border-slate-100">
                                        <div class="flex items-center justify-between">
                                            <p class="font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">{{ auth()->user()->role_name ?? auth()->user()->role }}</span>
                                        </div>
                                        <p class="text-slate-400 text-[11px] truncate">{{ auth()->user()->email }}</p>
                                    </div>

                                    {{-- Role Dashboard Link in Dropdown --}}
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-purple-900 bg-purple-50/60 font-semibold hover:bg-purple-50 transition">
                                            <span>🛡️</span> Konsol Super Admin
                                        </a>
                                    @elseif(auth()->user()->isPimpinan())
                                        <a href="{{ route('dashboard.pimpinan') }}" class="flex items-center gap-2 px-3 py-2 text-rose-900 bg-rose-50/60 font-semibold hover:bg-rose-50 transition">
                                            <span>👔</span> Dashboard Eksekutif
                                        </a>
                                    @elseif(auth()->user()->isAdminOpd())
                                        <a href="{{ route('dashboard.admin_opd') }}" class="flex items-center gap-2 px-3 py-2 text-blue-900 bg-blue-50/60 font-semibold hover:bg-blue-50 transition">
                                            <span>🏛️</span> Dashboard Pengelola OPD
                                        </a>
                                    @elseif(auth()->user()->isNarasumber())
                                        <a href="{{ route('dashboard.narasumber') }}" class="flex items-center gap-2 px-3 py-2 text-amber-900 bg-amber-50/60 font-semibold hover:bg-amber-50 transition">
                                            <span>👨‍🏫</span> Panel Narasumber
                                        </a>
                                    @elseif(auth()->user()->isReviewerTalent())
                                        <a href="{{ route('dashboard.reviewer_talent') }}" class="flex items-center gap-2 px-3 py-2 text-teal-900 bg-teal-50/60 font-semibold hover:bg-teal-50 transition">
                                            <span>🔍</span> Panel Talenta & IDP
                                        </a>
                                    @endif

                                    <a href="{{ route('dashboard.learner') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                                        <span>🎓</span> Kelas & Riwayat Belajar Saya
                                    </a>
                                    <a href="{{ route('external-trainings.index') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Target & JP Eksternal
                                    </a>
                                    <a href="{{ route('dashboard.learner', ['tab' => 'profil']) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Pengelolaan Profil & Akun
                                    </a>
                                    <a href="{{ route('attendance.history') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        Riwayat Presensi Saya
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Keluar Akun
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Guest Single Action (Masuk / Daftar) -->
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm shadow-brand-600/20 transition-all hover:shadow whitespace-nowrap">
                            <span>Masuk / Daftar</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @endauth

                    <!-- Mobile Menu Trigger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden border-b border-slate-200 bg-white px-4 py-3 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Beranda</a>
            <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Katalog</a>
            
            <!-- Mobile Materi Pembelajaran Sub Menu -->
            <div x-data="{ subOpen: false }" class="space-y-1">
                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Materi Pembelajaran
                    </span>
                    <svg class="w-4 h-4 transition-transform text-slate-400" :class="subOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="subOpen" class="pl-6 space-y-1 py-1">
                    <a href="{{ route('courses.index', ['format' => 'ecourse']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-brand-600">
                        • Ecourse
                    </a>
                    <a href="{{ route('in-class.index') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-brand-600">
                        • Inclass
                    </a>
                    <a href="{{ route('webinars.index') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-brand-600">
                        • Webinar
                    </a>
                </div>
            </div>

            <a href="{{ route('calendar.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-purple-700 hover:bg-purple-50">Kalender</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-brand-700 bg-brand-50">Dashboard Saya</a>
            @endauth
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="flex items-center gap-3 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="flex items-center gap-3 p-3.5 bg-sky-50 border border-sky-200 text-sky-800 rounded-xl text-xs font-medium shadow-sm">
                <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Main Footer -->
    <footer class="bg-white border-t border-slate-200/80 mt-16 text-slate-600 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Col 1: Brand Info & Address -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        @if($appSetting && $appSetting->logo_path)
                            <img src="{{ $appSetting->logo_url }}" class="h-8 w-auto max-w-[150px] object-contain" alt="{{ $appSetting->app_name }}">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-700 to-teal-500 flex items-center justify-center text-white font-black text-sm shadow-sm">
                                1
                            </div>
                            <span class="font-black text-slate-900 tracking-tight text-base">LMS <span class="text-brand-600">KAB BERAU</span></span>
                        @endif
                    </div>
                    <p class="text-slate-500 leading-relaxed text-xs">
                        {{ $appSetting->footer_description ?? 'Platform pengembangan kompetensi terpadu bagi ASN, PPPK, dan seluruh aparatur perangkat daerah Pemerintah Kabupaten Berau.' }}
                    </p>
                    @if($appSetting && $appSetting->footer_address)
                        <div class="flex items-start gap-1.5 text-slate-400 text-[11px] pt-1">
                            <svg class="w-3.5 h-3.5 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $appSetting->footer_address }}</span>
                        </div>
                    @endif

                    <!-- Social Media Links -->
                    <div class="flex items-center gap-2 pt-2">
                        @if($appSetting && $appSetting->social_instagram)
                            <a href="{{ $appSetting->social_instagram }}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-pink-50 hover:text-pink-600 text-slate-500 flex items-center justify-center transition font-bold text-[10px]" title="Instagram">IG</a>
                        @endif
                        @if($appSetting && $appSetting->social_youtube)
                            <a href="{{ $appSetting->social_youtube }}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-500 flex items-center justify-center transition font-bold text-[10px]" title="YouTube">YT</a>
                        @endif
                        @if($appSetting && $appSetting->social_linkedin)
                            <a href="{{ $appSetting->social_linkedin }}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-500 flex items-center justify-center transition font-bold text-[10px]" title="LinkedIn">IN</a>
                        @endif
                        @if($appSetting && $appSetting->social_tiktok)
                            <a href="{{ $appSetting->social_tiktok }}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition font-bold text-[10px]" title="TikTok">TT</a>
                        @endif
                        @if($appSetting && $appSetting->social_twitter)
                            <a href="{{ $appSetting->social_twitter }}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-900 flex items-center justify-center transition font-bold text-[10px]" title="Twitter / X">𝕏</a>
                        @endif
                        @if($appSetting && $appSetting->social_facebook)
                            <a href="{{ $appSetting->social_facebook }}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-500 flex items-center justify-center transition font-bold text-[10px]" title="Facebook">FB</a>
                        @endif
                    </div>
                </div>

                <!-- Col 2: Kurikulum & Kategori -->
                <div class="space-y-3">
                    <h4 class="font-semibold text-slate-900 tracking-tight text-xs uppercase">Kurikulum & Program</h4>
                    <ul class="space-y-2 text-slate-500">
                        <li><a href="{{ route('courses.index', ['category' => 'kompetensi-teknis-digital-spbe']) }}" class="hover:text-brand-600 transition">Kompetensi Teknis Digital & SPBE</a></li>
                        <li><a href="{{ route('courses.index', ['category' => 'kompetensi-manajerial']) }}" class="hover:text-brand-600 transition">Kompetensi Manajerial ASN</a></li>
                        <li><a href="{{ route('courses.index', ['category' => 'kompetensi-teknis-umum']) }}" class="hover:text-brand-600 transition">Kompetensi Teknis Umum & Keuangan</a></li>
                        <li><a href="{{ route('courses.index', ['category' => 'mandatory-pelatihan-wajib']) }}" class="hover:text-brand-600 transition">Mandatory & Pelatihan Wajib</a></li>
                        <li><a href="{{ route('trainer.register') }}" class="inline-flex items-center gap-1 font-semibold text-brand-600 hover:text-brand-700 transition"><span>🎓 Gabung Sebagai Trainer</span> <span class="text-[10px]">→</span></a></li>
                    </ul>
                </div>

                <!-- Col 3: Fitur & Fasilitas -->
                <div class="space-y-3">
                    <h4 class="font-semibold text-slate-900 tracking-tight text-xs uppercase">Fasilitas Platform</h4>
                    <ul class="space-y-2 text-slate-500">
                        <li><a href="#" class="hover:text-brand-600 transition">Sertifikat Digital Terverifikasi QR</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Akses Modul Kapan Saja (24/7)</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Kurikulum Berstandar Kompetensi Nasional</a></li>
                        <li><a href="{{ route('courses.index') }}" class="hover:text-brand-600 transition">Katalog Modul Pembelajaran</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-brand-600 transition">Artikel & Wawasan ASN</a></li>
                    </ul>
                </div>

                <!-- Col 4: Pusat Bantuan & Kontak -->
                <div class="space-y-3">
                    <h4 class="font-semibold text-slate-900 tracking-tight text-xs uppercase">Pusat Bantuan</h4>
                    <p class="text-slate-500 text-xs">
                        Memerlukan bantuan pendaftaran atau integrasi instansi? Tim fasilitator kami siap mendampingi.
                    </p>
                    <div class="space-y-1.5 text-xs text-slate-600 font-medium">
                        <p class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ $appSetting->contact_email ?? 'bkpsdm@beraukab.go.id' }}</span>
                        </p>
                        @if($appSetting && $appSetting->contact_phone)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{ $appSetting->contact_phone }}</span>
                            </p>
                        @endif
                        <p class="flex items-center gap-1.5 text-[11px] text-slate-400 pt-0.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Senin - Jumat, 08.00 - 16.00 WIB</span>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="border-t border-slate-100 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-400 text-[11px]">
                <p>{{ $appSetting->footer_copyright ?? ('© ' . date('Y') . ' Pemerintah Kabupaten Berau - BKPSDM. Seluruh Hak Cipta Dilindungi.') }}</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-slate-600 transition">Kebijakan Privasi</a>
                    <span>•</span>
                    <a href="#" class="hover:text-slate-600 transition">Syarat & Ketentuan</a>
                    <span>•</span>
                    <a href="{{ route('trainer.register') }}" class="hover:text-brand-600 font-semibold transition">Pendaftaran Trainer</a>
                </div>
            </div>
        </div>
    </footer>

@yield('scripts')
</body>
</html>
