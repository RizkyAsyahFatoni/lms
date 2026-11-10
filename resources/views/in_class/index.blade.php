@extends('layouts.app')

@section('title', 'Pelatihan Tatap Muka & Klasikal (In-Class) | LMS KAB Berau')

@section('content')
{{-- ── HERO SECTION: Ruang Kelas Klasikal Transparan ───────────────────── --}}
<div class="relative bg-slate-950 text-white py-12 sm:py-16 border-b border-slate-800/80 overflow-hidden">
    
    {{-- Foto Ruang Kelas / Training Room dengan Efek Transparan Realistis (Poin 1) --}}
    <div class="absolute inset-0 bg-cover bg-center opacity-40 mix-blend-luminosity filter blur-[0.4px] transition-opacity duration-300"
         style="background-image: url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1600&q=80')"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/80 to-slate-950/50"></div>
    <div class="absolute inset-0 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:24px_24px] opacity-10 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-amber-300/80 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white font-semibold">Pelatihan Klasikal (In-Class)</span>
        </nav>

        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Program Pembelajaran Tatap Muka & Workshop Klasikal</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white">
                Pelatihan Tatap Muka (In-Class)
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm lg:text-base leading-relaxed">
                Bimbingan teknis intensif, lokakarya studi kasus, dan seminar tatap muka di ruang pelatihan terakreditasi untuk akselerasi kompetensi ASN Pemerintah Kabupaten Berau.
            </p>
        </div>
    </div>
</div>

