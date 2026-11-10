@extends('layouts.admin')

@section('page_title', 'Buat Kursus Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Buat Program Kursus Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan judul, instruktur, dan konfigurasi pelatihan sebelum menyusun materi.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 sm:p-8"
         x-data="taxonomyPicker(
             @json(old('category_id', '')),
             @json(old('sub_category_id', '')),
             @json(old('sub_category', '')),
             @json(old('tags', '')),
             @json($mainCategories)
         )">
        <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Course Title -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Judul Kursus Pelatihan <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Pengelolaan Sistem Pemerintahan Berbasis Elektronik (SPBE) Terpadu" 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Subtitle -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Sub-judul / Tagline Singkat</label>
                <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Ringkasan 1 kalimat tentang apa yang akan dicapai peserta..." 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
            </div>

            <!-- TAXONOMY CARD: Kategori, Sub Kategori & Tag / Sub-Sub Kategori (Pencocokan IDP) -->
            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-brand-50 text-brand-700 rounded-lg border border-brand-200/60 text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Taksonomi & Pemetaan IDP ASN</h3>
                            <p class="text-[11px] text-slate-500">Kategori berjenjang sesuai standar katalog kompetensi BKPSDM Kabupaten Berau.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Pencocokan IDP Aktif
                    </span>
                </div>

                <!-- 2 Dropdowns: Kategori Utama & Sub Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            1. Kategori Utama <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" required x-model="selectedMainId" @change="onMainChange()"
                                class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium">
                            <option value="">-- Pilih Kategori Utama --</option>
                            <template x-for="cat in mainCategories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                        <span class="text-[11px] text-slate-400 mt-1 block">Rumpun kompetensi utama aparatur pemerintah.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            2. Sub Kategori <span class="text-slate-400 font-normal">(Cabang Bidang)</span>
                        </label>
                        <select name="sub_category_id" x-model="selectedSubId" @change="onSubChange()"
                                class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium"
                                :disabled="!selectedMainId">
                            <option value="">-- Pilih Sub Kategori --</option>
                            <template x-for="sub in availableSubs" :key="sub.id">
                                <option :value="sub.id" x-text="sub.name"></option>
                            </template>
                        </select>
                        <input type="hidden" name="sub_category" :value="subCategoryText">
                        <span class="text-[11px] text-slate-400 mt-1 block" x-show="selectedMainId">
                            <span x-text="availableSubs.length"></span> sub kategori tersedia untuk rumpun ini.
                        </span>
                        <span class="text-[11px] text-slate-400 mt-1 block" x-show="!selectedMainId">
                            Pilih Kategori Utama terlebih dahulu.
                        </span>
                    </div>
                </div>

                <!-- 3. Tag / Sub-Sub Kategori (Pencocokan IDP) -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            3. Tag / Sub-Sub Kategori <span class="text-brand-600 font-semibold">(Kata Kunci IDP)</span>
                        </label>
                        <span class="text-[11px] text-slate-500 font-mono" x-text="tagsArray.length + ' tag terpilih'"></span>
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="tags" :value="tagsString">

                    <!-- Selected Tag Badges & Live Input Box -->
                    <div class="p-2.5 bg-white border border-slate-200 rounded-xl focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500 transition space-y-2 mb-2">
                        <!-- Pills Container -->
                        <div class="flex flex-wrap items-center gap-1.5 min-h-[32px]">
                            <template x-for="(tag, idx) in tagsArray" :key="idx">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200/80 shadow-2xs group">
                                    <span x-text="tag"></span>
                                    <button type="button" @click="removeTag(idx)" class="text-brand-400 hover:text-rose-600 focus:outline-none transition-colors" title="Hapus tag">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            </template>
                            <span x-show="tagsArray.length === 0 && !tagInput" class="text-xs text-slate-400 italic py-1">
                                Belum ada tag dipilih. Ketik tag manual di bawah atau klik rekomendasi standar.
                            </span>
                        </div>

                        <!-- Multi-Tag Input Row -->
                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                            <div class="relative flex-1">
                                <input type="text" x-model="tagInput"
                                       @keydown.enter.prevent="addCustomTag()"
                                       @input="handleTagInput($event)"
                                       @paste="handleTagPaste($event)"
                                       @blur="addCustomTag()"
                                       placeholder="Ketik tag manual (bisa lebih dari 1, pisahkan dengan koma: Tag 1, Tag 2, Tag 3)..."
                                       class="w-full px-3 py-1.5 text-xs bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-500 text-slate-800 placeholder-slate-400">
                            </div>
                            <button type="button" @click="addCustomTag()"
                                    class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1 flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Tambah Tag</span>
                            </button>
                            <button type="button" x-show="tagsArray.length > 0" @click="clearAllTags()"
                                    class="px-2.5 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-500 rounded-lg text-xs font-medium transition flex-shrink-0" title="Hapus semua tag yang dipilih">
                                Hapus Semua
                            </button>
                        </div>
                    </div>

                    <!-- Helpful Tip for Multi-Tagging -->
                    <p class="text-[11px] text-slate-500 flex items-center gap-1.5 mb-3">
                        <svg class="w-3.5 h-3.5 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><strong>Bisa Tambah Banyak Tag Sekaligus:</strong> Ketik satu atau beberapa kata kunci sekaligus dipisahkan dengan tanda koma (contoh: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-mono">SPBE, Audit TIK, Keamanan Informasi</code>) lalu tekan <strong>Enter</strong> atau <strong>Koma</strong>.</span>
                    </p>

                    <!-- Recommended Tags from Excel Taxonomy -->
                    <div x-show="suggestedTags.length > 0" class="p-3 bg-white/70 rounded-xl border border-slate-200/80">
                        <div class="text-[11px] font-semibold text-slate-600 mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Rekomendasi Tag dari Standar IDP Berau (Klik untuk memilih):</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="recTag in suggestedTags" :key="recTag">
                                <button type="button" @click="toggleTag(recTag)"
                                        :class="hasTag(recTag) ? 'bg-brand-600 text-white border-brand-600 shadow-2xs' : 'bg-slate-50 text-slate-700 hover:bg-brand-50 hover:text-brand-700 border-slate-200'"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-medium border transition-colors flex items-center gap-1">
                                    <span x-text="recTag"></span>
                                    <span x-show="hasTag(recTag)">✓</span>
                                    <span x-show="!hasTag(recTag)" class="text-slate-400">+</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructor Row -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Instruktur / Trainer Pengampu <span class="text-red-500">*</span></label>
                <select name="instructor_id" required class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="">-- Pilih Instruktur --</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                            {{ $instructor->name }} ({{ $instructor->role }})
                        </option>
                    @endforeach
                </select>
                <span class="text-[11px] text-slate-400 mt-1 block">Fasilitator atau trainer per OPD yang bertanggung jawab atas materi pelatihan ini.</span>
            </div>

            <!-- Two Column Row: Format Pelatihan & Kredit JP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Format Pelatihan <span class="text-red-500">*</span></label>
                    <select name="course_type" required class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-semibold">
                        <optgroup label="💻 Format E-Course & Online">
                            <option value="e-course" {{ old('course_type') == 'e-course' ? 'selected' : '' }}>💻 E-Course Mandiri (Video & Modul)</option>
                            <option value="live-webinar" {{ old('course_type') == 'live-webinar' ? 'selected' : '' }}>📹 Live Webinar (Zoom / YouTube Live)</option>
                        </optgroup>
                        <optgroup label="🏛️ Format In-Class & Hybrid (Tatap Muka)">
                            <option value="in-class" {{ old('course_type') == 'in-class' ? 'selected' : '' }}>🏢 Pelatihan Tatap Muka (Dalam Ruangan)</option>
                            <option value="workshop" {{ old('course_type') == 'workshop' ? 'selected' : '' }}>🛠️ Workshop In-Class</option>
                            <option value="bimtek" {{ old('course_type') == 'bimtek' ? 'selected' : '' }}>📑 Bimbingan Teknis (Bimtek)</option>
                            <option value="seminar" {{ old('course_type') == 'seminar' ? 'selected' : '' }}>🎤 Seminar Tatap Muka</option>
                            <option value="hybrid" {{ old('course_type') == 'hybrid' ? 'selected' : '' }}>🌐 Pelatihan Hybrid (Daring & Luring)</option>
                        </optgroup>
                    </select>
                    <span class="text-[11px] text-slate-400">Pilih apakah e-course mandiri, live webinar, atau workshop/in-class tatap muka.</span>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kredit JP ASN / Jam Pelajaran <span class="text-red-500">*</span></label>
                    <input type="number" name="jp" value="{{ old('jp', 4) }}" min="1" required 
                           class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <span class="text-[11px] text-slate-400">Total poin JP yang diakui dan terakumulasi di dashboard & sertifikat ASN.</span>
                </div>
            </div>

            <!-- Three Column Row: Level, Price, Hours -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tingkat Kesulitan <span class="text-red-500">*</span></label>
                    <select name="level" required class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        <option value="Semua Tingkat" {{ old('level') == 'Semua Tingkat' ? 'selected' : '' }}>Semua Tingkat</option>
                        <option value="Pemula" {{ old('level') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                        <option value="Menengah" {{ old('level') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                        <option value="Lanjutan" {{ old('level') == 'Lanjutan' ? 'selected' : '' }}>Lanjutan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Harga Pelatihan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="1000" required 
                           class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                           placeholder="0 untuk gratis">
                    <span class="text-[11px] text-slate-400">Isi 0 jika kursus bersifat gratis</span>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Durasi Belajar (Jam Riil) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_hours" value="{{ old('duration_hours', 4) }}" min="1" required 
                           class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <span class="text-[11px] text-slate-400">Waktu riil belajar menyelesaikan materi (tampil di katalog). Berbeda dengan kredit JP.</span>
                </div>
            </div>

            <!-- Thumbnail URL -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">URL Gambar Thumbnail</label>
                <input type="url" name="thumbnail" value="{{ old('thumbnail') }}" placeholder="https://images.unsplash.com/..." 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                <span class="text-[11px] text-slate-400">Biarkan kosong untuk menggunakan placeholder resolusi tinggi standar.</span>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Deskripsi Lengkap Silabus</label>
                <textarea name="description" rows="4" placeholder="Jelaskan capaian pembelajaran, sasaran peserta, dan prasyarat..." 
                          class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">{{ old('description') }}</textarea>
            </div>

            <!-- Checkboxes -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-6">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="certificate_available" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span>Terbitkan E-Sertifikat Terverifikasi saat 100% Selesai</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span>Tampilkan sebagai Kursus Unggulan di Beranda</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                    <span>Simpan & Lanjut ke Modul Materi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function taxonomyPicker(initialMainId, initialSubId, initialSubText, initialTags, mainCategoriesData) {
    return {
        mainCategories: mainCategoriesData || [],
        selectedMainId: initialMainId ? String(initialMainId) : '',
        selectedSubId: initialSubId ? String(initialSubId) : '',
        subCategoryText: initialSubText || '',
        tagsString: initialTags || '',
        tagInput: '',

        get activeMain() {
            return this.mainCategories.find(c => String(c.id) === String(this.selectedMainId)) || null;
        },
        get availableSubs() {
            return this.activeMain ? (this.activeMain.sub_categories || []) : [];
        },
        get activeSub() {
            return this.availableSubs.find(s => String(s.id) === String(this.selectedSubId)) || null;
        },
        get suggestedTags() {
            if (!this.activeSub || !this.activeSub.tags) return [];
            return this.activeSub.tags.split(',')
                .map(t => t.trim())
                .filter(t => t.length > 0);
        },
        get tagsArray() {
            if (!this.tagsString) return [];
            return this.tagsString.split(',')
                .map(t => t.trim())
                .filter(t => t.length > 0);
        },
        onMainChange() {
            if (this.selectedSubId) {
                const subStillExists = this.availableSubs.some(s => String(s.id) === String(this.selectedSubId));
                if (!subStillExists) {
                    this.selectedSubId = '';
                    this.subCategoryText = '';
                }
            }
        },
        onSubChange() {
            if (this.activeSub) {
                this.subCategoryText = this.activeSub.name;
                if (!this.tagsString && this.activeSub.tags) {
                    this.tagsString = this.activeSub.tags;
                }
            } else {
                this.subCategoryText = '';
            }
        },
        toggleTag(tag) {
            let tags = [...this.tagsArray];
            const idx = tags.findIndex(t => t.toLowerCase() === tag.toLowerCase());
            if (idx >= 0) {
                tags.splice(idx, 1);
            } else {
                tags.push(tag);
            }
            this.tagsString = tags.join(', ');
        },
        hasTag(tag) {
            return this.tagsArray.some(t => t.toLowerCase() === tag.toLowerCase());
        },
        handleTagInput(event) {
            // Jika admin mengetik tanda koma atau titik-koma, langsung tambahkan sebagai tag
            if (this.tagInput.includes(',') || this.tagInput.includes(';')) {
                this.addCustomTag();
            }
        },
        handleTagPaste(event) {
            // Jika admin menempel teks banyak tag dipisahkan koma/newline
            setTimeout(() => {
                this.addCustomTag();
            }, 40);
        },
        addCustomTag() {
            const raw = this.tagInput.trim();
            if (!raw) return;
            // Split berdasarkan tanda koma, titik-koma, atau baris baru
            const newTags = raw.split(/[,;\n]+/).map(t => t.trim()).filter(t => t.length > 0);
            let tags = [...this.tagsArray];
            newTags.forEach(t => {
                if (!tags.some(existing => existing.toLowerCase() === t.toLowerCase())) {
                    tags.push(t);
                }
            });
            this.tagsString = tags.join(', ');
            this.tagInput = '';
        },
        removeTag(idx) {
            let tags = [...this.tagsArray];
            tags.splice(idx, 1);
            this.tagsString = tags.join(', ');
        },
        clearAllTags() {
            this.tagsString = '';
            this.tagInput = '';
        }
    };
}
</script>
@endsection
