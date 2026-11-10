@extends('layouts.app')

@section('content')
<!-- Hero Section (Standard v0.dev Modern Clean Canvas + Interactive Tabbed Showcase) -->
<section class="relative overflow-hidden bg-white border-b border-slate-200/80 pt-12 pb-16 lg:pt-16 lg:pb-20">
    <!-- Subtle Modern v0-Style Grid Pattern & Glow -->
    <div class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:24px_24px] opacity-60 pointer-events-none"></div>
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Top Pill Badge -->
        <div class="flex items-center justify-start mb-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50/80 text-brand-700 border border-brand-200/80 shadow-sm shadow-brand-500/5 hover:border-brand-300 transition-colors">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                <span>Portal Pengembangan Kompetensi ASN Kabupaten Berau</span>
                <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>

        <!-- Dual Pane Grid: Left Typography + Right Interactive Showcase Window -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center"
             x-data="{
                 activeTab: 0,
                 totalTabs: {{ $sliders->isNotEmpty() ? $sliders->count() : 3 }},
                 cycleTimer: null,
                 startCycle() {
                     this.cycleTimer = setInterval(() => {
                         this.activeTab = (this.activeTab + 1) % this.totalTabs;
                     }, 6000);
                 },
                 stopCycle() {
                     if (this.cycleTimer) clearInterval(this.cycleTimer);
                 }
             }"
             x-init="startCycle()"
             @mouseenter="stopCycle()"
             @mouseleave="startCycle()">
            
            <!-- Left Pane: Headline, Value Proposition, Action Buttons (6 Cols) -->
            <div class="lg:col-span-6 space-y-6">
                
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.12]">
                    Pengembangan Kompetensi <br/>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-700 via-teal-600 to-cyan-600">
                        ASN & Perangkat Daerah
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                    Pusat pembelajaran mandiri, webinar interaktif, dan bimbingan teknis bagi ASN, PPPK, serta seluruh perangkat daerah Pemerintah Kabupaten Berau guna pemenuhan standar kompetensi dan target jam pelajaran (JP).
                </p>

                <!-- Dual Action Buttons -->
                <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                    <a href="{{ route('courses.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <span>Mulai Belajar Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    
                    <a href="{{ route('calendar.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition-all duration-200 hover:border-slate-300">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Jadwal & Kalender Pembelajaran</span>
                    </a>
                </div>

                <!-- Social Proof & Accreditation Chips -->
                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <div class="flex -space-x-2 overflow-hidden">
                        <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-brand-600 text-white font-bold text-[9px] flex items-center justify-center">BP</span>
                        <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-teal-500 text-white font-bold text-[9px] flex items-center justify-center">DA</span>
                        <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-indigo-600 text-white font-bold text-[9px] flex items-center justify-center">RH</span>
                        @if($totalStudents > 10)
                            <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-slate-800 text-white font-bold text-[9px] flex items-center justify-center">+{{ $totalStudents > 999 ? round($totalStudents/1000) . 'k' : $totalStudents }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] text-slate-500 font-medium">{{ number_format($totalStudents) }} peserta terdaftar</p>
                    </div>
                </div>

            </div>

            <!-- Right Pane: Agenda Pelatihan, Workshop In-Class & Webinar (v0.dev Standard Precision Window) -->
            <div class="lg:col-span-6"
                 x-data="{
                     activeWebinarIndex: 0,
                     totalWebinars: {{ $upcomingWebinars->count() ?: 1 }},
                     cycleTimer: null,
                     startCycle() {
                         this.cycleTimer = setInterval(() => {
                             this.activeWebinarIndex = (this.activeWebinarIndex + 1) % this.totalWebinars;
                         }, 6000);
                     },
                     stopCycle() {
                         if (this.cycleTimer) clearInterval(this.cycleTimer);
                     },
                     next() {
                         this.activeWebinarIndex = (this.activeWebinarIndex + 1) % this.totalWebinars;
                     },
                     prev() {
                         this.activeWebinarIndex = (this.activeWebinarIndex - 1 + this.totalWebinars) % this.totalWebinars;
                     },
                     goTo(i) {
                         this.activeWebinarIndex = i;
                     }
                 }"
                 x-init="startCycle()"
                 @mouseenter="stopCycle()"
                 @mouseleave="startCycle()">
                
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-[0_20px_50px_rgba(15,23,42,0.08)] overflow-hidden transition-all duration-300">
                    
                    <!-- Window Topbar -->
                    <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-200/80 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-rose-400/80 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400/80 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400/80 inline-block"></span>
                            <span class="ml-2 text-[11px] font-mono font-medium text-slate-400 hidden sm:inline">lms.beraukab.go.id/webinar</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                                AGENDA & WORKSHOP
                            </span>
                            
                            <!-- Slide Counter & Clean Window Controls -->
                            <div class="flex items-center gap-1 bg-slate-200/60 p-0.5 rounded-lg border border-slate-300/40">
                                <span class="text-[10px] font-mono font-bold text-slate-600 px-1.5" x-text="(activeWebinarIndex + 1) + ' / ' + totalWebinars"></span>
                                <button @click="prev()" type="button" class="p-1 rounded-md hover:bg-white text-slate-600 hover:text-slate-900 transition shadow-none hover:shadow-sm" title="Agenda Sebelumnya">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button @click="next()" type="button" class="p-1 rounded-md hover:bg-white text-slate-600 hover:text-slate-900 transition shadow-none hover:shadow-sm" title="Agenda Berikutnya">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Active Agenda / Workshop / Webinar Body (No Scrollbar, No Speaker Tag) -->
                    <div class="p-5 sm:p-6 relative">
                        @if($upcomingWebinars->isNotEmpty())
                            @foreach($upcomingWebinars as $idx => $webinar)
                                @php
                                    $locLower = strtolower($webinar->location_type ?? '');
                                    $titleLower = strtolower($webinar->title ?? '');
                                    $isHybrid = str_contains($locLower, 'hybrid') || str_contains($titleLower, 'hybrid');
                                    $isWorkshopInClass = str_contains($locLower, 'in class') || str_contains($locLower, 'in-class') || str_contains($locLower, 'tatap muka') || str_contains($locLower, 'gedung') || str_contains($locLower, 'hotel') || str_contains($titleLower, 'workshop') || str_contains($titleLower, 'bimtek') || str_contains($locLower, 'workshop');
                                @endphp
                                <div x-show="activeWebinarIndex === {{ $idx }}"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="space-y-4">
                                    
                                    <!-- High-Impact Large Poster Image Banner -->
                                    <div class="h-64 sm:h-72 md:h-80 w-full rounded-2xl overflow-hidden relative bg-slate-900 border border-slate-200/90 shadow-sm group">
                                        @if($webinar->cover_image)
                                            <img src="{{ $webinar->cover_image }}" alt="{{ $webinar->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-slate-900 via-teal-950 to-indigo-950 flex items-center justify-center">
                                                <span class="text-4xl">🏛️</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-slate-950/20"></div>
                                        
                                        <!-- Top Overlaid Badges: Event Format & Price -->
                                        <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                @if($isHybrid)
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-500/95 text-white shadow-lg backdrop-blur-md border border-amber-300/40">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                                        HYBRID — IN-CLASS & ONLINE
                                                    </span>
                                                @elseif($isWorkshopInClass)
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-600/95 text-white shadow-lg backdrop-blur-md border border-indigo-400/40">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                        PELATIHAN IN-CLASS
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-teal-600/95 text-white shadow-lg backdrop-blur-md border border-teal-400/40">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                                        WEBINAR ONLINE
                                                    </span>
                                                @endif
                                            </div>

                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $webinar->price <= 0 ? 'bg-emerald-600/95 text-white border-emerald-400/30' : 'bg-slate-900/95 text-amber-300 border-amber-400/30' }} shadow-lg backdrop-blur-md border">
                                                {{ $webinar->price <= 0 ? 'Akses Terbuka' : 'Rp ' . number_format($webinar->price) }}
                                            </span>
                                        </div>

                                        <!-- Bottom Overlaid Glassmorphic Schedule & Location Banner -->
                                        <div class="absolute bottom-3 left-3 right-3 p-3 sm:p-3.5 rounded-xl bg-slate-950/75 backdrop-blur-md border border-white/20 flex items-center justify-between text-white shadow-xl">
                                            <div class="flex items-center gap-3">
                                                <div class="px-3 py-1.5 rounded-xl bg-white/10 border border-white/20 text-center flex flex-col justify-center min-w-[50px]">
                                                    <span class="text-[10px] font-black uppercase tracking-wider text-teal-300 leading-none">{{ $webinar->event_date->format('M') }}</span>
                                                    <span class="text-xl font-black text-white leading-tight mt-0.5">{{ $webinar->event_date->format('d') }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-xs sm:text-sm font-bold text-white flex items-center gap-1.5">
                                                        <span>{{ $webinar->event_date->format('l, d F Y') }}</span>
                                                    </p>
                                                    <p class="text-[11px] text-teal-300 font-mono mt-0.5 flex items-center gap-1.5 font-semibold">
                                                        <svg class="w-3 h-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span>{{ $webinar->time_start }} - {{ $webinar->time_end }} WIB</span>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="text-right hidden sm:block border-l border-white/15 pl-4 max-w-[210px]">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-200">
                                                    @if($isWorkshopInClass && !$isHybrid)
                                                        <span>📍 Tatap Muka Langsung</span>
                                                    @elseif($isHybrid)
                                                        <span>📍 In-Class & Online</span>
                                                    @else
                                                        <span>📡 Zoom & LMS Berau Live</span>
                                                    @endif
                                                </span>
                                                <p class="text-[10px] text-slate-300 truncate mt-0.5 font-medium">{{ $webinar->location_type }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Agenda Title & Details (Clean, Direct, Without Speaker Tag) -->
                                    <div class="space-y-1.5 pt-1">
                                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase">
                                                @if($isWorkshopInClass)
                                                    In-Class
                                                @elseif($isHybrid)
                                                    Hybrid
                                                @else
                                                    Online
                                                @endif
                                            </span>
                                            <span class="text-slate-300">•</span>
                                            <span class="truncate">{{ $webinar->location_type }}</span>
                                        </div>
                                        
                                        <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug line-clamp-2 hover:text-teal-600 transition">
                                            <a href="{{ route('webinars.show', $webinar->slug) }}">
                                                {{ $webinar->title }}
                                            </a>
                                        </h3>
                                    </div>

                                    <!-- Action Bar with Quota, Navigation Dots & Direct Registration CTA -->
                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-xs text-slate-600">
                                                Sisa Kuota: <strong class="text-slate-900 font-mono font-black">{{ number_format($webinar->quota) }}</strong> kursi
                                            </span>
                                        </div>

                                        <!-- Interactive Dots Indicator -->
                                        <div class="flex items-center gap-1.5">
                                            @foreach($upcomingWebinars as $dotIdx => $dotWb)
                                                <button @click="goTo({{ $dotIdx }})"
                                                        type="button"
                                                        class="h-2 rounded-full transition-all duration-300 focus:outline-none"
                                                        :class="activeWebinarIndex === {{ $dotIdx }} ? 'w-6 bg-teal-600' : 'w-2 bg-slate-200 hover:bg-slate-300'"
                                                        title="Buka agenda {{ $dotIdx + 1 }}"></button>
                                            @endforeach
                                        </div>
                                        
                                        <a href="{{ route('webinars.show', $webinar->slug) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 shadow-md shadow-teal-600/20 transition-all hover:translate-x-0.5">
                                            <span>Detail & Daftar Sesi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        @else
                            <!-- Fallback when no webinars -->
                            <div class="space-y-4 text-center py-16">
                                <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 text-2xl flex items-center justify-center mx-auto border border-teal-100">🗓️</div>
                                <h3 class="text-base font-bold text-slate-800">Belum ada agenda pelatihan baru</h3>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto">Jadwal workshop in-class dan webinar nasional berikutnya akan segera diumumkan.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Indicator Progress Line -->
                    <div class="h-1 w-full bg-slate-100">
                        <div class="h-full bg-brand-500 transition-all duration-300"
                             :style="'width: ' + (((activeWebinarIndex + 1) / totalWebinars) * 100) + '%'"></div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>

<!-- Stats Highlight Strip -->
<div class="bg-white border-b border-slate-200/80 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-3 text-center">
                <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalStudents) }}+</p>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Peserta Terdaftar</p>
            </div>
            <div class="p-3 text-center">
                <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $totalCourses }}</p>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kursus Tersedia</p>
            </div>
            <div class="p-3 text-center">
                <p class="text-2xl font-extrabold text-brand-600 tracking-tight">100%</p>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Sertifikat Digital Terverifikasi</p>
            </div>
            <div class="p-3 text-center">
                <p class="text-2xl font-extrabold text-emerald-600 tracking-tight">24/7</p>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Akses Modul Kapan Saja</p>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: PROGRAM PRIORITAS & INISIATIF UNGGULAN (Powered by Sliders) -->
