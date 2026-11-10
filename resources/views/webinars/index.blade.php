@extends('layouts.app')

@section('title', 'Jadwal Live Webinar & Kalender Pembelajaran | LMS KAB Berau')

@section('content')
{{-- ── HERO SECTION: Studio Live Webinar Transparan Realistis ───────────────────── --}}
<div class="relative bg-slate-950 text-white py-12 sm:py-16 border-b border-teal-900/60 overflow-hidden">
    
    {{-- Foto Live Video Conference / Zoom Meeting dengan Efek Transparan (10%) --}}
    <div class="absolute inset-0 bg-cover bg-center mix-blend-luminosity pointer-events-none"
         style="background-image: url('https://images.unsplash.com/photo-1588196749597-9ff075ee6b5b?auto=format&fit=crop&w=1600&q=80'); opacity: 0.10;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/95 to-slate-950/85 pointer-events-none"></div>
    <div class="absolute inset-0 pointer-events-none"
         style="background-image: radial-gradient(#14b8a6 1px, transparent 1px); background-size: 24px 24px; opacity: 0.06;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-teal-300/80 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white font-semibold">Webinar & Agenda Pelatihan</span>
        </nav>

        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-400/30 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Modul Live Webinar & Kalender Pembelajaran Daring</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white">
                Jadwal Live Webinar Pembelajaran
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm lg:text-base leading-relaxed">
                Ikuti sesi interaktif live streaming dan seminar daring bersama narasumber terakreditasi nasional untuk memenuhi standar pemenuhan Jam Pelajaran (JP) ASN.
            </p>
        </div>
    </div>
</div>

