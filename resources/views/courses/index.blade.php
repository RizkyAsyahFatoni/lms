@extends('layouts.app')

@section('title', 'Katalog Seluruh Program & Modul Pelatihan | LMS KAB Berau')

@section('content')
{{-- ── HERO HEADER ───────────────────────────────────────────────────────── --}}
<div class="bg-white border-b border-slate-200/80 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-3">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Katalog Pelatihan</span>
        </nav>

        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 border border-teal-200/70 mb-2">
                <span>📚</span><span>Katalog Terpadu Seluruh Modul & Program Pelatihan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Katalog Pelatihan Terintegrasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-3xl leading-relaxed">
                Eksplorasi seluruh materi: E-Course Mandiri, Live Webinar, Pelatihan Tatap Muka (In-Class), dan Program Hybrid untuk pemenuhan kompetensi ASN.
            </p>
        </div>

    </div>
</div>

{{-- ── MAIN CONTENT WITH LEFT SIDEBAR ───────────────────────────────────── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        
        {{-- ── SIDEBAR KIRI (FILTER, PENCARIAN, KATEGORI, FORMAT & TIPE AKSES) ── --}}
        <aside class="lg:col-span-1 space-y-6 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs sticky top-24">
            
            <!-- 1. Search Box -->
            <div>
                <form action="{{ route('courses.index') }}" method="GET" class="relative">
                    @if(request('format')) <input type="hidden" name="format" value="{{ request('format') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari materi atau topik..." 
                           class="w-full pl-9 pr-8 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(request('q'))
                        <a href="{{ route('courses.index', request()->except('q')) }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
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
                    <a href="{{ route('courses.index', array_merge(request()->except(['category', 'page']))) }}"
                       class="flex items-center justify-between p-2 rounded-xl transition group {{ empty($categorySlug) ? 'bg-brand-50 text-brand-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] {{ empty($categorySlug) ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 group-hover:border-brand-400' }}">
                                @if(empty($categorySlug)) ✓ @endif
                            </span>
                            <span>Semua Kategori</span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ empty($categorySlug) ? 'bg-brand-200/70 text-brand-900' : 'bg-slate-100 text-slate-500' }}">
                            {{ $totalAllCount }}
                        </span>
                    </a>

                    {{-- Dynamic Categories --}}
                    @foreach($categories as $cat)
                        @php
                            $isSelected = ($categorySlug ?? '') === $cat->slug;
                            $count = $categoryCounts[$cat->slug] ?? 0;
                        @endphp
                        <a href="{{ route('courses.index', array_merge(request()->query(), ['category' => $cat->slug, 'page' => 1])) }}"
                           class="flex items-center justify-between p-2 rounded-xl transition group {{ $isSelected ? 'bg-brand-50 text-brand-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] shrink-0 {{ $isSelected ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 group-hover:border-brand-400' }}">
                                    @if($isSelected) ✓ @endif
                                </span>
                                <span class="truncate">{{ $cat->name }}</span>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full shrink-0 {{ $isSelected ? 'bg-brand-200/70 text-brand-900' : 'bg-slate-100 text-slate-500' }}">
                                {{ $count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 3. Format Pelatihan -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight">Format Pelatihan</h3>
                <div class="space-y-1.5 text-xs">
                    <a href="{{ route('courses.index', array_merge(request()->except(['format', 'page']))) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ empty($format) ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>📚 Semua Format</span>
                        <span class="text-[11px] {{ empty($format) ? 'text-slate-300' : 'text-slate-400' }}">{{ $totalAllCount }}</span>
                    </a>

                    <a href="{{ route('courses.index', array_merge(request()->query(), ['format' => 'ecourse', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($format ?? '') === 'ecourse' ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>💻 E-Course Mandiri</span>
                        <span class="text-[11px] {{ ($format ?? '') === 'ecourse' ? 'text-teal-200' : 'text-slate-400' }}">{{ $formatCounts['ecourse'] ?? 0 }}</span>
                    </a>

                    <a href="{{ route('courses.index', array_merge(request()->query(), ['format' => 'webinar', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($format ?? '') === 'webinar' ? 'bg-indigo-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>📹 Live Webinar</span>
                        <span class="text-[11px] {{ ($format ?? '') === 'webinar' ? 'text-indigo-200' : 'text-slate-400' }}">{{ $formatCounts['webinar'] ?? 0 }}</span>
                    </a>

                    <a href="{{ route('courses.index', array_merge(request()->query(), ['format' => 'in_class', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($format ?? '') === 'in_class' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>🏛️ Tatap Muka (In-Class)</span>
                        <span class="text-[11px] {{ ($format ?? '') === 'in_class' ? 'text-amber-200' : 'text-slate-400' }}">{{ $formatCounts['in_class'] ?? 0 }}</span>
                    </a>

                    <a href="{{ route('courses.index', array_merge(request()->query(), ['format' => 'hybrid', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($format ?? '') === 'hybrid' ? 'bg-purple-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>🔄 Pelatihan Hybrid</span>
                        <span class="text-[11px] {{ ($format ?? '') === 'hybrid' ? 'text-purple-200' : 'text-slate-400' }}">{{ $formatCounts['hybrid'] ?? 0 }}</span>
                    </a>
                </div>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 4. Filter Jenis Akses -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight">Tipe Akses</h3>
                <div class="space-y-1.5 text-xs">
                    <a href="{{ route('courses.index', array_merge(request()->query(), ['type' => ($type === 'free' ? null : 'free'), 'page' => 1])) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl transition {{ ($type ?? '') === 'free' ? 'bg-teal-50 text-teal-900 font-bold border border-teal-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] {{ ($type ?? '') === 'free' ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-300' }}">
                                @if(($type ?? '') === 'free') ✓ @endif
                            </span>
                            <span>Gratis</span>
                        </div>
                    </a>

                    <a href="{{ route('courses.index', array_merge(request()->query(), ['type' => ($type === 'paid' ? null : 'paid'), 'page' => 1])) }}"
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
            @if(request('q') || request('category') || request('format') || request('type'))
                <div class="pt-2">
                    <a href="{{ route('courses.index') }}" 
                       class="w-full py-2 px-3 text-center block rounded-xl border border-slate-200 text-xs font-bold text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                        ✕ Reset Semua Filter
                    </a>
                </div>
            @endif

        </aside>

        {{-- ── KONTEN UTAMA KANAN (GRID KATALOG PELATIHAN) ─────────────────────── --}}
        <main class="lg:col-span-3 space-y-6">
            
            <!-- Bar Atas: Status Hasil & Active Filter Tags -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                
                {{-- Info Status Filter Aktif --}}
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="font-extrabold text-slate-900">
                        {{ $catalogItems->total() }} Pelatihan Ditemukan
                    </span>

                    @if(!empty($categorySlug))
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-50 text-brand-800 border border-brand-200 flex items-center gap-1">
                            <span>Kategori: {{ $categories->firstWhere('slug', $categorySlug)?->name ?? $categorySlug }}</span>
                            <a href="{{ route('courses.index', request()->except('category')) }}" class="hover:text-red-500 font-black">✕</a>
                        </span>
                    @endif

                    @if(!empty($format))
                        @php
                            $formatName = match($format) {
                                'ecourse' => 'E-Course',
                                'webinar' => 'Live Webinar',
                                'in_class' => 'In-Class',
                                'hybrid' => 'Hybrid',
                                default => $format,
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200 flex items-center gap-1">
                            <span>Format: {{ $formatName }}</span>
                            <a href="{{ route('courses.index', request()->except('format')) }}" class="hover:text-red-500 font-black">✕</a>
                        </span>
                    @endif

                    @if(!empty($search))
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1">
                            <span>"{{ $search }}"</span>
                            <a href="{{ route('courses.index', request()->except('q')) }}" class="hover:text-red-500 font-black">✕</a>
                        </span>
                    @endif

                    @if(!empty($type))
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 border border-teal-200 flex items-center gap-1">
                            <span>{{ $type === 'free' ? 'Gratis' : 'Berbayar' }}</span>
                            <a href="{{ route('courses.index', request()->except('type')) }}" class="hover:text-red-500 font-black">✕</a>
                        </span>
                    @endif
                </div>

                @if(request('q') || request('category') || request('format') || request('type'))
                    <a href="{{ route('courses.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition shrink-0">
                        Reset Filter
                    </a>
                @endif
            </div>

            {{-- Grid Kartu Pelatihan --}}
            @if($catalogItems->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($catalogItems as $item)
                        @php
                            $badgeBg = match($item->format) {
                                'webinar'  => 'bg-indigo-600 text-white',
                                'in_class' => 'bg-amber-600 text-white',
                                'hybrid'   => 'bg-purple-600 text-white',
                                default    => 'bg-teal-700 text-white',
                            };
                        @endphp
                        <a href="{{ $item->url }}" class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between group cursor-pointer">
                            <div>
                                {{-- Thumbnail with aspect-video --}}
                                <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                                    <img src="{{ Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset(ltrim($item->thumbnail, '/')) }}" alt="{{ $item->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                                    
                                    {{-- Format & Category Badges --}}
                                    <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider {{ $badgeBg }} shadow-xs">
                                            {{ $item->format_label }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/95 text-slate-800 shadow-xs backdrop-blur-xs">
                                            {{ $item->category_name }}
                                        </span>
                                    </div>

                                    {{-- JP ASN Badge --}}
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-400 text-slate-900 shadow-xs">
                                            ⚡ {{ $item->jp }} JP
                                        </span>
                                    </div>

                                    {{-- Bottom Meta Overlay --}}
                                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white text-xs font-medium">
                                        <span class="flex items-center gap-1 text-yellow-400 font-bold">
                                            <span>★</span>
                                            <span>{{ number_format($item->rating, 1) }}</span>
                                        </span>
                                        <span class="text-slate-200 text-[11px] truncate max-w-[200px]">
                                            👤 {{ $item->instructor_name }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Card Body --}}
                                <div class="p-5 space-y-2.5">
                                    <h3 class="font-bold text-slate-900 text-base leading-snug line-clamp-2 group-hover:text-brand-700 transition-colors">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $item->subtitle }}
                                    </p>
                                </div>
                            </div>

                            {{-- Card Footer --}}
                            <div class="px-5 pb-5 pt-0 flex items-center justify-between border-t border-slate-100 mt-2 pt-3">
                                <div>
                                    @if($item->price <= 0)
                                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200/80">
                                            Akses Terbuka
                                        </span>
                                    @else
                                        <span class="text-sm font-black text-slate-900">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-brand-700 bg-brand-50 group-hover:bg-brand-600 group-hover:text-white border border-brand-200/80 transition-all shadow-2xs">
                                    <span>Lihat Materi</span>
                                    <span>→</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $catalogItems->links() }}
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                        🔍
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Tidak ada materi yang cocok</h3>
                    <p class="text-xs text-slate-500">Coba gunakan kata kunci pencarian lain atau sesuaikan filter di sidebar kiri.</p>
                    <a href="{{ route('courses.index') }}" class="inline-block mt-2 px-4 py-2 rounded-xl text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 transition">
                        Reset Semua Filter
                    </a>
                </div>
            @endif

        </main>

    </div>
</div>
@endsection