@if($sliders->isNotEmpty())
<section class="py-14 bg-gradient-to-b from-slate-50/70 to-white border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                    <span class="text-xs font-bold text-brand-700 uppercase tracking-wider">Program & Inisiatif Unggulan</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Program & Inisiatif Pelatihan Unggulan</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Program pelatihan prioritas pilihan yang siap diakses untuk penguatan kompetensi ASN dan perangkat daerah.</p>
            </div>

            <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-700 hover:text-brand-800 transition">
                <span>Eksplorasi Kurikulum</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 {{ ($featuredGridLayout ?? '2x4') === '2x3' ? 'md:grid-cols-2 lg:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4' }} gap-6">
            @foreach($sliders as $slider)
                <a href="{{ Str::startsWith($slider->cta_link, 'http') ? $slider->cta_link : url($slider->cta_link) }}" 
                   class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.03)] hover:border-brand-400 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col justify-between cursor-pointer">
                    <div>
                        <div class="h-44 w-full bg-slate-100 relative overflow-hidden">
                            @if($slider->image_url)
                                <img src="{{ Str::startsWith($slider->image_url, 'http') ? $slider->image_url : asset(ltrim($slider->image_url, '/')) }}" 
                                     alt="{{ $slider->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-transparent"></div>
                            
                            @if($slider->badge_text)
                                <div class="absolute bottom-3 left-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-brand-600 text-white shadow-sm">
                                        {{ $slider->badge_text }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-5 space-y-2">
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base leading-snug line-clamp-2 group-hover:text-brand-600 transition">
                                {{ $slider->title }}
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                                {{ $slider->subtitle }}
                            </p>
                        </div>
                    </div>

                    <div class="p-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Terakreditasi
                        </span>
                        <div class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-brand-700 bg-brand-50 group-hover:bg-brand-600 group-hover:text-white transition">
                            <span>Lihat Materi</span>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

</section>
@endif

<!-- Categories Grid Section (Grid 2 x 4 / 8 Cards Kategori Utama) -->
<section class="py-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showAllCategories: false }">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 border border-teal-200/70 mb-1.5">
                <span>Rumpun Kompetensi Resmi</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kategori Bidang Pelatihan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Temukan kursus dan materi sesuai dengan rumpun kebutuhan aparatur Pemkab Berau.</p>
        </div>
        
        <!-- Toggle / Link Lihat Semua Kategori -->
        <button type="button" @click="showAllCategories = !showAllCategories" 
                class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-700 hover:text-teal-800 transition group focus:outline-none py-1.5 px-3 rounded-xl hover:bg-teal-50 border border-transparent hover:border-teal-200">
            <span x-text="showAllCategories ? 'Tampilkan 8 Kategori Saja' : 'Lihat Semua Kategori ({{ $categories->count() }})'"></span>
            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showAllCategories ? '-rotate-90' : 'group-hover:translate-x-0.5'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <!-- Grid 2 x 4 (8 Kategori Utama awal) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('courses.index', ['category' => $category->slug]) }}" 
               @if($loop->index >= 8)
                   x-show="showAllCategories"
                   x-cloak
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 transform scale-95"
                   x-transition:enter-end="opacity-100 transform scale-100"
               @endif
               class="group p-5 bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_2px_rgba(0,0,0,0.03)] hover:border-teal-500 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white transition-all duration-200">
                        @if($category->icon === 'activity')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        @elseif($category->icon === 'cpu')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        @elseif($category->icon === 'shield')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @elseif($category->icon === 'users')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        @elseif($category->icon === 'globe')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        @elseif($category->icon === 'briefcase')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @elseif($category->icon === 'building')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        @elseif($category->icon === 'map-pin')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @elseif($category->icon === 'award')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm group-hover:text-teal-700 transition-colors leading-snug">{{ $category->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                        {{ $category->description ?: 'Rumpun kompetensi terpadu untuk penguatan kapabilitas aparatur sipil negara.' }}
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-3 flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-600">
                        {{ $category->total_courses_count }} Kursus
                        @if($category->sub_categories_count > 0)
                            <span class="text-slate-400 font-normal">({{ $category->sub_categories_count }} Sub-Bidang)</span>
                        @endif
                    </span>
                    <span class="text-slate-400 group-hover:text-teal-600 group-hover:translate-x-1 transition-all font-semibold">&rarr;</span>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Bottom Actions / Expander -->
    @if($categories->count() > 8)
        <div class="mt-8 text-center" x-show="!showAllCategories">
            <button type="button" @click="showAllCategories = true"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 hover:border-teal-500 hover:text-teal-700 transition shadow-2xs group">
                <span>Lihat Semua {{ $categories->count() }} Kategori Utama</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>

        <div class="mt-8 text-center flex flex-col sm:flex-row items-center justify-center gap-3" x-show="showAllCategories" x-cloak>
            <button type="button" @click="showAllCategories = false"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                <span>Tampilkan 8 Kategori Saja</span>
            </button>
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-teal-50 border border-teal-200 text-xs font-bold text-teal-800 hover:bg-teal-100 transition shadow-2xs">
                <span>Jelajahi Seluruh Katalog Kursus</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    @endif