{{-- ── MAIN CONTENT WITH LEFT SIDEBAR (Filter, Pencarian, Kategori & Jadwal) ───── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="{
        viewMode: 'grid', {{-- 'grid' or 'calendar' --}}
        calendarEvents: @js($calendarEvents),
        selectedMonth: new Date().toISOString().slice(0, 7),
        get filteredEvents() {
            return this.calendarEvents.filter(e => e.date.startsWith(this.selectedMonth));
        }
     }">
    
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        
        {{-- ── SIDEBAR KIRI (FILTER, PENCARIAN, KATEGORI & JADWAL) ──────────────── --}}
        <aside class="lg:col-span-1 space-y-6 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs sticky top-24">
            
            <!-- 1. Search Box -->
            <div>
                <form action="{{ route('webinars.index') }}" method="GET" class="relative">
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari topik atau narasumber..."
                           class="w-full pl-9 pr-8 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(request('q'))
                        <a href="{{ route('webinars.index', request()->except('q')) }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                    @endif
                </form>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 2. Kategori / Bidang List with Checkbox & Count Badge -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center justify-between">
                    <span>Kategori / Bidang</span>
                </h3>

                <div class="space-y-1.5 text-xs">
                    {{-- Option Semua Kategori --}}
                    <a href="{{ route('webinars.index', array_merge(request()->except(['category', 'page']))) }}"
                       class="flex items-center justify-between p-2 rounded-xl transition group {{ empty($categorySlug) ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] {{ empty($categorySlug) ? 'bg-teal-600 border-teal-600 text-white' : 'border-slate-300 group-hover:border-teal-400' }}">
                                @if(empty($categorySlug)) ✓ @endif
                            </span>
                            <span>Semua Kategori</span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ empty($categorySlug) ? 'bg-teal-200/70 text-teal-900' : 'bg-slate-100 text-slate-500' }}">
                            {{ $totalAllCount }}
                        </span>
                    </a>

                    {{-- Dynamic Categories --}}
                    @foreach($categories as $cat)
                        @php
                            $isSelected = ($categorySlug ?? '') === $cat->slug;
                            $count = $categoryCounts[$cat->slug] ?? 0;
                        @endphp
                        <a href="{{ route('webinars.index', array_merge(request()->query(), ['category' => $cat->slug, 'page' => 1])) }}"
                           class="flex items-center justify-between p-2 rounded-xl transition group {{ $isSelected ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] shrink-0 {{ $isSelected ? 'bg-teal-600 border-teal-600 text-white' : 'border-slate-300 group-hover:border-teal-400' }}">
                                    @if($isSelected) ✓ @endif
                                </span>
                                <span class="truncate">{{ $cat->name }}</span>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full shrink-0 {{ $isSelected ? 'bg-teal-200/70 text-teal-900' : 'bg-slate-100 text-slate-500' }}">
                                {{ $count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 3. Jadwal & Status Sesi -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight">Jadwal & Status</h3>
                <div class="space-y-1.5 text-xs">
                    <a href="{{ route('webinars.index', array_merge(request()->query(), ['status' => 'upcoming', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($status ?? 'upcoming') === 'upcoming' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="flex items-center gap-2">
                            <span>⏰</span>
                            <span>Akan Datang & Sedang Live</span>
                        </span>
                        <span class="text-[10px] {{ ($status ?? 'upcoming') === 'upcoming' ? 'text-emerald-400' : 'text-slate-400' }}">●</span>
                    </a>
                    <a href="{{ route('webinars.index', array_merge(request()->query(), ['status' => 'completed', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($status ?? '') === 'completed' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="flex items-center gap-2">
                            <span>📁</span>
                            <span>Riwayat Webinar Selesai</span>
                        </span>
                        <span class="text-[10px] {{ ($status ?? '') === 'completed' ? 'text-teal-400' : 'text-slate-400' }}">●</span>
                    </a>
                </div>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 4. Filter Jenis Akses -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight">Tipe Akses</h3>
                <div class="space-y-1.5 text-xs">
                    <a href="{{ route('webinars.index', array_merge(request()->query(), ['type' => ($type === 'free' ? null : 'free'), 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($type ?? '') === 'free' ? 'bg-teal-50 text-teal-900 font-bold border border-teal-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] {{ ($type ?? '') === 'free' ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-300' }}">
                                @if(($type ?? '') === 'free') ✓ @endif
                            </span>
                            <span>Gratis</span>
                        </div>
                    </a>

                    <a href="{{ route('webinars.index', array_merge(request()->query(), ['type' => ($type === 'paid' ? null : 'paid'), 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($type ?? '') === 'paid' ? 'bg-teal-50 text-teal-900 font-bold border border-teal-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] {{ ($type ?? '') === 'paid' ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-300' }}">
                                @if(($type ?? '') === 'paid') ✓ @endif
                            </span>
                            <span>Berbayar</span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Reset Filter Button --}}
            @if(request('q') || request('category') || request('type') || request('status'))
                <div class="pt-2">
                    <a href="{{ route('webinars.index') }}" 
                       class="w-full py-2 px-3 text-center block rounded-xl border border-slate-200 text-xs font-bold text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                        ✕ Reset Semua Filter
                    </a>
                </div>
            @endif

        </aside>

        {{-- ── KONTEN UTAMA KANAN (TAMPILAN KATALOG & KALENDER) ─────────────────── --}}
        <main class="lg:col-span-3 space-y-6">
            
            <!-- Bar Atas: Status Hasil & View Switcher -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                
                {{-- Info Status Filter Aktif --}}
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="font-extrabold text-slate-900">
                        {{ $webinars->total() }} Agenda Ditemukan
                    </span>

                    @if(!empty($categorySlug))
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 border border-teal-200 flex items-center gap-1">
                            <span>Kategori: {{ $categories->firstWhere('slug', $categorySlug)?->name ?? $categorySlug }}</span>
                            <a href="{{ route('webinars.index', request()->except('category')) }}" class="hover:text-red-500 font-black">✕</a>
                        </span>
                    @endif

                    @if(!empty($search))
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1">
                            <span>"{{ $search }}"</span>
                            <a href="{{ route('webinars.index', request()->except('q')) }}" class="hover:text-red-500 font-black">✕</a>
                        </span>
                    @endif

                    @if(!empty($type))
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                            <span>{{ $type === 'free' ? 'Gratis' : 'Berbayar' }}</span>
                            <a href="{{ route('webinars.index', request()->except('type')) }}" class="hover:text-red-500 font-black">✕</a>
                        </span>
                    @endif
                </div>

                {{-- View Switcher (Grid vs Kalender) --}}
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl self-start sm:self-auto shrink-0">
                    <button @click="viewMode = 'grid'" 
                            :class="viewMode === 'grid' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-900 font-medium'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Kategori</span>
                    </button>
                    <button @click="viewMode = 'calendar'" 
                            :class="viewMode === 'calendar' ? 'bg-white text-teal-800 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-900 font-medium'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Kalender Pembelajaran</span>
                    </button>
                </div>
            </div>

            {{-- ── TAB 1: GRID VIEW KATALOG ────────────────────────────────────── --}}
            <div x-show="viewMode === 'grid'" class="space-y-6">
                @if($webinars->isEmpty())
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8">
                        <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 text-3xl flex items-center justify-center mx-auto border border-teal-100 mb-4">🗓️</div>
                        <h3 class="text-base font-bold text-slate-800">Tidak ada jadwal agenda ditemukan</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Coba sesuaikan filter kategori, status, atau kata kunci pencarian Anda untuk melihat agenda webinar lainnya.</p>
                        <a href="{{ route('webinars.index') }}" class="inline-flex items-center gap-1 mt-4 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-teal-600 transition shadow-xs">
                            Reset Filter
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($webinars as $wb)
                            @php
                                $delivery = $wb->delivery_mode ?? ($wb->location_type && str_contains(strtolower($wb->location_type), 'zoom') ? 'online' : 'in_class');
                                $isOnline = $delivery === 'online';
                                
                                // Deteksi nama kategori
                                $categoryName = $wb->course?->category?->name;
                                if (!$categoryName) {
                                    $titleLower = strtolower($wb->title);
                                    if (str_contains($titleLower, 'kesehatan') || str_contains($titleLower, 'faskes') || str_contains($titleLower, 'puskesmas') || str_contains($titleLower, 'rsud') || str_contains($titleLower, 'fhir') || str_contains($titleLower, 'rme')) {
                                        $categoryName = 'Kesehatan & Pelayanan Publik';
                                    } elseif (str_contains($titleLower, 'pdp') || str_contains($titleLower, 'data pribadi') || str_contains($titleLower, 'siber')) {
                                        $categoryName = 'Keamanan Siber & PDP';
                                    } elseif (str_contains($titleLower, 'sipd') || str_contains($titleLower, 'keuangan') || str_contains($titleLower, 'asn')) {
                                        $categoryName = 'Pengembangan ASN';
                                    } else {
                                        $categoryName = 'Teknologi Terapan';
                                    }
                                }
                            @endphp

                            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                                
                                <div>
                                    <!-- Card Media Header (Aspect Video) -->
                                    <div class="relative aspect-video bg-slate-900 overflow-hidden w-full">
                                        @if($wb->cover_image)
                                            <img src="{{ $wb->cover_image }}" alt="{{ $wb->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-tr from-slate-950 via-teal-950 to-slate-900 flex items-center justify-center">
                                                <span class="text-4xl">📹</span>
                                            </div>
                                        @endif

                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>

                                        <!-- Top Badges -->
                                        <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-1.5 flex-wrap">
                                            <div class="flex items-center gap-1.5">
                                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-indigo-600 text-white shadow-xs">
                                                    📹 Live Webinar
                                                </span>
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/95 text-slate-800 shadow-xs backdrop-blur-xs">
                                                    {{ $categoryName }}
                                                </span>
                                            </div>

                                            @if($wb->jp > 0)
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-400 text-slate-900 shadow-xs">
                                                    ⚡ {{ $wb->jp }} JP
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Status Badge & Date Overlay di Bottom Header -->
                                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white">
                                            <div class="flex items-center gap-2">
                                                <div class="px-2 py-1 rounded-lg bg-black/50 backdrop-blur-md border border-white/20 text-center flex flex-col min-w-[40px]">
                                                    <span class="text-[9px] font-extrabold uppercase text-teal-300 leading-none">{{ $wb->event_date->format('M') }}</span>
                                                    <span class="text-sm font-black leading-tight mt-0.5">{{ $wb->event_date->format('d') }}</span>
                                                </div>
                                                <div class="text-[11px] leading-tight">
                                                    <p class="font-bold text-white">{{ $wb->event_date->translatedFormat('d M Y') }}</p>
                                                    <p class="text-teal-200 font-mono text-[10px]">⏰ {{ $wb->time_start }} - {{ $wb->time_end }} WIB</p>
                                                </div>
                                            </div>

                                            @if($wb->status === 'ongoing')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500 text-white animate-pulse shadow-xs">
                                                    ● Live
                                                </span>
                                            @elseif($wb->status === 'completed')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800/90 text-slate-300 backdrop-blur-md">
                                                    ✓ Selesai
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/90 text-slate-900 backdrop-blur-md">
                                                    Akan Datang
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="p-5 space-y-3">
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                            <span>🌐</span>
                                            <span class="truncate font-medium">{{ $wb->location_type ?: 'Online Live via Zoom' }}</span>
                                        </div>

                                        <h3 class="text-sm font-black text-slate-900 tracking-tight leading-snug line-clamp-2 group-hover:text-teal-600 transition">
                                            <a href="{{ route('webinars.show', $wb->slug) }}">
                                                {{ $wb->title }}
                                            </a>
                                        </h3>

                                        @if($wb->description)
                                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $wb->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-5 pt-0 space-y-4">
                                    <!-- Speaker Info -->
                                    <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                                        @if($wb->speaker_avatar)
                                            <img src="{{ $wb->speaker_avatar }}" alt="{{ $wb->speaker_name }}"
                                                 class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 font-bold text-xs flex items-center justify-center border border-teal-200 shrink-0">
                                                {{ substr($wb->speaker_name, 0, 2) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate">{{ $wb->speaker_name }}</p>
                                            <p class="text-[10px] text-slate-400 truncate">{{ $wb->speaker_institution ?: ($wb->speaker_title ?: 'Narasumber Ahli') }}</p>
                                        </div>
                                    </div>

                                    <!-- Quota & CTA Footer -->
                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] text-slate-400 block font-medium">Sisa Kuota:</span>
                                            <span class="text-xs font-mono font-black {{ $wb->remaining_quota < 10 ? 'text-rose-600' : 'text-slate-800' }}">
                                                {{ $wb->remaining_quota }} Kursi
                                            </span>
                                        </div>

                                        <a href="{{ route('webinars.show', $wb->slug) }}"
                                           class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-teal-600 transition shadow-xs flex items-center gap-1.5 group-hover:bg-teal-600">
                                            <span>Detail Sesi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div class="pt-6">
                        {{ $webinars->links() }}
                    </div>
                @endif
            </div>

            {{-- ── TAB 2: KALENDER PEMBELAJARAN INTERAKTIF ─────────────────────── --}}
            <div x-show="viewMode === 'calendar'" class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <span>🗓️ Kalender Pembelajaran & Pelatihan</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Jadwal agenda live webinar daring dan kelas pelatihan terintegrasi kalender bulanan.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold text-slate-600">Pilih Bulan:</label>
                            <input type="month" x-model="selectedMonth"
                                   class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <!-- List of events for the selected month -->
                    <div class="mt-6 space-y-3">
                        <template x-if="filteredEvents.length === 0">
                            <div class="text-center py-14 text-slate-400 text-xs">
                                <span class="text-3xl block mb-2">📅</span>
                                Tidak ada agenda webinar terjadwal pada bulan ini.
                            </div>
                        </template>

                        <template x-for="event in filteredEvents" :key="event.id">
                            <div class="p-4 rounded-2xl border border-slate-200/80 hover:border-teal-300 hover:bg-teal-50/30 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="px-3 py-2 rounded-xl bg-slate-900 text-white text-center flex flex-col justify-center min-w-[56px] shrink-0">
                                        <span class="text-[10px] font-black uppercase tracking-wider text-teal-300" x-text="new Date(event.date).toLocaleString('id-ID', { month: 'short' })"></span>
                                        <span class="text-lg font-black" x-text="new Date(event.date).getDate()"></span>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase"
                                                  :class="event.delivery_mode === 'online' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-800 border border-amber-200'"
                                                  x-text="event.delivery_mode === 'online' ? 'Live Webinar Daring' : 'In-Class Workshop'"></span>
                                            <span class="text-[11px] text-slate-500 font-mono" x-text="'⏰ ' + event.time + ' WIB'"></span>
                                        </div>
                                        <h4 class="font-extrabold text-slate-900 text-sm hover:text-teal-600 transition" x-text="event.title"></h4>
                                        <p class="text-xs text-slate-500 flex items-center gap-1">
                                            <span>📍</span>
                                            <span x-text="event.location"></span>
                                        </p>
                                    </div>
                                </div>

                                <a :href="event.url" 
                                   class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-teal-600 transition shadow-xs whitespace-nowrap self-end sm:self-center">
                                    Buka Detail Agenda →
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </main>

    </div>
</div>
@endsection