{{-- ── MAIN CONTENT WITH LEFT SIDEBAR (Poin 2: Sesuai Referensi Gambar) ───── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        
        {{-- ── SIDEBAR KIRI (FILTER CATEGORIES, SEARCH, STATUS) ──────────────── --}}
        <aside class="lg:col-span-1 space-y-6 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            
            <!-- 1. Search Box -->
            <div>
                <form action="{{ route('in-class.index') }}" method="GET" class="relative">
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('level')) <input type="hidden" name="level" value="{{ request('level') }}"> @endif
                    
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Pelatihan..."
                           class="w-full pl-9 pr-8 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(request('q'))
                        <a href="{{ route('in-class.index', request()->except('q')) }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                    @endif
                </form>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 2. Categories List with Checkbox & Count Badge -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center justify-between">
                    <span>Kategori / Bidang</span>
                </h3>

                <div class="space-y-1.5 text-xs">
                    {{-- All Categories Option --}}
                    <a href="{{ route('in-class.index', array_merge(request()->except(['category', 'page']))) }}"
                       class="flex items-center justify-between p-2 rounded-xl transition group {{ empty($categorySlug) ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] {{ empty($categorySlug) ? 'bg-amber-600 border-amber-600 text-white' : 'border-slate-300 group-hover:border-amber-400' }}">
                                @if(empty($categorySlug)) ✓ @endif
                            </span>
                            <span>Semua Kategori</span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ empty($categorySlug) ? 'bg-amber-200/70 text-amber-900' : 'bg-slate-100 text-slate-500' }}">
                            {{ $totalAllCount }}
                        </span>
                    </a>

                    {{-- Dynamic Categories --}}
                    @foreach($categories as $cat)
                        @php
                            $isSelected = ($categorySlug ?? '') === $cat->slug;
                            $count = $cat->courses_count;
                            if (str_contains($cat->slug, 'kesehatan')) {
                                $count += \App\Models\Webinar::whereIn('delivery_mode', ['in_class', 'workshop', 'bimtek', 'seminar'])->where(function($q){
                                    $q->where('title', 'like', '%faskes%')->orWhere('title', 'like', '%kesehatan%')->orWhere('title', 'like', '%puskesmas%');
                                })->count();
                            } elseif (str_contains($cat->slug, 'asn') || str_contains($cat->slug, 'pemimpin')) {
                                $count += \App\Models\Webinar::whereIn('delivery_mode', ['in_class', 'workshop', 'bimtek', 'seminar'])->where(function($q){
                                    $q->where('title', 'like', '%keuangan%')->orWhere('title', 'like', '%sipd%');
                                })->count();
                            }
                        @endphp
                        <a href="{{ route('in-class.index', array_merge(request()->query(), ['category' => $cat->slug, 'page' => 1])) }}"
                           class="flex items-center justify-between p-2 rounded-xl transition group {{ $isSelected ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] shrink-0 {{ $isSelected ? 'bg-amber-600 border-amber-600 text-white' : 'border-slate-300 group-hover:border-amber-400' }}">
                                    @if($isSelected) ✓ @endif
                                </span>
                                <span class="truncate">{{ $cat->name }}</span>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full shrink-0 {{ $isSelected ? 'bg-amber-200/70 text-amber-900' : 'bg-slate-100 text-slate-500' }}">
                                {{ $count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 3. Status Sesi (Jadwal) -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight">Status Pelatihan</h3>
                <div class="space-y-1.5 text-xs">
                    <a href="{{ route('in-class.index', array_merge(request()->query(), ['status' => 'upcoming', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2 rounded-xl transition {{ ($status ?? 'upcoming') === 'upcoming' ? 'bg-slate-900 text-white font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>🏛️ Jadwal Mendatang & Aktif</span>
                        <span class="text-[10px]">●</span>
                    </a>
                    <a href="{{ route('in-class.index', array_merge(request()->query(), ['status' => 'completed', 'page' => 1])) }}"
                       class="flex items-center justify-between p-2 rounded-xl transition {{ ($status ?? '') === 'completed' ? 'bg-slate-900 text-white font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>📁 Riwayat Selesai</span>
                        <span class="text-[10px]">●</span>
                    </a>
                </div>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- 4. Tingkat Kompetensi -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight">Tingkat Pelatihan</h3>
                <div class="space-y-1 text-xs">
                    @foreach(['' => 'Semua Tingkat', 'Dasar' => 'Tingkat Dasar', 'Menengah' => 'Tingkat Menengah', 'Lanjutan' => 'Tingkat Lanjutan'] as $lvlKey => $lvlLabel)
                        @php $isLvl = ($level ?? '') === $lvlKey; @endphp
                        <a href="{{ route('in-class.index', array_merge(request()->query(), ['level' => $lvlKey, 'page' => 1])) }}"
                           class="flex items-center gap-2 p-1.5 rounded-lg transition {{ $isLvl ? 'text-amber-800 font-bold bg-amber-50/60' : 'text-slate-500 hover:text-slate-900' }}">
                            <span class="w-3 h-3 rounded-full border flex items-center justify-center {{ $isLvl ? 'border-amber-600 bg-amber-600' : 'border-slate-300' }}">
                                @if($isLvl)<span class="w-1.5 h-1.5 rounded-full bg-white"></span>@endif
                            </span>
                            <span>{{ $lvlLabel }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Reset Filter Button -->
            @if(request('category') || request('q') || request('level') || request('status') === 'completed')
                <div class="pt-2">
                    <a href="{{ route('in-class.index') }}"
                       class="w-full py-2 px-3 rounded-xl text-xs font-bold text-center text-slate-600 bg-slate-100 hover:bg-slate-200 transition block">
                        ↺ Reset Semua Filter
                    </a>
                </div>
            @endif

        </aside>

        {{-- ── RIGHT COLUMN (CARDS GRID) ────────────────────────────────────── --}}
        <main class="lg:col-span-3 space-y-6">
            
            {{-- Results Header Summary --}}
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 text-xs text-slate-500">
                <p>
                    Menampilkan <strong class="text-slate-900 font-black">{{ $totalItemsCount }}</strong> program pelatihan tatap muka
                    @if($categorySlug)
                        pada bidang <span class="font-bold text-amber-700">"{{ $categories->firstWhere('slug', $categorySlug)?->name }}"</span>
                    @endif
                </p>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold border border-amber-200/60 text-[11px]">
                        🏛️ Klasikal / Tatap Muka
                    </span>
                </div>
            </div>

            @if($totalItemsCount > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    
                    {{-- 1. Courses of In-Class Type --}}
                    @foreach($courses as $course)
                        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                            <div>
                                <!-- Thumbnail Flyer Poster -->
                                <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                                    <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/30 to-transparent"></div>
                                    
                                    <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase bg-amber-600 text-white shadow-xs">
                                            🏛️ In-Class
                                        </span>
                                        @if($course->category)
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/95 text-slate-800 shadow-xs backdrop-blur-xs">
                                                {{ $course->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="absolute top-3 right-3">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-400 text-slate-900 shadow-xs">
                                            ⚡ {{ $course->jp ?? 12 }} JP
                                        </span>
                                    </div>

                                    <div class="absolute bottom-3 left-3 right-3 text-white text-xs">
                                        <div class="flex items-center gap-1.5 text-amber-200 text-[11px] font-semibold truncate">
                                            <span>📍</span>
                                            <span>{{ $course->schedule_location ?: 'Sasana Praja Pemkab Berau' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-5 space-y-3">
                                    {{-- Rating & Review Row (Mirip Gambar Referensi) --}}
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                                            <span>★</span>
                                            <span>{{ number_format($course->rating, 1) }}</span>
                                            <span class="text-slate-400 font-normal">({{ $course->students_count ?: 45 }} Peserta)</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                            {{ $course->level ?: 'Menengah' }}
                                        </span>
                                    </div>

                                    <h3 class="text-base font-black text-slate-900 line-clamp-2 group-hover:text-amber-700 transition leading-snug">
                                        <a href="{{ route('courses.show', $course->slug) }}">
                                            {{ $course->title }}
                                        </a>
                                    </h3>

                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $course->subtitle ?: $course->description }}
                                    </p>

                                    <!-- Date & Time Row -->
                                    <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                        <span>📅 {{ $course->start_date ? \Carbon\Carbon::parse($course->start_date)->locale('id')->isoFormat('dddd, D MMMM Y') : 'Jadwal Reguler 2026' }}</span>
                                    </div>

                                    @if($course->instructor)
                                        <div class="pt-3 border-t border-slate-100 flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ substr($course->instructor->name, 0, 2) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[10px] text-slate-400 uppercase font-bold">Instruktur / Fasilitator</p>
                                                <p class="text-xs font-bold text-slate-800 truncate">{{ $course->instructor->name }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer Action -->
                            <div class="p-5 pt-0 border-t border-slate-100 mt-2 pt-3 flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                        GRATIS
                                    </span>
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-amber-600 transition shadow-xs">
                                    <span>Lihat Detail</span>
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    @endforeach

                    {{-- 2. Webinars / Sessions of In-Class Delivery Mode --}}
                    @foreach($webinars as $wb)
                        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                            <div>
                                <!-- Thumbnail Flyer Poster -->
                                <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                                    <img src="{{ $wb->cover_image ?: 'https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $wb->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/30 to-transparent"></div>
                                    
                                    <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase bg-amber-600 text-white shadow-xs">
                                            🏛️ In-Class
                                        </span>
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/95 text-slate-800 shadow-xs backdrop-blur-xs">
                                            Bimtek Tatap Muka
                                        </span>
                                    </div>

                                    <div class="absolute top-3 right-3">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-400 text-slate-900 shadow-xs">
                                            ⚡ {{ $wb->jp ?? 12 }} JP
                                        </span>
                                    </div>

                                    <div class="absolute bottom-3 left-3 right-3 text-white text-xs">
                                        <div class="flex items-center gap-1.5 text-amber-200 text-[11px] font-semibold truncate">
                                            <span>📍</span>
                                            <span>{{ $wb->location_type ?: 'Sasana Praja Kantor Bupati Berau' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-5 space-y-3">
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                                            <span>★</span>
                                            <span>4.9</span>
                                            <span class="text-slate-400 font-normal">(60 Kuota)</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Pendaftaran Dibuka
                                        </span>
                                    </div>

                                    <h3 class="text-base font-black text-slate-900 line-clamp-2 group-hover:text-amber-700 transition leading-snug">
                                        <a href="{{ route('webinars.show', $wb->slug) }}">
                                            {{ $wb->title }}
                                        </a>
                                    </h3>

                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $wb->description }}
                                    </p>

                                    <!-- Date & Time Row -->
                                    <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                        <span>📅 {{ \Carbon\Carbon::parse($wb->event_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                                        <span>•</span>
                                        <span>⏰ {{ $wb->time_start }} WITA</span>
                                    </div>

                                    @if($wb->speaker_name)
                                        <div class="pt-3 border-t border-slate-100 flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ substr($wb->speaker_name, 0, 2) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[10px] text-slate-400 uppercase font-bold">Narasumber / Fasilitator</p>
                                                <p class="text-xs font-bold text-slate-800 truncate">{{ $wb->speaker_name }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer Action -->
                            <div class="p-5 pt-0 border-t border-slate-100 mt-2 pt-3 flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                        GRATIS
                                    </span>
                                </div>
                                <a href="{{ route('webinars.show', $wb->slug) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-amber-600 transition shadow-xs">
                                    <span>Lihat Detail</span>
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto">
                        🏛️
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Belum ada pelatihan tatap muka untuk filter ini</h3>
                    <p class="text-xs text-slate-500">Silakan pilih kategori lain atau reset kata kunci pencarian Anda.</p>
                    <a href="{{ route('in-class.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition">
                        Reset Filter
                    </a>
                </div>
            @endif

        </main>

    </div>
</div>
@endsection
