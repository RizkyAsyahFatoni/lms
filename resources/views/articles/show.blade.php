@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/60 pb-20">
    
    <!-- Breadcrumb & Top Bar -->
    <div class="bg-white border-b border-slate-200/80 py-3.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs text-slate-500 overflow-x-auto">
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Beranda</span>
                </a>
                <span>/</span>
                <a href="{{ route('articles.index') }}" class="hover:text-slate-900 transition">
                    Artikel & Pengetahuan
                </a>
                <span>/</span>
                <a href="{{ route('articles.index', ['category' => $article->category]) }}" class="hover:text-slate-900 transition">
                    {{ $article->category_meta['label'] }}
                </a>
                <span>/</span>
                <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $article->title }}</span>
            </nav>
        </div>
    </div>

    <!-- Article Hero Section -->
    <div class="relative bg-white border-b border-slate-200/80 py-10 lg:py-12 overflow-hidden" style="background-color: #ffffff;">
        @if($article->cover_image)
            <!-- Ambient Cover Thumbnail Background (Opasitas 27% Murni) -->
            <div class="absolute inset-0 z-0 pointer-events-none select-none overflow-hidden" style="background-color: #ffffff;">
                <img src="{{ asset('storage/' . $article->cover_image) }}" 
                     alt="" 
                     class="w-full h-full object-cover pointer-events-none"
                     style="opacity: 0.27; filter: blur(0.5px);">
                <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(255, 255, 255, 0.85) 0%, rgba(255, 255, 255, 0.45) 55%, rgba(255, 255, 255, 0.15) 100%);"></div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-4xl space-y-4">
                
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ $article->category_meta['badge'] }} border shadow-2xs">
                        <span>{{ $article->category_meta['icon'] }}</span>
                        <span>{{ $article->category_meta['label'] }}</span>
                    </span>

                    @if($article->attachments->isNotEmpty())
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-900 text-white shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span>{{ $article->attachments->count() }} Dokumen Lampiran</span>
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight leading-snug" style="color: #0f172a !important;">
                    {{ $article->title }}
                </h1>

                @if($article->excerpt)
                    <p class="text-base text-slate-700 leading-relaxed font-normal" style="color: #334155 !important;">
                        {{ $article->excerpt }}
                    </p>
                @endif

                <!-- Author & Publishing Meta -->
                <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-4 text-xs text-slate-500 border-t border-slate-100/90">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-brand-50 border border-brand-200 text-brand-700 font-bold flex items-center justify-center text-xs shadow-2xs">
                            {{ substr($article->author_name ?: 'A', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $article->author_name ?: 'Tim BKPSDM' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $article->opd_name ?: 'Pemerintah Kabupaten Berau' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-slate-400 font-medium">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $article->published_at ? $article->published_at->format('d F Y') : $article->created_at->format('d F Y') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ number_format($article->views_count) }} kali dibaca
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Main Content Layout (2 Column) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Col (8 Cols): Content & Article Body -->
            <div class="lg:col-span-8 space-y-8">

                <!-- Article Content Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="prose prose-slate max-w-none text-slate-800 leading-relaxed text-sm sm:text-base article-body">
                        @if(str_contains($article->content, '<p>') || str_contains($article->content, '<h') || str_contains($article->content, '<ul>') || str_contains($article->content, '<ol>') || str_contains($article->content, '<img'))
                            {!! $article->clean_content !!}
                        @else
                            {!! nl2br(e($article->content)) !!}
                        @endif
                    </div>

                    <!-- Share & Print Toolbar -->
                    <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Bagikan Materi:</span>
                            <button onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan materi berhasil disalin!');" 
                                    class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Salin Tautan</span>
                            </button>
                            <button onclick="window.print();" 
                                    class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Dokumen</span>
                            </button>
                        </div>

                        <a href="{{ route('articles.index') }}" class="font-bold text-brand-600 hover:text-brand-700 hover:underline">
                            &larr; Kembali ke Pusat Pengetahuan
                        </a>
                    </div>
                </div>

                <!-- Attachments Section (In-Content Mobile & Desktop Anchor) -->
                @if($article->attachments->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-5" id="attachments-box">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                    <span>📎</span> Berkas Lampiran & Dokumen Terkait
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Unduh dokumen regulasi, petunjuk teknis (SOP), atau materi presentasi resmi di bawah ini.
                                </p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                {{ $article->attachments->count() }} Berkas
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-3.5">
                            @foreach($article->attachments as $attach)
                                @php
                                    $style = $attach->extension_style;
                                    $badge = $attach->type_badge;
                                @endphp
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 hover:border-brand-300 hover:bg-brand-50/30 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start gap-3.5">
                                        <!-- Extension Icon -->
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-xs font-mono {{ $style['bg'] }} {{ $style['text'] }} shadow-xs shrink-0">
                                            {{ $style['name'] }}
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $badge['bg'] }} border">
                                                    <span>{{ $badge['icon'] }}</span>
                                                    <span>{{ $badge['label'] }}</span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono font-medium">
                                                    • {{ $attach->formatted_size }}
                                                </span>
                                            </div>
                                            <h3 class="text-sm font-bold text-slate-900 leading-snug">
                                                {{ $attach->title }}
                                            </h3>
                                            @if($attach->file_name)
                                                <p class="text-[11px] text-slate-500 font-mono truncate max-w-md">
                                                    {{ $attach->file_name }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Download Button Action -->
                                    <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                                        @if($attach->external_url)
                                            <a href="{{ route('articles.attachments.download', $attach->id) }}" 
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-950 bg-emerald-400 hover:bg-emerald-500 transition shadow-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                <span>Buka Tautan Berkas</span>
                                            </a>
                                        @else
                                            <a href="{{ route('articles.attachments.download', $attach->id) }}" 
                                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-600 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>Unduh Dokumen</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Col (4 Cols): Sidebar Highlights & Attachments Sticky Widget -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- 1. Widget Pencarian Artikel -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span>🔍</span> Cari Artikel & Materi
                    </h3>
                    <form action="{{ route('articles.index') }}" method="GET" class="relative">
                        <div class="relative">
                            <input type="text" name="q" placeholder="Cari topik, regulasi, materi..." 
                                   class="w-full pl-9 pr-14 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <button type="submit" 
                                    class="absolute inset-y-1 right-1 px-3 bg-slate-900 hover:bg-brand-600 text-white rounded-lg text-[10px] font-bold transition flex items-center justify-center">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. Widget Daftar Kategori Pengetahuan -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-3.5">
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>📂</span> Kategori Pengetahuan
                        </h3>
                        <a href="{{ route('articles.index') }}" class="text-[11px] font-semibold text-brand-600 hover:underline">
                            Semua
                        </a>
                    </div>

                    <div class="space-y-1.5">
                        @php
                            $categoriesList = [
                                'regulasi'      => ['label' => 'Regulasi & Kebijakan', 'icon' => '⚖️'],
                                'panduan'       => ['label' => 'Panduan Teknis & SOP', 'icon' => '📘'],
                                'presentasi'    => ['label' => 'Materi Presentasi & Paparan', 'icon' => '📊'],
                                'best_practice' => ['label' => 'Best Practice & Inovasi', 'icon' => '💡'],
                                'materi_sharing'=> ['label' => 'Knowledge Sharing ASN', 'icon' => '🤝'],
                                'umum'          => ['label' => 'Artikel Umum / Wawasan', 'icon' => '📰'],
                            ];
                        @endphp

                        @foreach($categoriesList as $catKey => $catData)
                            @php
                                $count = $categoryCounts[$catKey] ?? 0;
                                $isActive = ($article->category === $catKey);
                            @endphp
                            <a href="{{ route('articles.index', ['category' => $catKey]) }}" 
                               class="flex items-center justify-between p-2.5 rounded-xl transition text-xs group {{ $isActive ? 'bg-teal-50 border border-teal-200 text-teal-900 font-bold' : 'hover:bg-slate-50 text-slate-700 border border-transparent' }}">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="text-sm shrink-0">{{ $catData['icon'] }}</span>
                                    <span class="truncate {{ $isActive ? 'text-teal-950 font-bold' : 'group-hover:text-slate-900' }}">{{ $catData['label'] }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold shrink-0 {{ $isActive ? 'bg-teal-200/80 text-teal-800' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200' }}">
                                    {{ $count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- OPD Information Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-3">
                    <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">Instansi Penerbit / Kontributor</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200 text-teal-700 flex items-center justify-center font-bold text-base">
                            🏛️
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $article->opd_name ?: 'BKPSDM Kabupaten Berau' }}</h4>
                            <p class="text-[11px] text-slate-500">Pemerintah Kabupaten Berau</p>
                        </div>
                    </div>
                </div>

                <!-- Related Articles / Rekomendasi Materi Lain -->
                @if($relatedArticles->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Artikel Terkait Lainnya</h3>
                            <a href="{{ route('articles.index', ['category' => $article->category]) }}" class="text-[11px] text-brand-600 font-semibold hover:underline">
                                Semua &rarr;
                            </a>
                        </div>

                        <div class="space-y-3">
                            @foreach($relatedArticles as $rel)
                                <a href="{{ route('articles.show', $rel->slug) }}" class="block p-3 rounded-xl hover:bg-slate-50 transition group space-y-1">
                                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                                        <span class="font-bold text-teal-700 uppercase">{{ $rel->category_meta['label'] }}</span>
                                        <span>{{ $rel->published_at ? $rel->published_at->format('d M Y') : '' }}</span>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition line-clamp-2">
                                        {{ $rel->title }}
                                    </h4>
                                    @if($rel->attachments_count > 0)
                                        <p class="text-[10px] text-slate-400 font-mono">
                                            📎 {{ $rel->attachments_count }} berkas terlampir
                                        </p>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>

</div>

<style>
    /* Styling List & Numbering Khusus Isi Konten Artikel */
    .article-body ol {
        list-style: decimal !important;
        list-style-type: decimal !important;
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
        padding-left: 1.75rem !important;
    }
    .article-body ol > li {
        list-style: decimal !important;
        list-style-type: decimal !important;
        display: list-item !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
        padding-left: 0.25rem !important;
        line-height: 1.75 !important;
    }
    .article-body ul {
        list-style: disc !important;
        list-style-type: disc !important;
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
        padding-left: 1.75rem !important;
    }
    .article-body ul > li {
        list-style: disc !important;
        list-style-type: disc !important;
        display: list-item !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
        padding-left: 0.25rem !important;
        line-height: 1.75 !important;
    }
    .article-body li[data-list="ordered"] {
        list-style-type: decimal !important;
    }
    .article-body li[data-list="bullet"] {
        list-style-type: disc !important;
    }
    .article-body .ql-ui {
        display: none !important;
    }
    .article-body p {
        margin-top: 0.85rem !important;
        margin-bottom: 0.85rem !important;
        line-height: 1.8 !important;
    }
    .article-body h1, .article-body h2, .article-body h3, .article-body h4 {
        color: #0f172a !important;
        font-weight: 800 !important;
        margin-top: 1.5rem !important;
        margin-bottom: 0.75rem !important;
        line-height: 1.35 !important;
    }
    .article-body h1 { font-size: 1.75rem !important; }
    .article-body h2 { font-size: 1.45rem !important; }
    .article-body h3 { font-size: 1.25rem !important; }
    .article-body h4 { font-size: 1.1rem !important; }
    .article-body blockquote {
        border-left: 4px solid #0d9488 !important;
        padding: 0.75rem 1.25rem !important;
        background-color: #f0fdfa !important;
        color: #334155 !important;
        font-style: italic !important;
        margin: 1.25rem 0 !important;
        border-radius: 0 0.75rem 0.75rem 0 !important;
    }
    .article-body table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 1.25rem 0 !important;
    }
    .article-body table th, .article-body table td {
        border: 1px solid #e2e8f0 !important;
        padding: 0.6rem 0.85rem !important;
    }
    .article-body table th {
        background-color: #f8fafc !important;
        font-weight: 700 !important;
    }
    .article-body img {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 1rem !important;
        margin: 1.5rem auto !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