</section>

<!-- Featured Courses Section (Clean Cards v0.app Style) -->
<section class="py-14 bg-slate-50/60 border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-bold text-brand-700 uppercase tracking-wider">Kurikulum Terpopuler</span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">Program Pelatihan Rekomendasi</h2>
            </div>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Buka Seluruh Katalog &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredCourses as $course)
                <a href="{{ route('courses.show', $course->slug) }}" 
                   class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.03)] hover:border-brand-400 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col justify-between cursor-pointer">
                    <div>
                        <!-- Thumbnail -->
                        <div class="relative h-44 w-full overflow-hidden bg-slate-100">
                            @if($course->thumbnail)
                                <img src="{{ Str::startsWith($course->thumbnail, 'http') ? $course->thumbnail : asset(ltrim($course->thumbnail, '/')) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-brand-800 to-teal-700 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-white/90 backdrop-blur-md text-slate-800 shadow-sm">
                                    {{ $course->category->name }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5 space-y-2.5">
                            <h3 class="font-extrabold text-slate-900 text-base leading-snug line-clamp-2 group-hover:text-brand-600 transition">
                                {{ $course->title }}
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                {{ $course->subtitle }}
                            </p>

                            <div class="pt-2 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 overflow-hidden text-[10px] font-bold text-slate-700 flex items-center justify-center">
                                    {{ substr($course->instructor->name, 0, 2) }}
                                </div>
                                <span class="text-xs text-slate-600 truncate">{{ $course->instructor->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer & Enroll Button -->
                    <div class="px-5 pb-5 pt-0 flex items-center justify-between">
                        <div>
                            @if($course->price <= 0)
                                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200/80">Akses Terbuka ASN</span>
                            @else
                                <span class="text-base font-extrabold text-slate-900">{{ $course->formatted_price }}</span>
                            @endif
                        </div>
                        <div class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-brand-700 bg-brand-50 group-hover:bg-brand-600 group-hover:text-white border border-brand-200/80 transition-all">
                            <span>Lihat Materi</span>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- POPUP MODAL -->
@if($activePopup)
<div x-data="{
         showPopup: false,
         popupId: {{ $activePopup->id }},
         init() {
             const dismissed = localStorage.getItem('berau_lms_popup_dismissed_' + this.popupId);
             if (!dismissed) {
                 setTimeout(() => { this.showPopup = true; }, 1200);
             }
         },
         dismiss(permanent = false) {
             this.showPopup = false;
             if (permanent) {
                 localStorage.setItem('berau_lms_popup_dismissed_' + this.popupId, 'true');
             }
         }
     }"
     x-show="showPopup" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

    <div @click.away="dismiss()"
         x-show="showPopup"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden relative">

        <!-- Dismiss Button (X) -->
        <button @click="dismiss()" 
                class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-slate-900/60 hover:bg-slate-900 text-white flex items-center justify-center transition focus:outline-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        @if($activePopup->image_url)
            <div class="h-48 w-full bg-slate-100 relative overflow-hidden">
                <img src="{{ $activePopup->image_url }}" alt="{{ $activePopup->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                @if($activePopup->badge_text)
                    <div class="absolute bottom-3 left-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-teal-500 text-white shadow-sm">
                            {{ $activePopup->badge_text }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <div class="p-6 space-y-3 text-center sm:text-left">
            @if(!$activePopup->image_url && $activePopup->badge_text)
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-teal-50 text-teal-700 border border-teal-200">
                    {{ $activePopup->badge_text }}
                </span>
            @endif

            <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight leading-snug">
                {{ $activePopup->title }}
            </h3>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                {{ $activePopup->content }}
            </p>

            <div class="pt-3 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
                <button @click="dismiss(true)" type="button" class="text-[11px] text-slate-400 hover:text-slate-600 transition">
                    Jangan tampilkan lagi
                </button>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button @click="dismiss()" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Tutup
                    </button>
                    <a href="{{ $activePopup->button_link }}" 
                       class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 transition shadow-sm text-center">
                        {{ $activePopup->button_text }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endif

@endsection
