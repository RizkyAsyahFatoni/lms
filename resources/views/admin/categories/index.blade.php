@extends('layouts.admin')

@section('title', 'Kategori & Taksonomi Pembelajaran')
@section('page_title', 'Kategori & Taksonomi Pembelajaran')

@section('content')
<div class="space-y-6">

    <!-- Top Bar: Title & Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kategori & Taksonomi Pembelajaran</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                    TAKSONOMI IDP
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola Kategori Utama, Sub Kategori, dan Tag Materi untuk pencocokan IDP aparatur Kabupaten Berau.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Sync / Re-import from Excel Button -->
            <form action="{{ route('admin.categories.import-excel') }}" method="POST"
                  onsubmit="return confirm('Sinkronkan taksonomi dengan file Excel \"katagori lms.xlsx\"? Kategori dan sub-kategori yang belum ada akan ditambahkan otomatis.');">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-300 transition shadow-2xs"
                        title="Sinkronkan dengan C:\laragon\www\lms\katagori lms.xlsx">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Sinkronkan dari Excel</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Total Taksonomi</p>
                <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">{{ $totalCategories }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Kategori Utama (Parent)</p>
                <p class="text-2xl font-black text-indigo-700 tracking-tight mt-0.5">{{ $totalMain }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Sub Kategori & Spesialisasi</p>
                <p class="text-2xl font-black text-emerald-700 tracking-tight mt-0.5">{{ $totalSub }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
        </div>
    </div>

    <!-- WordPress 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Form Tambah Kategori Baru (4 Cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-4 sticky top-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Kategori Baru</span>
                </h2>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Buat Kategori Utama atau tentukan Induk untuk membuat Sub-Kategori.
                </p>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <!-- Nama -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Nama Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           placeholder="Contoh: Kompetensi Manajerial"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Nama kategori yang akan ditampilkan di LMS dan IDP.</p>
                    @error('name') <p class="text-rose-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Slug (URL)
                    </label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           placeholder="kompetensi-manajerial (opsional)"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, slug dibuat otomatis dari nama.</p>
                    @error('slug') <p class="text-rose-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                </div>

                <!-- Kategori Induk (Parent) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Kategori Induk (Parent)
                    </label>
                    <select name="parent_id"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="">— Tidak Ada (Jadikan Kategori Utama) —</option>
                        @foreach($mainCategories as $main)
                            <option value="{{ $main->id }}" {{ old('parent_id') == $main->id ? 'selected' : '' }}>
                                {{ $main->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Pilih induk jika kategori ini merupakan Sub-Kategori.</p>
                </div>

                <!-- Warna Badge -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Warna Badge Visual</label>
                    <select name="badge_color"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="teal" {{ old('badge_color') == 'teal' ? 'selected' : '' }}>Teal (Default)</option>
                        <option value="indigo" {{ old('badge_color') == 'indigo' ? 'selected' : '' }}>Indigo</option>
                        <option value="blue" {{ old('badge_color') == 'blue' ? 'selected' : '' }}>Blue</option>
                        <option value="emerald" {{ old('badge_color') == 'emerald' ? 'selected' : '' }}>Emerald</option>
                        <option value="amber" {{ old('badge_color') == 'amber' ? 'selected' : '' }}>Amber</option>
                        <option value="rose" {{ old('badge_color') == 'rose' ? 'selected' : '' }}>Rose</option>
                        <option value="purple" {{ old('badge_color') == 'purple' ? 'selected' : '' }}>Purple</option>
                        <option value="cyan" {{ old('badge_color') == 'cyan' ? 'selected' : '' }}>Cyan</option>
                        <option value="slate" {{ old('badge_color') == 'slate' ? 'selected' : '' }}>Slate</option>
                    </select>
                </div>

                <!-- Tag / Saran Sub-Sub Kategori -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Tag / Sub-Sub Kategori (Dropdown Suggestions)
                    </label>
                    <textarea name="tags" rows="3"
                              placeholder="Pisahkan dengan koma, contoh: Target SMART, Efisiensi Anggaran, Manajemen Waktu"
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">{{ old('tags') }}</textarea>
                    <p class="text-[10px] text-slate-400 mt-1">
                        Daftar tag spesifik ini akan otomatis muncul sebagai saran klik saat pembuat materi menyusun kursus.
                    </p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2"
                              placeholder="Deskripsi ruang lingkup kompetensi..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">{{ old('description') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Simpan Kategori Baru</span>
                </button>
            </form>
        </div>

        <!-- RIGHT COLUMN: Tabel Kategori Hirarkis (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden space-y-4">
            
            <!-- Table Header & Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="flex items-center gap-2 flex-1 max-w-md">
                    <div class="relative flex-1">
                        <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama, slug, atau tag..."
                               class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <select name="parent" onchange="this.form.submit()"
                            class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 text-slate-600">
                        <option value="all" {{ ($parentFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Taksonomi</option>
                        <option value="main" {{ ($parentFilter ?? 'all') === 'main' ? 'selected' : '' }}>Hanya Kategori Utama</option>
                        <option value="sub" {{ ($parentFilter ?? 'all') === 'sub' ? 'selected' : '' }}>Hanya Sub-Kategori</option>
                    </select>

                    @if(!empty($search) || ($parentFilter ?? 'all') !== 'all')
                        <a href="{{ route('admin.categories.index') }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100" title="Reset filter">
                            ✕
                        </a>
                    @endif
                </form>

                <div class="text-[11px] text-slate-400">
                    Menampilkan <strong>{{ $categories->total() }}</strong> taksonomi
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-4">Nama Taksonomi</th>
                            <th class="p-4">Induk (Parent)</th>
                            <th class="p-4">Tag / Saran Sub-Sub Topik</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4 text-center">Kursus</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $cat)
                            @php
                                $isSub = $cat->parent_id !== null;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition {{ !$isSub ? 'bg-slate-50/30' : '' }}">
                                <!-- Name -->
                                <td class="p-4 font-medium text-slate-900">
                                    <div class="flex items-center gap-2">
                                        @if($isSub)
                                            <span class="text-slate-300 font-mono text-xs pl-3">—</span>
                                            <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                                            <div class="min-w-0">
                                                <span class="font-bold text-slate-800">{{ $cat->name }}</span>
                                                @if($cat->description)
                                                    <p class="text-[10px] text-slate-400 truncate max-w-xs">{{ $cat->description }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                UTAMA
                                            </span>
                                            <div class="min-w-0">
                                                <span class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $cat->name }}</span>
                                                <span class="text-[10px] font-mono text-slate-400 block">{{ $cat->sub_categories_count ?? $cat->subCategories->count() }} sub-kategori</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Parent -->
                                <td class="p-4 text-slate-600">
                                    @if($cat->parent)
                                        <span class="inline-flex items-center gap-1 font-semibold text-slate-700 text-xs">
                                            <span>{{ $cat->parent->name }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">— Kategori Utama —</span>
                                    @endif
                                </td>

                                <!-- Tags / Dropdown Suggestions -->
                                <td class="p-4 max-w-xs">
                                    @if(!empty($cat->tags_array))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_slice($cat->tags_array, 0, 3) as $t)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $t }}
                                                </span>
                                            @endforeach
                                            @if(count($cat->tags_array) > 3)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-teal-50 text-teal-700 border border-teal-200" title="{{ $cat->tags }}">
                                                    +{{ count($cat->tags_array) - 3 }} lagi
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- Slug -->
                                <td class="p-4 font-mono text-[11px] text-slate-400 truncate max-w-[120px]">
                                    {{ $cat->slug }}
                                </td>

                                <!-- Total Courses -->
                                <td class="p-4 text-center">
                                    @php
                                        $count = $isSub ? $cat->sub_courses_count : $cat->total_courses_count;
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono {{ $count > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $count }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="p-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.categories.edit', $cat->id) }}"
                                           class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-700 hover:text-brand-700 hover:bg-brand-50 transition border border-slate-200">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \"{{ $cat->name }}\"?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                    title="Hapus Kategori">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    Tidak ada kategori yang cocok dengan pencarian Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $categories->links() }}
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
