@extends('layouts.admin')

@section('page_title', 'Slider & Hero Banner')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, courseModal: false, courseSearch: '', activeSlider: {} }">

    @php
        $existingCourseIds = $sliders->pluck('course_id')->filter()->toArray();
    @endphp

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen Slider & Hero Banner</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $sliders->count() }} Banner
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Pilih materi kursus unggulan untuk banner atau atur pesan promosi manual yang tampil di beranda LMS.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Button Pilih Dari Kursus LMS -->
            <button @click="courseModal = true" 
                    type="button"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>+ Pilih dari Kursus LMS</span>
            </button>

            <!-- Button Tambah Manual -->
            <button @click="addModal = true" 
                    type="button"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-2xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Buat Banner Manual</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Info Pengaturan Grid Beranda -->
    <div class="p-3.5 bg-brand-50/50 border border-brand-200/80 rounded-2xl flex items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="w-8 h-8 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold shrink-0">📐</span>
            <div class="min-w-0">
                <span class="font-bold text-slate-800 block">Format Grid Program Unggulan Beranda:</span>
                <span class="text-[11px] text-slate-500">
                    Saat ini menggunakan layout <strong>{{ ($appSetting->featured_grid_layout ?? '2x4') === '2x3' ? '2x3 (3 Kolom per Baris)' : '2x4 (4 Kolom per Baris)' }}</strong>.
                </span>
            </div>
        </div>
        <a href="{{ route('admin.settings.index', ['tab' => 'layout']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-bold text-brand-700 bg-white border border-brand-200 hover:bg-brand-50 transition shrink-0 flex items-center gap-1.5 shadow-2xs">
            <span>Ubah di Pengaturan UI</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- MODAL 1: PILIH DARI KURSUS LMS -->
    <div x-show="courseModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="courseModal = false" 
             x-show="courseModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-3xl rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Pilih Kursus / Materi untuk Banner Slider</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih salah satu kursus yang telah dipublikasikan. Judul, gambar, dan tombol otomatis terkonfigurasi.</p>
                </div>
                <button @click="courseModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Search input -->
            <div class="p-4 bg-slate-50 border-b border-slate-100">
                <div class="relative">
                    <input type="text" x-model="courseSearch" placeholder="Cari judul kursus, kategori, atau instruktur..." 
                           class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Course list items -->
            <div class="p-5 overflow-y-auto space-y-3 divide-y divide-slate-100">
                @forelse($courses as $course)
                    @php
                        $thumb = $course->thumbnail;
                        $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') || str_starts_with($thumb, '/') ? $thumb : asset('storage/' . $thumb)) : null;
                        $isAlreadySlider = in_array($course->id, $existingCourseIds);
                        $matchedSlider = $sliders->firstWhere('course_id', $course->id);
                    @endphp

                    <div class="pt-3 first:pt-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                         x-show="courseSearch === '' || '{{ strtolower(addslashes($course->title . ' ' . ($course->category->name ?? '') . ' ' . ($course->instructor->name ?? ''))) }}'.includes(courseSearch.toLowerCase())">
                        
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-16 h-12 rounded-lg bg-slate-100 shrink-0 overflow-hidden border border-slate-200">
                                @if($thumbUrl)
                                    <img src="{{ $thumbUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-400">No Img</div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-brand-50 text-brand-700">
                                        {{ $course->category->name ?? 'Umum' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">Level: {{ $course->level }} • {{ $course->jp ?? 2 }} JP</span>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-xs truncate mt-0.5">
                                    {{ $course->title }}
                                </h4>
                                <p class="text-[10px] text-slate-500 truncate">
                                    Instruktur: {{ $course->instructor->name ?? 'Fasilitator LMS' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                            @if($isAlreadySlider && $matchedSlider)
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Slide #{{ $matchedSlider->order }}</span>
                                    </span>

                                    <!-- Unselect / Remove from Slider button -->
                                    <form action="{{ route('admin.sliders.unselect-course') }}" method="POST" onsubmit="return confirm('Lepas kursus ini dari slider beranda?')">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                            Unselect / Hapus
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('admin.sliders.add-course') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <button type="submit" 
                                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-slate-900 hover:bg-brand-600 transition shadow-2xs flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Jadikan Slider</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada kursus aktif yang dipublikasikan di LMS.
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button @click="courseModal = false" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: ADD SLIDER MANUAL -->
    <div x-show="addModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="addModal = false" 
             x-show="addModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-xl rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Tambah Banner Slider Manual</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Input custom untuk pengumuman atau banner eksternal.</p>
                </div>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.sliders.store') }}" method="POST" class="p-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Hubungkan ke Kursus (Opsional)</label>
                    <select name="course_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        <option value="">-- Tanpa Relasi Kursus (Manual) --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Utama Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Pelatihan Kepemimpinan ASN Kabupaten Berau 2026"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Lencana (Badge)</label>
                        <input type="text" name="badge_text" placeholder="Contoh: KURIKULUM RESMI NASIONAL"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Urutan Tampil (Order) <span class="text-red-500">*</span></label>
                        <input type="number" name="order" value="{{ $sliders->count() + 1 }}" min="1" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Singkat / Subtitle</label>
                    <textarea name="subtitle" rows="2" placeholder="Uraian singkat 1-2 kalimat..."
                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"></textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">URL Gambar Latar Belakang / Banner</label>
                        <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                            📐 Rekomendasi: 1920 × 800 px (Rasio 2.4:1 Landscape)
                        </span>
                    </div>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau WebP resolusi Full HD (1920×800 px atau 1600×600 px) dengan fokus konten di area tengah/kanan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Tombol Aksi (CTA) <span class="text-red-500">*</span></label>
                        <input type="text" name="cta_text" value="Mulai Belajar Sekarang" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Tombol (URL) <span class="text-red-500">*</span></label>
                        <input type="text" name="cta_link" value="/courses" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>Aktifkan Banner di Beranda</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button @click="addModal = false" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                            Simpan Banner
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: EDIT SLIDER -->
    <div x-show="editModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="editModal = false" 
             x-show="editModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-xl rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Edit Banner Slider</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi atau gambar slider.</p>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/admin/sliders/' + activeSlider.id" method="POST" class="p-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Utama Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="activeSlider.title" required
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Lencana (Badge)</label>
                        <input type="text" name="badge_text" x-model="activeSlider.badge_text"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Urutan Tampil (Order) <span class="text-red-500">*</span></label>
                        <input type="number" name="order" x-model="activeSlider.order" min="1" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Singkat / Subtitle</label>
                    <textarea name="subtitle" x-model="activeSlider.subtitle" rows="2"
                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"></textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">URL Gambar Latar Belakang / Banner</label>
                        <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                            📐 Rekomendasi: 1920 × 800 px (Rasio 2.4:1 Landscape)
                        </span>
                    </div>
                    <input type="url" name="image_url" x-model="activeSlider.image_url"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau WebP resolusi Full HD (1920×800 px atau 1600×600 px).</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Tombol Aksi (CTA) <span class="text-red-500">*</span></label>
                        <input type="text" name="cta_text" x-model="activeSlider.cta_text" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Tombol (URL) <span class="text-red-500">*</span></label>
                        <input type="text" name="cta_link" x-model="activeSlider.cta_link" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="activeSlider.is_active" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>Aktifkan Banner di Beranda</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button @click="editModal = false" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sliders Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sliders as $slider)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between transition hover:shadow-md {{ !$slider->is_active ? 'opacity-70 bg-slate-50/50' : '' }}">
                <div>
                    <!-- Preview Image -->
                    <div class="h-44 w-full bg-slate-100 relative overflow-hidden">
                        @if($slider->image_url)
                            <img src="{{ Str::startsWith($slider->image_url, 'http') ? $slider->image_url : asset(ltrim($slider->image_url, '/')) }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">Tanpa Gambar</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        
                        <div class="absolute top-3 left-3 flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-white/90 backdrop-blur-md text-slate-900 shadow-sm">
                                Slide #{{ $slider->order }}
                            </span>
                            @if($slider->is_active)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-emerald-500 text-white shadow-sm flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-500 text-white shadow-sm">
                                    Unselected (Sembunyi)
                                </span>
                            @endif
                        </div>

                        @if($slider->badge_text)
                            <div class="absolute bottom-3 left-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-brand-500 text-white">
                                    {{ $slider->badge_text }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-5 space-y-2">
                        @if($slider->course)
                            <div class="flex items-center gap-1.5 text-[10px] text-indigo-600 font-bold bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg w-fit">
                                <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <span>Terkoneksi Kursus LMS</span>
                            </div>
                        @endif

                        <h4 class="font-extrabold text-slate-900 text-sm leading-snug line-clamp-2">
                            {{ $slider->title }}
                        </h4>
                        <p class="text-xs text-slate-500 line-clamp-2">
                            {{ $slider->subtitle ?: 'Tanpa deskripsi tambahan.' }}
                        </p>

                        <div class="pt-2 flex items-center gap-2 text-[11px] text-slate-600 font-mono">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold font-sans">{{ $slider->cta_text }}</span>
                            <span class="truncate text-slate-400">&rarr; {{ $slider->cta_link }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions: Toggle / Unselect, Edit, Delete -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/70 flex items-center justify-between gap-2">
                    <!-- Toggle / Unselect Button -->
                    <form action="{{ route('admin.sliders.toggle', $slider->id) }}" method="POST">
                        @csrf
                        @if($slider->is_active)
                            <button type="submit" 
                                    title="Klik untuk menyembunyikan (unselect) banner dari beranda"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white border border-amber-300 text-amber-700 hover:bg-amber-50 transition shadow-2xs flex items-center gap-1.5">
                                <span>👁️‍🗨️</span>
                                <span>Sembunyikan</span>
                            </button>
                        @else
                            <button type="submit" 
                                    title="Klik untuk mengaktifkan kembali banner ke beranda"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-2xs flex items-center gap-1.5">
                                <span>✓</span>
                                <span>Aktifkan</span>
                            </button>
                        @endif
                    </form>

                    <div class="flex items-center gap-2">
                        <button @click="activeSlider = { id: {{ $slider->id }}, title: '{{ addslashes($slider->title) }}', subtitle: '{{ addslashes($slider->subtitle ?? '') }}', badge_text: '{{ addslashes($slider->badge_text ?? '') }}', image_url: '{{ addslashes($slider->image_url ?? '') }}', cta_text: '{{ addslashes($slider->cta_text) }}', cta_link: '{{ addslashes($slider->cta_link) }}', order: {{ $slider->order }}, is_active: {{ $slider->is_active ? 'true' : 'false' }} }; editModal = true"
                                type="button" 
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                            ✏️ Edit
                        </button>

                        <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" onsubmit="return confirm('Hapus banner slider ini secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 bg-white rounded-2xl border border-slate-200 text-center text-xs text-slate-400">
                Belum ada banner slider yang dibuat. Klik tombol <strong>"+ Pilih dari Kursus LMS"</strong> untuk menambahkan materi ke slider!
            </div>
        @endforelse
    </div>

</div>
@endsection
