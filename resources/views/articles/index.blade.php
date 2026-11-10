@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/60 pb-16">
    
    <!-- Hero Banner & Header -->
    <div class="relative bg-gradient-to-br from-slate-900 via-brand-950 to-teal-950 text-white overflow-hidden py-12 lg:py-16 border-b border-slate-800">
        <!-- Background Banner Image Transparent (Tema Artikel, Regulasi & Materi Pembelajaran) -->
        <div class="absolute inset-0 z-0 pointer-events-none select-none overflow-hidden">
            <img src="{{ asset('images/banner-articles-header.jpg') }}" alt="Regulasi & Materi Pembelajaran" 
                 class="w-full h-full object-cover object-right"
                 style="opacity: 0.35; mix-blend-mode: lighten; filter: contrast(1.05);">
            <!-- Gradien pelindung kontras teks di sisi kiri dan transisi halus -->
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/85 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/40"></div>
        </div>

        <div class="relative max-w-7xl xl:max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md border border-white/20 text-emerald-300 mb-4 shadow-2xs">
                    <span>📚</span> Repositori Pengetahuan & Corporate University Kab. Berau
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Pusat Artikel, Regulasi & <span class="text-emerald-400 font-black drop-shadow-md" style="color: #34d399; text-shadow: 0 2px 12px rgba(0,0,0,0.7);">Materi Pembelajaran</span>
                </h1>
                <p class="mt-4 text-sm sm:text-base text-slate-200 leading-relaxed">
                    Akses terpadu literatur kompetensi ASN, peraturan kepala daerah, panduan teknis operasional (SOP), bahan presentasi narasumber, serta praktik baik (best practices) antar perangkat daerah.
                </p>
            </div>

            <!-- Quick Metrics Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-8 pt-8 border-t border-white/10">
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-xs text-slate-300 font-medium">Total Publikasi</p>
                    <p class="text-2xl sm:text-3xl font-black text-white mt-1">{{ number_format($stats['total_articles']) }}</p>
                    <span class="text-xs font-bold text-emerald-300" style="color: #6ee7b7;">Artikel & Materi</span>
                </div>
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-xs text-slate-300 font-medium">Regulasi & Kebijakan</p>
                    <p class="text-2xl sm:text-3xl font-black text-rose-300 mt-1">{{ number_format($stats['total_regulations']) }}</p>
                    <span class="text-xs font-bold text-rose-300">Dokumen Perbup/UU</span>
                </div>
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-xs text-slate-300 font-medium">Panduan & SOP</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-300 mt-1" style="color: #6ee7b7;">{{ number_format($stats['total_guides']) }}</p>
                    <span class="text-xs font-bold text-emerald-300" style="color: #6ee7b7;">Buku Pedoman</span>
                </div>
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-xs text-slate-300 font-medium">Bahan Presentasi</p>
                    <p class="text-2xl sm:text-3xl font-black text-amber-300 mt-1">{{ number_format($stats['total_slides']) }}</p>
                    <span class="text-xs font-bold text-amber-300">Slide Paparan / Modul</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Flexbox Layout: Sidebar Kiri Ramping + Konten Kanan 3 Kolom) -->
    <div class="max-w-7xl xl:max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        <div class="flex flex-col lg:flex-row items-start gap-8">

            <!-- SIDEBAR KIRI: Pencarian, Kategori & Filter (Lebar Tetap Ramping 288px) -->
            <aside class="w-full lg:w-72 shrink-0 space-y-5">

                <!-- 1. Box Pencarian Kata Kunci -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🔍</span> Cari Dokumen
                        </h3>
                        @if($search)
                            <a href="{{ route('articles.index', array_merge(request()->except('q'), ['q' => ''])) }}" 
                                class="text-[10px] text-rose-500 font-semibold hover:underline">
                                Hapus
                            </a>
                        @endif
                    </div>

                    <form action="{{ route('articles.index') }}" method="GET">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="attachment_type" value="{{ $attachmentType }}">
                        <input type="hidden" name="opd" value="{{ $opdFilter }}">

                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="text" name="q" value="{{ $search }}" 
                                       placeholder="Kata kunci, judul..." 
                                       class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                            </div>
                            <button type="submit" 
                                    class="px-3.5 py-2 bg-slate-900 hover:bg-brand-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center shrink-0 shadow-xs">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. Box Kategori Materi Pengetahuan -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>📂</span> Kategori Materi
                        </h3>
                        @if($category !== 'all')
                            <a href="{{ route('articles.index', array_merge(request()->query(), ['category' => 'all'])) }}" 
                               class="text-[10px] font-semibold text-brand-600 hover:underline">
                                Semua
                            </a>
                        @endif
                    </div>

                    <div class="space-y-1">
                        @php
                            $catMenu = [
                                'all'           => ['label' => 'Semua Kategori', 'icon' => '📁', 'count' => $stats['total_articles']],
                                'regulasi'      => ['label' => 'Regulasi & Kebijakan', 'icon' => '⚖️', 'count' => $categoryCounts['regulasi'] ?? 0],
                                'panduan'       => ['label' => 'Panduan Teknis & SOP', 'icon' => '📘', 'count' => $categoryCounts['panduan'] ?? 0],
                                'presentasi'    => ['label' => 'Materi Presentasi & Paparan', 'icon' => '📊', 'count' => $categoryCounts['presentasi'] ?? 0],
                                'best_practice' => ['label' => 'Best Practice & Inovasi', 'icon' => '💡', 'count' => $categoryCounts['best_practice'] ?? 0],
                                'materi_sharing'=> ['label' => 'Knowledge Sharing ASN', 'icon' => '🤝', 'count' => $categoryCounts['materi_sharing'] ?? 0],
                                'umum'          => ['label' => 'Artikel Umum / Wawasan', 'icon' => '📰', 'count' => $categoryCounts['umum'] ?? 0],
                            ];
                        @endphp

                        @foreach($catMenu as $cKey => $cVal)
                            @php
                                $isActive = ($category === $cKey);
                            @endphp
                            <a href="{{ route('articles.index', array_merge(request()->query(), ['category' => $cKey])) }}" 
                               class="flex items-center justify-between p-2.5 rounded-xl transition text-xs group {{ $isActive ? 'bg-teal-50 border border-teal-200 text-teal-950 font-bold shadow-2xs' : 'hover:bg-slate-50 text-slate-700 border border-transparent' }}">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="text-sm shrink-0">{{ $cVal['icon'] }}</span>
                                    <span class="truncate {{ $isActive ? 'text-teal-950 font-bold' : 'group-hover:text-slate-900' }}">{{ $cVal['label'] }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold shrink-0 {{ $isActive ? 'bg-teal-200/80 text-teal-800' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200' }}">
                                    {{ $cVal['count'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Box Filter Tipe Lampiran Berkas -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>📎</span> Jenis Lampiran
                        </h3>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('articles.index', array_merge(request()->query(), ['attachment_type' => 'all'])) }}"
                           class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $attachmentType === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua
                        </a>
                        <a href="{{ route('articles.index', array_merge(request()->query(), ['attachment_type' => 'regulasi'])) }}"
                           class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $attachmentType === 'regulasi' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-100' }}">
                            Perbup / SK
                        </a>
                        <a href="{{ route('articles.index', array_merge(request()->query(), ['attachment_type' => 'panduan'])) }}"
                           class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $attachmentType === 'panduan' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-100' }}">
                            SOP / Juknis
                        </a>
                        <a href="{{ route('articles.index', array_merge(request()->query(), ['attachment_type' => 'presentasi'])) }}"
                           class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $attachmentType === 'presentasi' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-100' }}">
                            Slide Paparan
                        </a>
                    </div>
                </div>

                <!-- 4. Box Filter OPD Kontributor -->
                @if($availableOpds->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 space-y-3">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🏛️</span> OPD Kontributor
                        </h3>
                        <form action="{{ route('articles.index') }}" method="GET">
                            <input type="hidden" name="q" value="{{ $search }}">
                            <input type="hidden" name="category" value="{{ $category }}">
                            <input type="hidden" name="attachment_type" value="{{ $attachmentType }}">

                            <select name="opd" onchange="this.form.submit()" 
                                    class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                                <option value="all">Semua OPD Kontributor</option>
                                @foreach($availableOpds as $opd)
                                    <option value="{{ $opd }}" {{ $opdFilter === $opd ? 'selected' : '' }}>{{ $opd }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif

                <!-- 5. Tombol Reset Filter -->
                @if($search || $category !== 'all' || $attachmentType !== 'all' || $opdFilter !== 'all')
                    <div>
                        <a href="{{ route('articles.index') }}" 
                           class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs font-bold transition flex items-center justify-center gap-2 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Reset Filter</span>
                        </a>
                    </div>
                @endif

            </aside>

            <!-- KONTEN UTAMA KANAN (Mengambil Seluruh Sisa Ruang Flex) -->
            <main class="flex-1 min-w-0 w-full space-y-6">
                
                <!-- Action & Status Header Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-200/80">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">
                                Koleksi Pengetahuan & Regulasi
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                {{ $articles->total() }} Materi
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">
                            @if($search)
                                Hasil pencarian kata kunci: <span class="font-bold text-slate-800">"{{ $search }}"</span> • 
                            @endif
                            @if($category !== 'all')
                                Kategori: <span class="font-bold text-teal-700">{{ $catMenu[$category]['label'] ?? $category }}</span>
                            @else
                                Seluruh literatur dan dokumen pembelajaran ASN terverifikasi
                            @endif
                        </p>
                    </div>
                    
                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isAdminOpd())
                            <a href="{{ route('admin.articles.create') }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-brand-600 text-white rounded-xl text-xs font-bold transition shadow-sm self-start sm:self-auto shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Unggah Artikel / Berkas</span>
                            </a>
                        @endif
                    @endauth
                </div>

                @if($articles->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-sm my-6">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-4">
                            🔍
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Tidak ada artikel atau dokumen yang sesuai</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            Coba ubah kata kunci pencarian, pilih kategori lain, atau reset filter pencarian untuk melihat seluruh koleksi materi.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('articles.index') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-brand-600 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                Lihat Seluruh Artikel & Dokumen
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Grid of Cards (3 Kolom Responsif & Bersih) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($articles as $article)
                            @php
                                $meta = $article->category_meta;
                            @endphp
                            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 flex flex-col group overflow-hidden min-w-0">
                                
                                <!-- Header / Cover Preview -->
                                <div class="relative h-44 bg-gradient-to-tr from-slate-900 to-slate-800 overflow-hidden flex items-center justify-center">
                                    @if($article->cover_image)
                                        <img src="{{ asset('storage/' . $article->cover_image) }}" 
                                             alt="{{ $article->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-slate-900 to-teal-950 flex flex-col items-center justify-center p-4 text-center">
                                            <span class="text-3xl mb-1.5">{{ $meta['icon'] }}</span>
                                            <span class="text-xs font-bold text-slate-300 line-clamp-2 px-3">{{ $article->title }}</span>
                                        </div>
                                    @endif

                                    <!-- Category Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-white/95 backdrop-blur-md {{ $meta['badge'] }} shadow-xs">
                                            <span>{{ $meta['icon'] }}</span>
                                            <span>{{ $meta['label'] }}</span>
                                        </span>
                                    </div>

                                    <!-- Attachments Count Pill -->
                                    @if($article->attachments_count > 0)
                                        <div class="absolute bottom-3 right-3">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-900/90 backdrop-blur-md text-white border border-white/20 shadow-xs">
                                                <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>{{ $article->attachments_count }} Berkas</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Card Body (Clean & Focused) -->
                                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                                            <span class="truncate max-w-[150px] font-medium text-slate-600">
                                                🏛️ {{ $article->opd_name ?: 'BKPSDM Berau' }}
                                            </span>
                                            <span class="font-mono text-[10px]">{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</span>
                                        </div>

                                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-600 transition leading-snug line-clamp-2">
                                            <a href="{{ route('articles.show', $article->slug) }}">
                                                {{ $article->title }}
                                            </a>
                                        </h3>

                                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                            {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 120) }}
                                        </p>
                                    </div>

                                    <!-- Footer Card Action -->
                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2 text-slate-400 text-[11px]">
                                            <span>👁️ {{ number_format($article->views_count) }} baca</span>
                                            @if($article->attachments->sum('downloads_count') > 0)
                                                <span>📥 {{ number_format($article->attachments->sum('downloads_count')) }} unduh</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('articles.show', $article->slug) }}" 
                                           class="inline-flex items-center gap-1 font-bold text-brand-600 hover:text-brand-700 group-hover:translate-x-0.5 transition-transform text-xs">
                                            <span>Buka</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>

                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-8 pt-4 border-t border-slate-200/80">
                        {{ $articles->links() }}
                    </div>
                @endif

            </main>

        </div>
    </div>

</div>
@endsection
