@extends('layouts.admin')

@section('title', 'Edit Kategori: ' . $category->name)
@section('page_title', 'Edit Kategori')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.categories.index') }}" class="hover:text-slate-700 transition">Kategori</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold truncate">{{ $category->name }}</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Kategori & Taksonomi</h1>
        </div>

        <a href="{{ route('admin.categories.index') }}"
           class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- Nama -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required value="{{ old('name', $category->name) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-bold text-slate-900">
                @error('name') <p class="text-rose-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
            </div>

            <!-- Slug -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Slug (URL)
                </label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none text-slate-700">
                <p class="text-[10px] text-slate-400 mt-1">Slug URL unik yang digunakan untuk link katalog dan filter materi.</p>
                @error('slug') <p class="text-rose-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
            </div>

            <!-- Kategori Induk (Parent) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Kategori Induk (Parent)
                </label>
                <select name="parent_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                    <option value="">— Tidak Ada (Jadikan Kategori Utama) —</option>
                    @foreach($mainCategories as $main)
                        <option value="{{ $main->id }}" {{ old('parent_id', $category->parent_id) == $main->id ? 'selected' : '' }}>
                            {{ $main->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Jika diatur ke "Tidak Ada", kategori ini akan menjadi Kategori Utama (Parent Level 1).</p>
            </div>

            <!-- Warna Badge -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Warna Badge Visual</label>
                <select name="badge_color"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                    @php $colors = ['teal', 'indigo', 'blue', 'emerald', 'amber', 'rose', 'purple', 'cyan', 'slate']; @endphp
                    @foreach($colors as $col)
                        <option value="{{ $col }}" {{ old('badge_color', $category->badge_color) == $col ? 'selected' : '' }}>
                            {{ ucfirst($col) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tag / Saran Sub-Sub Kategori -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Tag / Sub-Sub Kategori (Dropdown Suggestions)
                </label>
                <textarea name="tags" rows="3"
                          placeholder="Pisahkan dengan koma, contoh: Target SMART, Efisiensi Anggaran, Manajemen Waktu"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">{{ old('tags', $category->tags) }}</textarea>
                <p class="text-[10px] text-slate-400 mt-1">
                    Daftar tag kompetensi ini otomatis disarankan saat instruktur/admin menyusun materi kursus.
                </p>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="3"
                          placeholder="Deskripsi ruang lingkup kompetensi..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.categories.index') }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
