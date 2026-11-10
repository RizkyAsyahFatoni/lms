<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Management Console') | {{ $appSetting->app_name ?? 'LMS KAB Berau' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ $appSetting->favicon_url ?? asset('favicon.svg') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans, Playfair Display, Great Vibes, Cinzel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#f8fafc] text-slate-900 font-sans antialiased selection:bg-brand-500 selection:text-white"
      x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex flex-col lg:flex-row">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 lg:hidden"
             style="display: none;"></div>

        <!-- Left Sidebar (v0.dev Standard Precision Sidebar) -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/90 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 select-none shadow-[4px_0_24px_rgba(0,0,0,0.02)]"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Top Section: Brand & Workspace Switcher -->
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                        @if($appSetting && $appSetting->logo_path)
                            <img src="{{ $appSetting->logo_url }}" class="h-9 w-auto max-w-[130px] object-contain" alt="{{ $appSetting->app_name }}">
                        @else
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-700 via-brand-600 to-teal-500 text-white font-black text-sm flex items-center justify-center shadow-md shadow-brand-600/20 group-hover:scale-105 transition">
                                1
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-slate-900 tracking-tight text-base leading-none">{{ $appSetting->app_name ?? 'LMS KAB Berau' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-black bg-brand-50 text-brand-700 border border-brand-200/80">{{ $appSetting->badge_text ?? 'OFFICIAL' }}</span>
                                </div>
                                <p class="text-[10px] font-mono font-medium text-slate-400 mt-0.5 uppercase tracking-wider">Management Console</p>
                            </div>
                        @endif
                    </a>

                    <!-- Close button for mobile -->
                    <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Workspace Pill -->
                <div class="mt-4 p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></div>
                        <span class="text-xs font-bold text-slate-800 truncate">LMS Berau Core</span>
                    </div>
                    <span class="text-[9px] font-mono font-bold uppercase px-1.5 py-0.5 rounded bg-white text-slate-500 border border-slate-200 shadow-2xs">ADMIN</span>
                </div>
            </div>

            <!-- Navigation Links Section (Grouped Tree Structure) -->
            <div class="flex-1 overflow-y-auto px-3.5 py-3 space-y-4 text-xs select-none">

                <!-- 1. OVERVIEW -->
                @php
                    $isOverviewActive = request()->routeIs('admin.dashboard') || request()->routeIs('admin.activity-logs.*');
                @endphp
                <div x-data="{ open: true }" class="space-y-1">
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2 py-1 text-slate-500 hover:text-slate-800 transition group">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">OVERVIEW</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="{ '-rotate-90': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" class="relative ml-4 pl-4 border-l border-slate-200/90 space-y-1 mt-1">
                        <!-- Dashboard -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.dashboard') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-sky-50 text-sky-600 border border-sky-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                </div>
                                <span class="truncate flex-1">Dashboard</span>
                            </a>
                        </div>

                        <!-- Activity Logs -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.activity-logs.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.activity-logs.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-slate-100 text-slate-600 border border-slate-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                </div>
                                <span class="truncate flex-1">Log Aktivitas Sistem</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. VERIFIKASI & PERSETUJUAN -->
                @php
                    $isVerificationActive = request()->routeIs('admin.verifications.*') || request()->routeIs('admin.users.verifications.*') || request()->routeIs('admin.external-trainings.*') || request()->routeIs('admin.opd-requests.*');
                @endphp
                <div x-data="{ open: true }" class="space-y-1">
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2 py-1 text-slate-500 hover:text-slate-800 transition group">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">VERIFIKASI & PERSETUJUAN</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if(($totalPendingVerifications ?? 0) > 0)
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            @endif
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="{ '-rotate-90': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <div x-show="open" class="relative ml-4 pl-4 border-l border-slate-200/90 space-y-1 mt-1">
                        <!-- Antrean Persetujuan -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.verifications.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.verifications.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-rose-50 text-rose-600 border border-rose-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="truncate flex-1">Antrean Persetujuan</span>
                                @if(($totalPendingVerifications ?? 0) > 0)
                                    <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-bold font-mono">
                                        {{ $totalPendingVerifications }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        <!-- Verifikasi Data Pegawai -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.users.verifications.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.users.verifications.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <span class="truncate flex-1">Verifikasi Data Pegawai</span>
                                @if(($pendingAsnCount ?? 0) > 0)
                                    <span class="px-1.5 py-0.2 rounded-full bg-blue-600 text-white text-[9px] font-bold font-mono">
                                        {{ $pendingAsnCount }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        <!-- Validasi Pelatihan Luar -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.external-trainings.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.external-trainings.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                </div>
                                <span class="truncate flex-1">Validasi Pelatihan Luar</span>
                                @if(($pendingExternalCount ?? 0) > 0)
                                    <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[9px] font-bold font-mono">
                                        {{ $pendingExternalCount }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        <!-- Usulan Pelatihan OPD -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.opd-requests.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.opd-requests.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-purple-50 text-purple-600 border border-purple-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span class="truncate flex-1">Usulan Pelatihan OPD</span>
                                @php
                                    $pendingOpdCount = \App\Models\OpdActivityRequest::where('status', 'pending')->count();
                                @endphp
                                @if($pendingOpdCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[9px] font-bold font-mono">
                                        {{ $pendingOpdCount }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. MANAJEMEN PEMBELAJARAN -->
                @php
                    $isLearningActive = request()->routeIs('admin.courses.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.webinars.*') || request()->routeIs('admin.submissions.*') || request()->routeIs('admin.question-banks.*') || request()->routeIs('admin.attendances.*');
                @endphp
                <div x-data="{ open: true }" class="space-y-1">
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2 py-1 text-slate-500 hover:text-slate-800 transition group">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">MANAJEMEN PEMBELAJARAN</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="{ '-rotate-90': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" class="relative ml-4 pl-4 border-l border-slate-200/90 space-y-1 mt-1">
                        <!-- Kursus & Pelatihan -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.courses.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.courses.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <span class="truncate flex-1">Kursus & Pelatihan</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-500">
                                    {{ \App\Models\Course::count() }}
                                </span>
                            </a>
                        </div>

                        <!-- Kategori Pelatihan -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.categories.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-teal-50 text-teal-600 border border-teal-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <span class="truncate flex-1">Kategori Pelatihan</span>
                            </a>
                        </div>

                        <!-- Webinar & Kelas Live (Moved to Pembelajaran) -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.webinars.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.webinars.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-red-50 text-red-600 border border-red-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="truncate flex-1">Webinar & Kelas Live</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-500">
                                    {{ \App\Models\Webinar::count() }}
                                </span>
                            </a>
                        </div>

                        <!-- Penilaian Tugas Peserta -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.submissions.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.submissions.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-orange-50 text-orange-600 border border-orange-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </div>
                                <span class="truncate flex-1">Penilaian Tugas Peserta</span>
                                @php
                                    $pendingSubmissionsCount = \App\Models\AssignmentSubmission::where('status', 'submitted')->count();
                                @endphp
                                @if($pendingSubmissionsCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[9px] font-bold font-mono">
                                        {{ $pendingSubmissionsCount }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        <!-- Bank Soal & Ujian -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.question-banks.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.question-banks.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-cyan-50 text-cyan-600 border border-cyan-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span class="truncate flex-1">Bank Soal & Ujian</span>
                            </a>
                        </div>

                        <!-- Sesi & Presensi QR -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.attendances.sessions') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.attendances.sessions') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-purple-50 text-purple-600 border border-purple-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </div>
                                <span class="truncate flex-1">Sesi & Presensi QR</span>
                            </a>
                        </div>

                        <!-- Rekapitulasi Presensi -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.attendances.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ (request()->routeIs('admin.attendances.*') && !request()->routeIs('admin.attendances.sessions')) ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </div>
                                <span class="truncate flex-1">Rekapitulasi Presensi</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 4. DATA PEGAWAI & INSTRUKTUR -->
                @php
                    $isAsnActive = (request()->routeIs('admin.users.*') && !request()->routeIs('admin.users.verifications.*')) || request()->routeIs('admin.idp.*') || request()->routeIs('admin.trainers.*');
                @endphp
                <div x-data="{ open: true }" class="space-y-1">
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2 py-1 text-slate-500 hover:text-slate-800 transition group">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">DATA PEGAWAI & INSTRUKTUR</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="{ '-rotate-90': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" class="relative ml-4 pl-4 border-l border-slate-200/90 space-y-1 mt-1">
                        <!-- Manajemen Pengguna -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.users.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ (request()->routeIs('admin.users.*') && !request()->routeIs('admin.users.verifications.*')) ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <span class="truncate flex-1">Manajemen Pengguna</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-500">
                                    {{ \App\Models\User::count() }}
                                </span>
                            </a>
                        </div>

                        <!-- Rencana Pengembangan (IDP) -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.idp.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.idp.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                </div>
                                <span class="truncate flex-1">Rencana Pengembangan (IDP)</span>
                            </a>
                        </div>

                        <!-- Instruktur & Fasilitator -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.trainers.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.trainers.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-purple-50 text-purple-600 border border-purple-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                </div>
                                <span class="truncate flex-1">Instruktur & Fasilitator</span>
                                @php
                                    $pendingTrainersCount = \App\Models\User::where('trainer_status', 'pending')->count();
                                @endphp
                                @if($pendingTrainersCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[9px] font-bold font-mono">
                                        {{ $pendingTrainersCount }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 5. PUSAT LAPORAN & STATISTIK -->
                @php
                    $isReportActive = request()->routeIs('admin.reports.*') || request()->routeIs('admin.jp.*');
                @endphp
                <div x-data="{ open: true }" class="space-y-1">
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2 py-1 text-slate-500 hover:text-slate-800 transition group">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">PUSAT LAPORAN & STATISTIK</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="{ '-rotate-90': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" class="relative ml-4 pl-4 border-l border-slate-200/90 space-y-1 mt-1">
                        <!-- Katalog Laporan -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.reports.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.reports.index') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-teal-50 text-teal-600 border border-teal-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span class="truncate flex-1">Katalog Laporan</span>
                            </a>
                        </div>

                        <!-- Report Builder -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.reports.builder') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.reports.builder') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                </div>
                                <span class="truncate flex-1">Pembuat Laporan Kustom</span>
                            </a>
                        </div>

                        <!-- Pantauan Jam Pelajaran -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.jp.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ (request()->routeIs('admin.jp.*') && !request()->routeIs('admin.jp.asn*') && !request()->routeIs('admin.jp.opd*')) ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-violet-50 text-violet-600 border border-violet-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="truncate flex-1">Pantauan Jam Pelajaran (JP)</span>
                            </a>
                        </div>

                        <!-- Rekap JP Pegawai -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.jp.asn') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.jp.asn*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <span class="truncate flex-1">Rekap JP Pegawai</span>
                            </a>
                        </div>

                        <!-- Rekap JP Unit Kerja -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.jp.opd') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.jp.opd*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-rose-50 text-rose-600 border border-rose-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span class="truncate flex-1">Rekap JP Unit Kerja (OPD)</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 6. PORTAL & PUBLIKASI (Strictly: Popup Banner, Slider, Artikel & Regulasi) -->
                @php
                    $isPortalActive = request()->routeIs('admin.popups.*') || request()->routeIs('admin.sliders.*') || request()->routeIs('admin.articles.*');
                @endphp
                <div x-data="{ open: true }" class="space-y-1">
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2 py-1 text-slate-500 hover:text-slate-800 transition group">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">PORTAL & PUBLIKASI</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="{ '-rotate-90': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" class="relative ml-4 pl-4 border-l border-slate-200/90 space-y-1 mt-1">
                        <!-- Popup Banner -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.popups.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.popups.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                </div>
                                <span class="truncate flex-1">Popup Banner</span>
                            </a>
                        </div>

                        <!-- Slider Beranda -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.sliders.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.sliders.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-pink-50 text-pink-600 border border-pink-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="truncate flex-1">Slider Beranda</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-pink-50 text-pink-700">
                                    {{ \App\Models\Slider::count() }}
                                </span>
                            </a>
                        </div>

                        <!-- Artikel & Regulasi -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.articles.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.articles.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-sky-50 text-sky-600 border border-sky-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                </div>
                                <span class="truncate flex-1">Artikel & Regulasi</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-500">
                                    {{ \App\Models\Article::count() }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 7. PENGATURAN & SISTEM -->
                @php
                    $isSystemActive = request()->routeIs('admin.settings.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.api.*') || request()->routeIs('admin.sso.*') || request()->routeIs('admin.simpeg.*') || request()->routeIs('admin.mail.*');
                @endphp
                <div x-data="{ open: true }" class="space-y-1">
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2 py-1 text-slate-500 hover:text-slate-800 transition group">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">PENGATURAN & SISTEM</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="{ '-rotate-90': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" class="relative ml-4 pl-4 border-l border-slate-200/90 space-y-1 mt-1">
                        <!-- Pengaturan & Tampilan LMS (Includes UI Grid & Branding) -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.settings.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ (request()->routeIs('admin.settings.*') || request()->routeIs('admin.branding.*') || request()->routeIs('admin.footer.*')) ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-slate-100 text-slate-600 border border-slate-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <span class="truncate flex-1">Pengaturan & Tampilan UI</span>
                            </a>
                        </div>

                        <!-- Peran & Hak Akses Pengguna (Replaces '6 Role KAK') -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.roles.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.roles.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-purple-50 text-purple-600 border border-purple-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="truncate flex-1">Peran & Hak Akses</span>
                            </a>
                        </div>

                        <!-- Matriks Izin Akses -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.permissions.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.permissions.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-teal-50 text-teal-600 border border-teal-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </div>
                                <span class="truncate flex-1">Matriks Izin Akses</span>
                            </a>
                        </div>

                        <!-- Integrasi API Gateway -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.api.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.api.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </div>
                                <span class="truncate flex-1">Integrasi API Gateway</span>
                            </a>
                        </div>

                        <!-- Single Sign-On (SSO) -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.sso.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.sso.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-cyan-50 text-cyan-600 border border-cyan-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <span class="truncate flex-1">Single Sign-On (SSO)</span>
                                @php
                                    $ssoIsActive = \App\Models\SsoSetting::getSettings()->is_active;
                                @endphp
                                <span class="w-2 h-2 rounded-full {{ $ssoIsActive ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                            </a>
                        </div>

                        <!-- Sinkronisasi SIMPEG -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.simpeg.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.simpeg.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="truncate flex-1">Sinkronisasi SIMPEG</span>
                                @php
                                    $simpegIsActive = \App\Models\SimpegSetting::getSettings()->is_active;
                                @endphp
                                <span class="w-2 h-2 rounded-full {{ $simpegIsActive ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                            </a>
                        </div>

                        <!-- Email SMTP & Notifikasi Otomatis -->
                        <div class="relative flex items-center">
                            <div class="absolute -left-4 top-1/2 w-3.5 border-t border-slate-200/90"></div>
                            <a href="{{ route('admin.mail.index') }}" 
                               class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('admin.mail.*') ? 'bg-violet-100/70 text-violet-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-medium' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="truncate flex-1">Email SMTP & Notifikasi</span>
                                @php
                                    $mailIsActive = \App\Models\MailSetting::getSettings()->is_active;
                                @endphp
                                <span class="w-2 h-2 rounded-full {{ $mailIsActive ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}" title="{{ $mailIsActive ? 'SMTP Aktif' : 'SMTP Nonaktif' }}"></span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Section: Floating Profile Card & Frontend Link -->
            <div class="p-3.5 border-t border-slate-100 bg-slate-50/60 space-y-2.5">
                
                <!-- Quick Visit Frontend Button -->
                <a href="{{ route('home') }}" target="_blank"
                   class="w-full flex items-center justify-between px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 hover:text-slate-900 shadow-2xs transition group">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Lihat Website Beranda</span>
                    </span>
                    <svg class="w-3 h-3 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- User Profile Pill Card (Mirrors Screenshot Reference) -->
                <div class="p-2.5 bg-white rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between gap-2.5">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 flex-1 hover:opacity-85 transition group" title="Pengaturan Profil">
                        <div class="w-9 h-9 rounded-full text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs ring-2 ring-indigo-100" style="background-color: #4f46e5; color: #ffffff;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate leading-tight group-hover:text-violet-700 transition">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[10px] text-slate-400 capitalize truncate mt-0.5">{{ auth()->user()->role ?? 'Admin' }}</p>
                        </div>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" 
                                class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" 
                                title="Keluar / Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        <!-- Right Main Content Area -->
        <div class="flex-1 min-w-0 flex flex-col min-h-screen">
            
            <!-- Sticky Admin Top Header Bar -->
            <header class="h-16 px-4 sm:px-6 lg:px-8 bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <!-- Mobile Sidebar Toggle Button -->
                    <button @click="sidebarOpen = true" 
                            type="button" 
                            class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Breadcrumbs Slot or Default Breadcrumbs -->
                    <nav class="flex items-center gap-2 text-xs text-slate-500">
                        <a href="{{ route('admin.dashboard') }}" class="font-bold text-slate-700 hover:text-brand-600 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                            Admin
                        </a>
                        <span>/</span>
                        <span class="text-slate-900 font-semibold truncate">@yield('page_title', 'Workspace')</span>
                    </nav>
                </div>

                <!-- Right Header Actions: Status & Quick Controls -->
                <div class="flex items-center gap-3">

                    <!-- Notification Bell & Badge Verifikasi (Data ASN & JP Eksternal) -->
                    @if(($totalPendingVerifications ?? 0) > 0)
                        <a href="{{ route('admin.verifications.index') }}" 
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200/90 text-rose-700 transition shadow-2xs group"
                           title="Pusat Verifikasi: {{ $totalPendingVerifications }} permohonan menunggu approval">
                            <div class="relative flex items-center justify-center">
                                <svg class="w-4 h-4 text-rose-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                                <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-rose-600"></span>
                            </div>
                            <div class="text-left hidden md:block">
                                <span class="text-[11px] font-black text-rose-900 block leading-tight">{{ $totalPendingVerifications }} Antrean Menunggu</span>
                                <span class="text-[9px] text-rose-600 font-medium block leading-none mt-0.5">
                                    @if(($pendingAsnCount ?? 0) > 0)<span class="font-bold">{{ $pendingAsnCount }}</span> Data ASN @endif
                                    @if(($pendingAsnCount ?? 0) > 0 && ($pendingExternalCount ?? 0) > 0)• @endif
                                    @if(($pendingExternalCount ?? 0) > 0)<span class="font-bold">{{ $pendingExternalCount }}</span> Diklat ({{ $pendingExternalJp ?? 0 }} JP)@endif
                                </span>
                            </div>
                            <span class="md:hidden px-1.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-rose-600 text-white">
                                {{ $totalPendingVerifications }}
                            </span>
                        </a>
                    @endif

                    <!-- Lonceng Notifikasi In-App (KAK Bab XXIII & Bab IX) -->
                    @include('partials.notification-bell')

                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        SISTEM NORMAL
                    </span>

                    <a href="{{ route('admin.courses.create') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden sm:inline">Tambah Kursus</span>
                    </a>
                </div>
            </header>

            <!-- Notification Messages -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full" x-data="{ show: true }" x-show="show">
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2">
                            <span>✅</span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 font-bold">&times;</button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full" x-data="{ show: true }" x-show="show">
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2">
                            <span>⚠️</span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-bold">&times;</button>
                    </div>
                </div>
            @endif

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                @yield('content')
            </main>

        </div>

    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
