@extends('layouts.admin')

@section('page_title', 'Artikel & Repositori Pengetahuan')

@section('content')
<div class="space-y-6">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Artikel & Repositori Pengetahuan</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-teal-50 text-teal-700 border border-teal-200">
                    KAK BAB XVI
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola publikasi materi Corporate University, dokumen regulasi daerah (Perbup/SK), panduan SOP teknis, dan bahan presentasi pembelajaran ASN.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('articles.index') }}" target="_blank" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
                <span>🌐 Lihat di Web Publik</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>

            <a href="{{ route('admin.articles.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-600 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tulis Artikel & Unggah Berkas</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Total Artikel</p>
            <p class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($stats['total']) }}</p>
            <span class="text-[10px] text-slate-400">Seluruh publikasi</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Status Terbit</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tight mt-1">{{ number_format($stats['published']) }}</p>
            <span class="text-[10px] text-emerald-600 font-semibold">Tampil di portal</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Draf / Belum Terbit</p>
            <p class="text-2xl font-black text-amber-600 tracking-tight mt-1">{{ number_format($stats['draft']) }}</p>
            <span class="text-[10px] text-amber-600 font-semibold">Perlu peninjauan</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Berkas Lampiran</p>
            <p class="text-2xl font-black text-indigo-600 tracking-tight mt-1">{{ number_format($stats['attachments']) }}</p>
            <span class="text-[10px] text-indigo-500 font-semibold">Regulasi, SOP, Paparan</span>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-sm">
        <form action="{{ route('admin.articles.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:w-80">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul, kata kunci, isi..." 
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto">
                <select name="category" onchange="this.form.submit()" class="py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white">
                    <option value="all">Semua Kategori</option>
                    <option value="regulasi" {{ $category === 'regulasi' ? 'selected' : '' }}>⚖️ Regulasi & Kebijakan</option>
                    <option value="panduan" {{ $category === 'panduan' ? 'selected' : '' }}>📘 Panduan & SOP</option>
                    <option value="presentasi" {{ $category === 'presentasi' ? 'selected' : '' }}>📊 Materi Presentasi</option>
                    <option value="best_practice" {{ $category === 'best_practice' ? 'selected' : '' }}>💡 Best Practice</option>
                    <option value="materi_sharing" {{ $category === 'materi_sharing' ? 'selected' : '' }}>🤝 Knowledge Sharing</option>
                    <option value="umum" {{ $category === 'umum' ? 'selected' : '' }}>📰 Artikel Umum</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white">
                    <option value="all">Semua Status</option>
                    <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Terbit</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draf</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                    Filter
                </button>

                @if($search || $category !== 'all' || $status !== 'all')
                    <a href="{{ route('admin.articles.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Articles -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-4">Artikel & Ringkasan</th>
                        <th class="p-4">Kategori & OPD</th>
                        <th class="p-4">Lampiran Dokumen</th>
                        <th class="p-4">Status & Waktu</th>
                        <th class="p-4 text-center">Statistik</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($articles as $article)
                        @php
                            $meta = $article->category_meta;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4 max-w-sm">
                                <div class="flex items-start gap-3">
                                    @if($article->cover_image)
                                        <img src="{{ asset('storage/' . $article->cover_image) }}" alt="" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-lg shrink-0">
                                            {{ $meta['icon'] }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('articles.show', $article->slug) }}" target="_blank" class="font-bold text-slate-900 hover:text-brand-600 transition line-clamp-1 text-xs">
                                            {{ $article->title }}
                                        </a>
                                        <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                            {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 80) }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $meta['badge'] }} border">
                                    <span>{{ $meta['icon'] }}</span>
                                    <span>{{ $meta['label'] }}</span>
                                </span>
                                <p class="text-[11px] text-slate-500 mt-1 font-medium truncate max-w-[150px]">
                                    🏛️ {{ $article->opd_name ?: 'BKPSDM Berau' }}
                                </p>
                            </td>

                            <td class="p-4">
                                @if($article->attachments->isNotEmpty())
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-800">
                                            <span>📎</span> {{ $article->attachments->count() }} Dokumen
                                        </span>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($article->attachments->take(3) as $att)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold uppercase {{ $att->extension_style['badge'] }} border" title="{{ $att->title }}">
                                                    {{ $att->extension_style['name'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Tanpa lampiran</span>
                                @endif
                            </td>

                            <td class="p-4 whitespace-nowrap">
                                <form action="{{ route('admin.articles.toggle-status', $article->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase transition {{ $article->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' }}">
                                        {{ $article->is_published ? '● Terbit' : '○ Draf' }}
                                    </button>
                                </form>
                                <p class="text-[10px] text-slate-400 font-mono mt-1">
                                    {{ $article->created_at->format('d/m/Y H:i') }}
                                </p>
                            </td>

                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="text-[11px] text-slate-600 font-mono">
                                    <div>👁️ {{ number_format($article->views_count) }}</div>
                                    <div class="text-[10px] text-slate-400">📥 {{ number_format($article->attachments->sum('downloads_count')) }}</div>
                                </div>
                            </td>

                            <td class="p-4 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('articles.show', $article->slug) }}" target="_blank" 
                                   class="p-1.5 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded-lg inline-block transition" title="Lihat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <a href="{{ route('admin.articles.edit', $article->id) }}" 
                                   class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg inline-block transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini beserta seluruh berkas lampirannya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Belum ada artikel atau dokumen yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
