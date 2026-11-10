@extends('layouts.admin')

@section('page_title', 'Edit Materi Kursus & Silabus')

@php
    $defaultLayout = [
        'header' => ['x' => 50, 'y' => 14, 'fontFamily' => 'jakarta', 'fontSize' => 11, 'color' => '#0f172a'],
        'title' => ['x' => 50, 'y' => 27, 'fontFamily' => 'cinzel', 'fontSize' => 12, 'color' => '#0f766e'],
        'recipient' => ['x' => 50, 'y' => 48, 'fontFamily' => $course->cert_font_family ?: 'playfair', 'fontSize' => 20, 'color' => $course->cert_name_color ?: '#0f172a'],
        'course' => ['x' => 50, 'y' => 66, 'fontFamily' => 'jakarta', 'fontSize' => 9, 'color' => '#1e293b'],
        'signature' => ['x' => 80, 'y' => 85, 'fontFamily' => 'playfair', 'fontSize' => 10, 'color' => '#0f172a'],
        'seal' => ['x' => 50, 'y' => 85, 'fontFamily' => 'jakarta', 'fontSize' => 6, 'color' => '#78350f'],
        'qr' => ['x' => 20, 'y' => 85, 'fontFamily' => 'jakarta', 'fontSize' => 7, 'color' => '#64748b'],
    ];
    $savedLayout = $defaultLayout;
    if (is_array($course->cert_elements_layout)) {
        foreach ($defaultLayout as $k => $def) {
            if (isset($course->cert_elements_layout[$k]) && is_array($course->cert_elements_layout[$k])) {
                $savedLayout[$k] = array_merge($def, $course->cert_elements_layout[$k]);
            }
        }
    }
    $certShowHeader = $course->cert_show_header ?? true;
@endphp

@section('content')
<script>
function courseEditorStudio() {
    return {
        activeTab: '{{ request('tab', 'info') }}',
        targetType: @json($course->target_audience_type ?? 'umum'),
        certTemplateMode: @json($course->cert_template_mode ?? 'default'),
        certBgPreview: @json($course->cert_bg_image ?? asset('uploads/certificates/blank_certificate_gold_navy.png')),
        certTheme: @json($course->cert_theme_color ?? 'teal'),
        certTitle: @json($course->cert_title ?? 'SERTIFIKAT KOMPETENSI RESMI'),
        certInstitution: @json($course->cert_institution_name ?? 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA'),
        certSub: @json($course->cert_institution_sub ?? 'Pemerintah Kabupaten Berau'),
        signatoryName: @json($course->cert_signatory_name ?? 'Dra. Hj. Sri Eka Yuliana, M.Si'),
        signatoryRole: @json($course->cert_signatory_role ?? 'Kepala BKPSDM Kabupaten Berau'),
        signatoryNip: @json($course->cert_signatory_nip ?? '19720514 199803 2 004'),
        certFontFamily: @json($course->cert_font_family ?? 'playfair'),
        certFontSize: @json($course->cert_font_size ?? 'large'),
        certNameColor: @json($course->cert_name_color ?? '#0f172a'),
        certNameVerticalPos: {{ $course->cert_name_vertical_pos ?? 46 }},
        certShowQr: {{ ($course->cert_show_qr ?? true) ? 'true' : 'false' }},
        certShowNumber: {{ ($course->cert_show_number ?? true) ? 'true' : 'false' }},
        certShowDate: {{ ($course->cert_show_date ?? true) ? 'true' : 'false' }},
        certShowSeal: {{ ($course->cert_show_seal ?? true) ? 'true' : 'false' }},
        certShowJp: {{ ($course->cert_show_jp ?? true) ? 'true' : 'false' }},
        certShowHeader: {{ $certShowHeader ? 'true' : 'false' }},
        certSealText: @json($course->cert_seal_text ?? 'LMS BERAU VALID'),
        certStatement: @json($course->cert_statement ?: 'Telah menyelesaikan seluruh rangkaian silabus, pengujian formatif, dan tugas evaluasi mandiri berstandar nasional.'),
        previewStudentName: 'dr. H. Muhammad Fathurrahman, Sp.A / S.Kom',
        sigPreview: @json($course->cert_signature_image ?? ''),
        certLogoPreview: @json($course->cert_logo_url ?? ''),
        removeCertLogo: false,
        // Taxonomy & IDP Matching State
        mainCategories: @json($mainCategories),
        selectedMainId: @json(old('category_id', $course->category_id ? (string)$course->category_id : '')),
        selectedSubId: @json(old('sub_category_id', $course->sub_category_id ? (string)$course->sub_category_id : '')),
        subCategoryText: @json(old('sub_category', $course->sub_category ?? '')),
        tagsString: @json(old('tags', $course->tags ?? '')),
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
        },

        layout: @json($savedLayout),
        activeDragKey: null,
        selectedElementKey: 'signature',
        dragStartX: 0,
        dragStartY: 0,
        elemStartX: 0,
        elemStartY: 0,
        getFontFamilyStyle(fontKey) {
            const fonts = {
                'playfair': "'Playfair Display', Georgia, serif",
                'great_vibes': "'Great Vibes', cursive",
                'cinzel': "'Cinzel', serif",
                'jakarta': "'Plus Jakarta Sans', sans-serif",
                'montserrat': "'Montserrat', sans-serif",
                'serif': "Georgia, Cambria, 'Times New Roman', serif",
                'sans': "system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif",
                'mono': "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace"
            };
            return fonts[fontKey] || fonts['jakarta'];
        },
        startDrag(key, e) {
            if (e.button !== 0) return;
            this.activeDragKey = key;
            this.selectedElementKey = key;
            this.dragStartX = e.clientX;
            this.dragStartY = e.clientY;
            this.elemStartX = this.layout[key].x;
            this.elemStartY = this.layout[key].y;
        },
        onCanvasMouseMove(e) {
            if (!this.activeDragKey) return;
            const canvas = this.$refs.certCanvas;
            if (!canvas) return;
            const rect = canvas.getBoundingClientRect();
            const deltaX = ((e.clientX - this.dragStartX) / rect.width) * 100;
            const deltaY = ((e.clientY - this.dragStartY) / rect.height) * 100;
            let newX = Math.round(this.elemStartX + deltaX);
            let newY = Math.round(this.elemStartY + deltaY);
            this.layout[this.activeDragKey].x = Math.max(3, Math.min(97, newX));
            this.layout[this.activeDragKey].y = Math.max(3, Math.min(97, newY));
        },
        stopDrag() {
            this.activeDragKey = null;
        },
        resetLayout() {
            this.layout = {
                header: { x: 50, y: 14, fontFamily: 'jakarta', fontSize: 11, color: '#0f172a' },
                title: { x: 50, y: 27, fontFamily: 'cinzel', fontSize: 12, color: '#0f766e' },
                recipient: { x: 50, y: 48, fontFamily: 'playfair', fontSize: 20, color: '#0f172a' },
                course: { x: 50, y: 66, fontFamily: 'jakarta', fontSize: 9, color: '#1e293b' },
                signature: { x: 80, y: 85, fontFamily: 'playfair', fontSize: 10, color: '#0f172a' },
                seal: { x: 50, y: 85, fontFamily: 'jakarta', fontSize: 6, color: '#78350f' },
                qr: { x: 20, y: 85, fontFamily: 'jakarta', fontSize: 7, color: '#64748b' }
            };
        },
        centerElement(key) {
            if (this.layout[key]) {
                this.layout[key].x = 50;
            }
        },
        setSignaturePreset(pos) {
            if (pos === 'right') { this.layout.signature = { x: 82, y: 85 }; }
            if (pos === 'left') { this.layout.signature = { x: 20, y: 85 }; }
            if (pos === 'center') { this.layout.signature = { x: 50, y: 85 }; }
        },
        handleBgFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const self = this;
                const reader = new FileReader();
                reader.onload = function(e) {
                    self.certBgPreview = e.target.result;
                    self.certTemplateMode = 'custom_bg';
                };
                reader.readAsDataURL(file);
            }
        },
        handleSigFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const self = this;
                const reader = new FileReader();
                reader.onload = function(e) {
                    self.sigPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        handleLogoFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const self = this;
                const reader = new FileReader();
                reader.onload = function(e) {
                    self.certLogoPreview = e.target.result;
                    self.removeCertLogo = false;
                };
                reader.readAsDataURL(file);
            }
        },
        deleteLogo() {
            this.certLogoPreview = '';
            this.removeCertLogo = true;
            if (this.$refs.logoFileInput) {
                this.$refs.logoFileInput.value = '';
            }
        }
    };
}
window.courseEditorStudio = courseEditorStudio;
document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('courseEditorStudio', courseEditorStudio);
    }
});
</script>

<div class="space-y-6" x-data="courseEditorStudio()">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.courses.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-900 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Kursus: {{ Str::limit($course->title, 40) }}</h1>
                        @if(($course->approval_status ?? 'approved') === 'pending')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white animate-pulse">Menunggu Persetujuan</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh informasi materi, kurikulum silabus, dan sertifikat kelulusan dalam satu pintu.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Lihat Preview</span>
            </a>
            <button type="submit" form="editCourseForm"
                    class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-semibold flex-wrap">
        <button type="button" @click="activeTab = 'info'"
                :class="activeTab === 'info' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-2">
            <span>📋</span>
            <span>1. Informasi Utama & Target</span>
        </button>

        <button type="button" @click="activeTab = 'syllabus'"
                :class="activeTab === 'syllabus' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-2">
            <span>📚</span>
            <span>2. Silabus & Modul Materi ({{ $course->lessons->count() }})</span>
        </button>

        <button type="button" @click="activeTab = 'certificate'"
                :class="activeTab === 'certificate' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-2">
            <span>🎨</span>
            <span>3. Pengaturan Sertifikat (Builder)</span>
        </button>

        <button type="button" @click="activeTab = 'evaluasi'"
                :class="activeTab === 'evaluasi' ? 'bg-teal-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-2">
            <span>📊</span>
            <span>4. Evaluasi Pembelajaran</span>
        </button>
    </div>

    <!-- TAB 1: INFORMASI UTAMA & TARGET PESERTA -->
    <form id="editCourseForm" action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div x-show="activeTab === 'info'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left: Main Form (8 Cols) -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-5 text-xs">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-bold text-slate-900">Detail Judul, Kategori & Deskripsi</h2>
                            <p class="text-xs text-slate-500">Tentukan identitas materi dan struktur taksonomi pembelajaran.</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Judul Pelatihan / Materi <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" required value="{{ old('title', $course->title) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-bold text-slate-900">
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Subtitle / Ringkasan Singkat</label>
                                <input type="text" name="subtitle" value="{{ old('subtitle', $course->subtitle) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none text-slate-600">
                            </div>

                            <!-- TAXONOMY CARD: Kategori, Sub Kategori & Tag / Sub-Sub Kategori (Pencocokan IDP) -->
                            <div class="p-5 rounded-2xl bg-slate-50/90 border border-slate-200 space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
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
                                        <label class="block font-semibold text-slate-700 mb-1.5">
                                            1. Kategori Utama <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="category_id" required x-model="selectedMainId" @change="onMainChange()"
                                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white font-medium">
                                            <option value="">-- Pilih Kategori Utama --</option>
                                            <template x-for="cat in mainCategories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name" :selected="String(cat.id) === String(selectedMainId)"></option>
                                            </template>
                                        </select>
                                        <span class="text-[11px] text-slate-400 mt-1 block">Rumpun kompetensi aparatur pemerintah.</span>
                                    </div>

                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1.5">
                                            2. Sub Kategori <span class="text-slate-400 font-normal">(Cabang Bidang)</span>
                                        </label>
                                        <select name="sub_category_id" x-model="selectedSubId" @change="onSubChange()"
                                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white font-medium"
                                                :disabled="!selectedMainId">
                                            <option value="">-- Pilih Sub Kategori --</option>
                                            <template x-for="sub in availableSubs" :key="sub.id">
                                                <option :value="sub.id" x-text="sub.name" :selected="String(sub.id) === String(selectedSubId)"></option>
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
                                        <label class="block font-semibold text-slate-700">
                                            3. Tag / Sub-Sub Kategori <span class="text-brand-600 font-normal">(Kata Kunci IDP)</span>
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

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Deskripsi Lengkap Kegiatan</label>
                                <textarea name="description" rows="4"
                                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">{{ old('description', $course->description) }}</textarea>
                            </div>

                            {{-- KAK Poin 4: Tujuan Pembelajaran --}}
                            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-200/80">
                                <label class="block font-bold text-blue-900 mb-1.5 flex items-center gap-1.5">
                                    <span>🎯</span><span>Tujuan Pembelajaran <span class="text-rose-500">*KAK</span></span>
                                </label>
                                <textarea name="learning_objectives" rows="4"
                                          placeholder="Setelah mengikuti pelatihan ini, peserta diharapkan mampu:&#10;1. Memahami konsep dasar...&#10;2. Menerapkan prosedur...&#10;3. Menganalisis..." 
                                          class="w-full px-3.5 py-2.5 rounded-xl border border-blue-200 bg-white text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('learning_objectives', $course->learning_objectives) }}</textarea>
                                <p class="text-[11px] text-blue-700 mt-1">📋 Tuliskan tujuan pembelajaran dalam poin-poin yang terukur (kompetensi yang diharapkan dicapai peserta).</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Jenis Pelatihan & Target Peserta -->
                    <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-5 text-xs">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-bold text-slate-900">Jenis Pelatihan, Sasaran & Bobot JP</h2>
                            <p class="text-xs text-slate-500">Sesuaikan metode penyelenggaraan dan target kepesertaan aparatur/umum.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Jenis Materi <span class="text-rose-500">*</span></label>
                                <select name="course_type" required
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-semibold">
                                    <optgroup label="💻 Format E-Course & Online">
                                        <option value="e-course" {{ in_array($course->course_type ?? 'e-course', ['e-course', 'ecourse']) ? 'selected' : '' }}>📚 E-Course (Asinkron / Mandiri)</option>
                                        <option value="live-webinar" {{ in_array($course->course_type ?? '', ['live-webinar', 'live_webinar']) ? 'selected' : '' }}>📹 Live Webinar (Zoom / YouTube)</option>
                                    </optgroup>
                                    <optgroup label="🏛️ Format In-Class & Hybrid (Tatap Muka)">
                                        <option value="in-class" {{ in_array($course->course_type ?? '', ['in-class', 'in_class']) ? 'selected' : '' }}>🏢 Pelatihan Tatap Muka (Dalam Ruangan)</option>
                                        <option value="workshop" {{ ($course->course_type ?? '') == 'workshop' ? 'selected' : '' }}>🔧 Workshop In-Class</option>
                                        <option value="bimtek" {{ ($course->course_type ?? '') == 'bimtek' ? 'selected' : '' }}>🏫 Bimbingan Teknis (Bimtek)</option>
                                        <option value="seminar" {{ ($course->course_type ?? '') == 'seminar' ? 'selected' : '' }}>🎤 Seminar Tatap Muka</option>
                                        <option value="hybrid" {{ ($course->course_type ?? '') == 'hybrid' ? 'selected' : '' }}>🔀 Hybrid Learning (Daring & Luring)</option>
                                    </optgroup>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Target Peserta <span class="text-rose-500">*</span></label>
                                <select name="target_audience_type" x-model="targetType" required
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-semibold">
                                    <option value="umum">🌐 Untuk Umum</option>
                                    <option value="asn-opd">🏛️ ASN OPD Tertentu</option>
                                    <option value="jabatan-tertentu">👔 Jabatan Tertentu</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Jam Pelajaran (JP) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="jp" min="1" max="120" value="{{ old('jp', $course->jp ?? 2) }}"
                                           class="w-full pl-3.5 pr-12 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-bold">
                                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 font-mono text-xs">JP</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Target Detail -->
                        <div x-show="targetType === 'asn-opd' || targetType === 'jabatan-tertentu'" 
                             x-transition class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5">
                            <label class="block font-semibold text-slate-700" 
                                   x-text="targetType === 'asn-opd' ? 'Spesifikasi OPD / Instansi yang Dituju' : 'Spesifikasi Jabatan ASN / Fungsional'"></label>
                            <input type="text" name="target_audience_detail" value="{{ old('target_audience_detail', $course->target_audience_detail) }}"
                                   placeholder="Contoh: Dinas Kesehatan Provinsi, Administrator Kesehatan Ahli Pertama, dsb."
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 outline-none">
                            <p class="text-[11px] text-slate-400">Peserta dengan kriteria ini akan diprioritaskan atau materi akan disaring khusus untuk kelompok tersebut.</p>
                        </div>

                        {{-- KAK Poin 5: Sasaran Peserta Deskriptif --}}
                        <div class="p-4 bg-indigo-50/60 rounded-2xl border border-indigo-200/80">
                            <label class="block font-bold text-indigo-900 mb-1.5 flex items-center gap-1.5">
                                <span>👥</span><span>Deskripsi Sasaran Peserta <span class="text-rose-500">*KAK</span></span>
                            </label>
                            <textarea name="target_audience_description" rows="3"
                                      placeholder="Contoh: Pelatihan ini ditujukan bagi ASN yang memiliki jabatan fungsional analis kebijakan, perencana, atau tenaga administrasi umum di lingkungan OPD Kabupaten/Kota..."
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-indigo-200 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('target_audience_description', $course->target_audience_description) }}</textarea>
                            <p class="text-[11px] text-indigo-700 mt-1">📋 Deskripsikan secara narasi siapa yang sebaiknya mengikuti kegiatan ini, termasuk prasyarat kompetensi awal.</p>
                        </div>

                        {{-- KAK Poin 8: Metode Pembelajaran --}}
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">🎓 Metode Pembelajaran <span class="text-rose-500">*KAK</span></label>
                            <input type="text" name="learning_method" value="{{ old('learning_method', $course->learning_method) }}"
                                   placeholder="Contoh: Ceramah, Diskusi Kelompok, Studi Kasus, Simulasi Praktik, E-Learning Mandiri"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Pisahkan dengan koma jika menggunakan beberapa metode sekaligus.</p>
                        </div>
                    </div>

                    {{-- KAK Poin 7: Jadwal / Schedule --}}
                    <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-5 text-xs">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">📅 Jadwal Pelaksanaan <span class="text-rose-500 text-xs font-normal">*KAK</span></h2>
                            <p class="text-xs text-slate-500">
                                Untuk e-course: <strong>tanggal rilis</strong> menentukan kapan materi dapat diakses. Untuk webinar/tatap muka: tanggal pelaksanaan kegiatan.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">📅 Tanggal Mulai / Rilis</label>
                                <input type="date" name="start_date" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-mono">
                                <p class="text-[11px] text-slate-400 mt-1">E-course: tanggal rilis (peserta baru bisa akses). Webinar/Tatap Muka: hari-H kegiatan.</p>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">🏁 Tanggal Selesai / Batas Akses</label>
                                <input type="date" name="end_date" value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-mono">
                                <p class="text-[11px] text-slate-400 mt-1">Tanggal terakhir peserta dapat mengakses atau menyelesaikan kursus.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">⏰ Waktu Pelaksanaan</label>
                                <input type="time" name="schedule_time" value="{{ old('schedule_time', $course->schedule_time) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-mono">
                                <p class="text-[11px] text-slate-400 mt-1">Waktu mulai kegiatan (khusus webinar/tatap muka/bimtek).</p>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">📋 Batas Pendaftaran</label>
                                <input type="date" name="registration_deadline" value="{{ old('registration_deadline', $course->registration_deadline?->format('Y-m-d')) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none font-mono">
                                <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong jika pendaftaran dibuka bebas.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">📍 Lokasi / Tautan Platform</label>
                            <input type="text" name="schedule_location" value="{{ old('schedule_location', $course->schedule_location) }}"
                                   placeholder="Contoh: Aula Utama Badan Kepegawaian Daerah / https://zoom.us/j/1234567890"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Untuk webinar: masukkan link Zoom/GMeet. Untuk tatap muka: alamat/nama gedung lengkap.</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Meta, Price, Thumbnail & Approval (4 Cols) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Card Status & Approval -->
                    <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                        <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2">Status & Publikasi</h3>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Status Persetujuan (Approval)</label>
                            <select name="approval_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                <option value="approved" {{ ($course->approval_status ?? 'approved') == 'approved' ? 'selected' : '' }}>✅ Disetujui (Approved)</option>
                                <option value="pending" {{ ($course->approval_status ?? '') == 'pending' ? 'selected' : '' }}>⏳ Pending Review (Menunggu)</option>
                                <option value="rejected" {{ ($course->approval_status ?? '') == 'rejected' ? 'selected' : '' }}>❌ Ditolak (Rejected)</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div>
                                <p class="font-bold text-slate-900">Publikasikan Materi</p>
                                <p class="text-[11px] text-slate-400">Tampil di katalog frontend</p>
                            </div>
                            <input type="checkbox" name="is_published" value="1" {{ $course->is_published ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div>
                                <p class="font-bold text-slate-900">Kursus Unggulan</p>
                                <p class="text-[11px] text-slate-400">Tampil di hero slider beranda</p>
                            </div>
                            <input type="checkbox" name="is_featured" value="1" {{ $course->is_featured ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        </div>
                    </div>

                    <!-- Card Instructor, Level & Price -->
                    <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                        <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2">Instruktur & Biaya</h3>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Instruktur / Pemateri</label>
                            <select name="instructor_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                @foreach($instructors as $inst)
                                    <option value="{{ $inst->id }}" {{ $course->instructor_id == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }} ({{ $inst->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tingkat Kesulitan</label>
                            <select name="level" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                <option value="Pemula" {{ $course->level == 'Pemula' ? 'selected' : '' }}>Level 1: Pemula</option>
                                <option value="Menengah" {{ $course->level == 'Menengah' ? 'selected' : '' }}>Level 2: Menengah</option>
                                <option value="Lanjutan" {{ $course->level == 'Lanjutan' ? 'selected' : '' }}>Level 3: Mahir / Lanjutan</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Investasi (Rp)</label>
                                <input type="number" name="price" value="{{ $course->price }}" min="0" step="1000"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                <span class="text-[10px] text-slate-400">0 = Gratis</span>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Durasi Belajar (Jam Riil) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="duration_hours" value="{{ $course->duration_hours }}" min="1"
                                           class="w-full pl-3.5 pr-12 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 font-mono text-[11px]">Jam</span>
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-1 leading-tight">Waktu riil menyelesaikan materi (tampil di katalog). Berbeda dengan kredit JP.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Thumbnail -->
                    <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                        <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2">Sampul / Thumbnail</h3>
                        
                        @if($course->thumbnail)
                            <div class="rounded-xl overflow-hidden aspect-video bg-slate-100 border border-slate-200">
                                <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover" alt="Thumbnail">
                            </div>
                        @endif

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">URL Gambar Sampul</label>
                            <input type="text" name="thumbnail" value="{{ $course->thumbnail }}"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold text-slate-700">Atau Unggah File Baru</label>
                                <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                    📐 Rekomendasi: 1280 × 720 px (Rasio 16:9 Landscape)
                                </span>
                            </div>
                            <input type="file" name="thumbnail_file" accept="image/*"
                                   class="w-full text-[11px] text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 hover:file:bg-slate-200">
                            <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau WebP (Maks. 2 MB). Tampil sebagai sampul kursus di katalog dan ruang belajar.</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- TAB 3: PENGATURAN SERTIFIKAT BUILDER (ADVANCED & PREMIUM) -->
        <div x-show="activeTab === 'certificate'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Certificate Form Controls (6 Cols) -->
                <div class="lg:col-span-6 space-y-6 p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs text-xs">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Certificate Builder Studio</h2>
                            <p class="text-xs text-slate-500">Desain piagam resmi, upload template background kosongan, dan kelola atribut otomatis.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                            Studio Pro
                        </span>
                    </div>

                    <!-- Activation Toggle -->
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                        <div>
                            <p class="font-bold text-slate-900">Terbitkan E-Sertifikat Digital Otomatis</p>
                            <p class="text-[11px] text-slate-500">Sertifikat terbit otomatis saat siswa lulus seluruh modul & kuis 100%</p>
                        </div>
                        <input type="checkbox" name="certificate_available" value="1" {{ $course->certificate_available ? 'checked' : '' }}
                               class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    </div>

                    <!-- 1. Mode Desain Sertifikat -->
                    <div>
                        <label class="block font-bold text-slate-900 uppercase tracking-wider text-[11px] mb-2">
                            Mode Desain Sertifikat <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex flex-col p-3 rounded-2xl border cursor-pointer transition text-left relative"
                                   :class="certTemplateMode === 'default' ? 'bg-teal-50/70 border-teal-500 ring-2 ring-teal-500/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/60'">
                                <input type="radio" name="cert_template_mode" value="default" x-model="certTemplateMode" class="sr-only">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-lg">🏛️</span>
                                    <span class="font-bold text-slate-900 text-xs">Bingkai Klasik Vektor</span>
                                </div>
                                <span class="text-[10px] text-slate-500 leading-snug">Bingkai ganda ornamen resmi dengan varian tema warna modern.</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-2xl border cursor-pointer transition text-left relative"
                                   :class="certTemplateMode === 'custom_bg' ? 'bg-teal-50/70 border-teal-500 ring-2 ring-teal-500/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/60'">
                                <input type="radio" name="cert_template_mode" value="custom_bg" x-model="certTemplateMode" class="sr-only">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-lg">🖼️</span>
                                    <span class="font-bold text-slate-900 text-xs">Background Kosongan</span>
                                </div>
                                <span class="text-[10px] text-slate-500 leading-snug">Upload desain piagam Anda dari Canva/Photoshop, teks di-generate otomatis.</span>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Sub-Settings: Custom Background Image Upload & Calibration -->
                    <div x-show="certTemplateMode === 'custom_bg'" x-transition class="p-4 bg-amber-50/60 border border-amber-200/80 rounded-2xl space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                <span>🎨</span>
                                <span>Upload Template Piagam Kosongan</span>
                            </h3>
                            <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">📐 Rekomendasi: 2970 × 2100 px (A4 Landscape 300 DPI, min. 1920×1357 px)</span>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Berkas Gambar (JPG / PNG / WebP)</label>
                            <input type="file" name="cert_bg_image_file" accept="image/png,image/jpeg,image/webp"
                                   @change="handleBgFileSelect($event)"
                                   class="w-full px-3 py-2 text-xs bg-white rounded-xl border border-slate-200 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-brand-700 cursor-pointer">
                            <input type="hidden" name="cert_bg_image" :value="certBgPreview">
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="certBgPreview = '{{ asset('uploads/certificates/blank_certificate_gold_navy.png') }}'"
                                    class="px-3 py-1.5 rounded-lg bg-white border border-amber-300 text-amber-900 hover:bg-amber-100/50 text-[11px] font-semibold transition">
                                ✨ Gunakan Template Royal Gold & Navy Bawaan
                            </button>
                        </div>
                    </div>

                    <!-- Hidden sync for legacy fields from Studio layout.recipient -->
                    <input type="hidden" name="cert_font_family" :value="layout.recipient.fontFamily">
                    <input type="hidden" name="cert_name_color" :value="layout.recipient.color">
                    <input type="hidden" name="cert_name_vertical_pos" :value="layout.recipient.y">

                    <!-- 3. Sub-Settings: Tema Aksen Warna (Khusus Bingkai Default) -->
                    <div x-show="certTemplateMode === 'default'" x-transition class="space-y-2">
                        <label class="block font-bold text-slate-800 text-[11px] uppercase tracking-wider">Aksen Warna Bingkai Sertifikat</label>
                        <select name="cert_theme_color" x-model="certTheme" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold">
                            <option value="teal">Teal (Standar Nasional / Lembaga Resmi)</option>
                            <option value="indigo">Indigo (Akademi Digital Modern)</option>
                            <option value="blue">Blue (Aparatur Pemerintahan & BKN)</option>
                            <option value="emerald">Emerald (Kesehatan Digital & Sertifikasi)</option>
                            <option value="slate">Slate (Enterprise Platinum Klasik)</option>
                        </select>
                    </div>

                    <!-- 4. Identitas & Kop Surat Lembaga -->
                    <div class="space-y-3 pt-1">
                        <h3 class="font-bold text-slate-900 text-xs flex items-center gap-1.5 border-t border-slate-100 pt-3">
                            <span>🏛️</span>
                            <span>Kop & Identitas Lembaga Penerbit</span>
                        </h3>

                        <!-- Pengaturan Logo Kop Lembaga (Upload / Replace / Delete) -->
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label class="font-bold text-slate-800 text-[11px] flex items-center gap-1.5">
                                    <span>🖼️</span>
                                    <span>Logo Kop Lembaga (PNG / JPG / SVG / WebP)</span>
                                </label>
                                <span class="text-[10px] text-slate-500">Maks. 3 MB (Transparan Disarankan)</span>
                            </div>

                            <input type="hidden" name="remove_cert_logo" :value="removeCertLogo ? '1' : '0'">

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <!-- Thumbnail Preview -->
                                <div class="w-16 h-16 rounded-xl border-2 border-dashed border-slate-200 bg-white flex items-center justify-center overflow-hidden shrink-0 shadow-2xs">
                                    <template x-if="certLogoPreview">
                                        <img :src="certLogoPreview" class="w-full h-full object-contain p-1" alt="Logo Lembaga">
                                    </template>
                                    <template x-if="!certLogoPreview">
                                        <div class="text-center p-1">
                                            <span class="text-base block">🏛️</span>
                                            <span class="text-[8px] text-slate-400 font-bold leading-tight">Default OL</span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex-1 space-y-1.5">
                                    <input type="file" name="cert_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="hidden" x-ref="logoFileInput" @change="handleLogoFileSelect">
                                    
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button" @click="$refs.logoFileInput.click()"
                                                class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            <span x-text="certLogoPreview ? 'Ganti / Replace Logo' : 'Unggah Logo Lembaga'"></span>
                                        </button>

                                        <button type="button" x-show="certLogoPreview" @click="deleteLogo()"
                                                class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs border border-rose-200 transition flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus Logo</span>
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-slate-500">Logo ini akan otomatis tampil pada kop sertifikat menggantikan monogram default 'OL'.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Judul Piagam / Sertifikat <span class="text-rose-500">*</span></label>
                            <input type="text" name="cert_title" x-model="certTitle" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold"
                                   value="{{ $course->cert_title ?? 'SERTIFIKAT KOMPETENSI RESMI' }}">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nama Lembaga Penyelenggara <span class="text-rose-500">*</span></label>
                                <input type="text" name="cert_institution_name" x-model="certInstitution" required
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold"
                                       value="{{ $course->cert_institution_name ?? 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA' }}">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Sub Unit / Keterangan Lembaga</label>
                                <input type="text" name="cert_institution_sub" x-model="certSub"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs"
                                       value="{{ $course->cert_institution_sub ?? 'Pemerintah Kabupaten Berau' }}">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Redaksi Pernyataan Kompetensi</label>
                            <textarea name="cert_statement" rows="2"
                                      class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">{{ $course->cert_statement ?? 'Diberikan sebagai bukti kelulusan dan penguasaan standar kompetensi teknis yang dipersyaratkan.' }}</textarea>
                        </div>
                    </div>

                    <!-- 6. Penandatangan & Tanda Tangan Basah -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                        <h3 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span>🖋️</span>
                            <span>Pejabat Penandatangan Resmi</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nama Pejabat & Gelar</label>
                                <input type="text" name="cert_signatory_name" x-model="signatoryName"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold"
                                       value="{{ $course->cert_signatory_name ?? 'Dra. Hj. Sri Eka Yuliana, M.Si' }}">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Jabatan Resmi</label>
                                <input type="text" name="cert_signatory_role" x-model="signatoryRole"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"
                                       value="{{ $course->cert_signatory_role ?? 'Kepala BKPSDM Kabupaten Berau' }}">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">NIP / Nomor SK</label>
                                <input type="text" name="cert_signatory_nip" x-model="signatoryNip"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono"
                                       value="{{ $course->cert_signatory_nip ?? '19720514 199803 2 004' }}">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold text-slate-700">Unggah Tanda Tangan Basah Transparan (Opsional)</label>
                                <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                    📐 Rekomendasi: 400 × 200 px (Rasio 2:1 PNG Transparan)
                                </span>
                            </div>
                            <input type="file" name="cert_signature_file" accept="image/png,image/webp"
                                   @change="handleSigFileSelect($event)"
                                   class="w-full px-3 py-1.5 text-xs bg-white rounded-xl border border-slate-200 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-800 file:text-white cursor-pointer">
                            <input type="hidden" name="cert_signature_image" :value="sigPreview">
                            <p class="text-[10px] text-slate-400 mt-1">Disarankan format PNG latar belakang transparan (resolusi 400×200 px) agar menyatu dengan sertifikat tanpa blok putih.</p>
                        </div>
                    </div>

                    <!-- 7. Fitur Sakelar Otomatisasi (Toggles) -->
                    <div class="pt-2 border-t border-slate-100 space-y-2.5">
                        <label class="block font-bold text-slate-900 uppercase tracking-wider text-[11px]">
                            Opsi Tampilan Otomatis Dokumen
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-100/50">
                                <input type="checkbox" name="cert_show_number" value="1" x-model="certShowNumber" class="rounded text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-[11px]">Nomor Registrasi Otomatis</span>
                            </label>

                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-100/50">
                                <input type="checkbox" name="cert_show_date" value="1" x-model="certShowDate" class="rounded text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-[11px]">Tanggal Terbit Otomatis</span>
                            </label>

                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-100/50">
                                <input type="checkbox" name="cert_show_jp" value="1" x-model="certShowJp" class="rounded text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-[11px]">Cantumkan Jam Pelajaran (JP)</span>
                            </label>

                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-100/50">
                                <input type="checkbox" name="cert_show_qr" value="1" x-model="certShowQr" class="rounded text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-[11px]">QR Code Verifikasi Online</span>
                            </label>

                            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-100/50 sm:col-span-2">
                                <input type="checkbox" name="cert_show_seal" value="1" x-model="certShowSeal" class="rounded text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-[11px]">Cap Stempel Digital Resmi (Digital Verified Seal)</span>
                            </label>
                        </div>
                    </div>

                    <!-- 8. FREE DRAG & MOVE STUDIO (PENGATUR POSISI BEBAS) -->
                    <div class="p-4 bg-indigo-50/60 rounded-2xl border border-indigo-200/80 space-y-3.5">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                <span>📍</span>
                                <span>Free Drag & Move Studio (Posisi Elemen Bebas)</span>
                            </h3>
                            <button type="button" @click="resetLayout()" class="text-[10px] text-indigo-700 hover:text-indigo-900 font-bold underline flex items-center gap-1">
                                <span>🔄 Reset Posisi Bawaan</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Posisikan tanda tangan, nama penerima, judul, stempel, atau kop lembaga secara <strong>bebas dan fleksibel</strong>. Klik & seret (drag) langsung pada kanvas pratinjau di sebelah kanan, atau sesuaikan koordinat persentase (X%, Y%) di bawah ini:
                        </p>

                        <!-- Header Toggle -->
                        <div class="p-2.5 bg-white rounded-xl border border-indigo-200 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900 text-[11px]">Tampilkan Kop Lembaga & Logo</p>
                                <p class="text-[10px] text-slate-500">Nonaktifkan jika desain background template Anda sudah memiliki kop surat sendiri.</p>
                            </div>
                            <input type="checkbox" name="cert_show_header" value="1" x-model="certShowHeader" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                        </div>

                        <!-- Element Selector Pills -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 text-[10px] uppercase tracking-wider">Pilih Elemen Yang Ingin Diatur:</label>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-1.5 text-[10px] font-semibold">
                                <button type="button" @click="selectedElementKey = 'signature'"
                                        :class="selectedElementKey === 'signature' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-indigo-100/50 border border-indigo-200'"
                                        class="p-1.5 rounded-lg transition text-center truncate">
                                    🖋️ Ttd & Pejabat
                                </button>
                                <button type="button" @click="selectedElementKey = 'recipient'"
                                        :class="selectedElementKey === 'recipient' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-indigo-100/50 border border-indigo-200'"
                                        class="p-1.5 rounded-lg transition text-center truncate">
                                    👑 Nama Peserta
                                </button>
                                <button type="button" @click="selectedElementKey = 'title'"
                                        :class="selectedElementKey === 'title' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-indigo-100/50 border border-indigo-200'"
                                        class="p-1.5 rounded-lg transition text-center truncate">
                                    📜 Judul Dokumen
                                </button>
                                <button type="button" @click="selectedElementKey = 'header'"
                                        :class="selectedElementKey === 'header' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-indigo-100/50 border border-indigo-200'"
                                        class="p-1.5 rounded-lg transition text-center truncate">
                                    🏢 Kop Lembaga
                                </button>
                                <button type="button" @click="selectedElementKey = 'course'"
                                        :class="selectedElementKey === 'course' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-indigo-100/50 border border-indigo-200'"
                                        class="p-1.5 rounded-lg transition text-center truncate">
                                    📚 Info Kursus
                                </button>
                                <button type="button" @click="selectedElementKey = 'seal'"
                                        :class="selectedElementKey === 'seal' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-indigo-100/50 border border-indigo-200'"
                                        class="p-1.5 rounded-lg transition text-center truncate">
                                    🏅 Cap Stempel
                                </button>
                                <button type="button" @click="selectedElementKey = 'qr'"
                                        :class="selectedElementKey === 'qr' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-indigo-100/50 border border-indigo-200'"
                                        class="p-1.5 rounded-lg transition text-center truncate">
                                    📱 QR Verifikasi
                                </button>
                            </div>
                        </div>

                        <!-- Active Element Position Sliders -->
                        <div class="p-3 bg-white rounded-xl border border-indigo-200 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-[11px] uppercase tracking-wide">
                                    Koordinat: <span class="text-indigo-600" x-text="selectedElementKey.toUpperCase()"></span>
                                </span>
                                <button type="button" @click="centerElement(selectedElementKey)"
                                        class="px-2 py-0.5 rounded bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-semibold hover:bg-indigo-100">
                                    Tengahkan Horizontal (X: 50%)
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-[10px] font-semibold text-slate-600">Posisi Horizontal (X)</label>
                                        <span class="text-[10px] font-mono font-bold text-indigo-700" x-text="layout[selectedElementKey].x + '%'"></span>
                                    </div>
                                    <input type="range" min="5" max="95" step="1" x-model="layout[selectedElementKey].x"
                                           class="w-full accent-indigo-600 cursor-pointer">
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-[10px] font-semibold text-slate-600">Posisi Vertikal (Y)</label>
                                        <span class="text-[10px] font-mono font-bold text-indigo-700" x-text="layout[selectedElementKey].y + '%'"></span>
                                    </div>
                                    <input type="range" min="5" max="95" step="1" x-model="layout[selectedElementKey].y"
                                           class="w-full accent-indigo-600 cursor-pointer">
                                </div>
                            </div>

                            <!-- Preset Tanda Tangan Khusus -->
                            <template x-if="selectedElementKey === 'signature'">
                                <div class="pt-2 border-t border-slate-100">
                                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">Preset Cepat Posisi Tanda Tangan:</label>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="setSignaturePreset('right')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-medium text-slate-700">
                                            Kanan Bawah (Standar)
                                        </button>
                                        <button type="button" @click="setSignaturePreset('center')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-medium text-slate-700">
                                            Tengah Bawah
                                        </button>
                                        <button type="button" @click="setSignaturePreset('left')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-medium text-slate-700">
                                            Kiri Bawah
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Pengaturan Tipografi Elemen Terpilih (Jenis Font, Ukuran Font, Warna Font) -->
                            <div class="pt-3 border-t border-slate-100 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800 text-[11px] flex items-center gap-1.5">
                                        <span>🎨</span>
                                        <span>Format Huruf & Warna: <span class="text-indigo-600 font-mono" x-text="selectedElementKey.toUpperCase()"></span></span>
                                    </span>
                                    <span class="text-[9px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        Live Preview
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <!-- 1. Jenis Font (Font Family) -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Jenis Font</label>
                                        <select x-model="layout[selectedElementKey].fontFamily"
                                                @change="if (selectedElementKey === 'recipient') certFontFamily = layout.recipient.fontFamily"
                                                class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-white text-slate-800 focus:ring-1 focus:ring-indigo-500">
                                            <option value="playfair">Playfair Display (Serif Elegan)</option>
                                            <option value="great_vibes">Great Vibes (Kaligrafi Mewah)</option>
                                            <option value="cinzel">Cinzel (Imperial Klasik)</option>
                                            <option value="jakarta">Plus Jakarta Sans (Modern Clean)</option>
                                            <option value="montserrat">Montserrat (Modern Geometric)</option>
                                            <option value="serif">Serif Tradisional (Baskerville)</option>
                                            <option value="sans">Sans-Serif Bersih (Inter/Arial)</option>
                                            <option value="mono">Monospace (Ketik / Resmi)</option>
                                        </select>
                                    </div>

                                    <!-- 2. Ukuran Font (Font Size) -->
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-[10px] font-semibold text-slate-600">Ukuran Font</label>
                                            <span class="text-[10px] font-mono font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded" x-text="layout[selectedElementKey].fontSize + 'px'"></span>
                                        </div>
                                        <input type="range" min="6" max="44" step="1" x-model="layout[selectedElementKey].fontSize"
                                               class="w-full accent-indigo-600 cursor-pointer">
                                    </div>

                                    <!-- 3. Warna Font (Font Color) -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Warna Font</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="color" x-model="layout[selectedElementKey].color"
                                                   @input="if (selectedElementKey === 'recipient') certNameColor = layout.recipient.color"
                                                   class="w-7 h-7 rounded border border-slate-200 p-0 cursor-pointer flex-shrink-0">
                                            <input type="text" x-model="layout[selectedElementKey].color"
                                                   @input="if (selectedElementKey === 'recipient') certNameColor = layout.recipient.color"
                                                   class="w-full px-2 py-1 rounded-lg border border-slate-200 text-xs font-mono uppercase text-slate-800">
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Color Palette Swatches -->
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 pt-0.5">
                                    <span class="text-[9px] font-semibold">Palet Warna Cepat:</span>
                                    <button type="button" @click="layout[selectedElementKey].color = '#0f172a'; if(selectedElementKey==='recipient') certNameColor='#0f172a'" class="w-4 h-4 rounded-full bg-[#0f172a] border border-slate-300 shadow-2xs hover:scale-125 transition" title="Dark Slate (#0f172a)"></button>
                                    <button type="button" @click="layout[selectedElementKey].color = '#1e3a8a'; if(selectedElementKey==='recipient') certNameColor='#1e3a8a'" class="w-4 h-4 rounded-full bg-[#1e3a8a] border border-slate-300 shadow-2xs hover:scale-125 transition" title="Navy Blue (#1e3a8a)"></button>
                                    <button type="button" @click="layout[selectedElementKey].color = '#047857'; if(selectedElementKey==='recipient') certNameColor='#047857'" class="w-4 h-4 rounded-full bg-[#047857] border border-slate-300 shadow-2xs hover:scale-125 transition" title="Emerald Green (#047857)"></button>
                                    <button type="button" @click="layout[selectedElementKey].color = '#b45309'; if(selectedElementKey==='recipient') certNameColor='#b45309'" class="w-4 h-4 rounded-full bg-[#b45309] border border-slate-300 shadow-2xs hover:scale-125 transition" title="Warm Gold (#b45309)"></button>
                                    <button type="button" @click="layout[selectedElementKey].color = '#831843'; if(selectedElementKey==='recipient') certNameColor='#831843'" class="w-4 h-4 rounded-full bg-[#831843] border border-slate-300 shadow-2xs hover:scale-125 transition" title="Burgundy (#831843)"></button>
                                    <button type="button" @click="layout[selectedElementKey].color = '#000000'; if(selectedElementKey==='recipient') certNameColor='#000000'" class="w-4 h-4 rounded-full bg-black border border-slate-300 shadow-2xs hover:scale-125 transition" title="Pitch Black (#000000)"></button>
                                    <button type="button" @click="layout[selectedElementKey].color = '#ffffff'; if(selectedElementKey==='recipient') certNameColor='#ffffff'" class="w-4 h-4 rounded-full bg-white border border-slate-400 shadow-2xs hover:scale-125 transition" title="White (#ffffff)"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields to serialize layout -->
                        <input type="hidden" name="cert_elements_layout" :value="JSON.stringify(layout)">
                    </div>

                </div>

                <!-- Right: Live Visual Certificate Canvas (6 Cols) -->
                <div class="lg:col-span-6 sticky top-6 space-y-4">
                    
                    <!-- Canvas Top Controls & Interactive Name Simulator -->
                    <div class="p-4 bg-slate-900 text-white rounded-3xl shadow-xl space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="font-bold text-white tracking-tight">Interactive Live Canvas Studio</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-950 text-emerald-300 border border-emerald-800">
                                    Free Drag Active ✥
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Simulasi Nama Peserta (Pratinjau Langsung):</label>
                            <input type="text" x-model="previewStudentName"
                                   placeholder="Ketik nama peserta simulasi..."
                                   class="w-full px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-semibold focus:ring-2 focus:ring-brand-500 outline-none">
                        </div>

                        <!-- Helper Drag Hint Badge -->
                        <div class="py-1 px-2.5 rounded-lg bg-indigo-950/60 border border-indigo-700/50 flex items-center justify-between text-[10px] text-indigo-200">
                            <span class="flex items-center gap-1.5">
                                <span>💡</span>
                                <span><strong>Tips:</strong> Klik & seret (drag) langsung blok tanda tangan atau teks pada piagam di bawah.</span>
                            </span>
                            <button type="button" @click="resetLayout()" class="underline text-indigo-300 hover:text-white font-bold">
                                Reset
                            </button>
                        </div>

                        <!-- LIVE CERTIFICATE CANVAS FRAME -->
                        <div x-ref="certCanvas"
                             @mousemove="onCanvasMouseMove($event)"
                             @mouseup.window="stopDrag()"
                             @mouseleave="stopDrag()"
                             class="relative w-full rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 select-none cursor-default"
                             style="aspect-ratio: 1.414 / 1;"
                             :class="{
                                 'bg-white text-slate-900': certTemplateMode === 'default',
                                 'border-8 border-double border-teal-700/80': certTemplateMode === 'default' && certTheme === 'teal',
                                 'border-8 border-double border-indigo-700/80': certTemplateMode === 'default' && certTheme === 'indigo',
                                 'border-8 border-double border-blue-700/80': certTemplateMode === 'default' && certTheme === 'blue',
                                 'border-8 border-double border-emerald-700/80': certTemplateMode === 'default' && certTheme === 'emerald',
                                 'border-8 border-double border-slate-800/80': certTemplateMode === 'default' && certTheme === 'slate',
                             }">

                            <!-- Custom Background Image Layer -->
                            <template x-if="certTemplateMode === 'custom_bg'">
                                <div class="absolute inset-0 w-full h-full bg-cover bg-center pointer-events-none"
                                     :style="'background-image: url(' + certBgPreview + '); background-size: 100% 100%;'">
                                </div>
                            </template>

                            <!-- Default Vector Ornaments Layer -->
                            <template x-if="certTemplateMode === 'default'">
                                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(240,253,250,0.6),transparent_70%)] pointer-events-none"></div>
                            </template>

                            <!-- CONTENT OVERLAY CONTAINER (RELATIVE CONTAINER FOR ABSOLUTE DRAGGABLE BLOCKS) -->
                            <div class="relative z-10 w-full h-full"
                                 :class="certTemplateMode === 'custom_bg' ? 'bg-black/5' : ''">

                                <!-- 1. Kop Lembaga & Logo (Draggable) -->
                                <template x-if="certShowHeader">
                                    <div @mousedown.prevent="startDrag('header', $event)"
                                         @click.stop="selectedElementKey = 'header'"
                                         :style="'left: ' + layout.header.x + '%; top: ' + layout.header.y + '%; transform: translate(-50%, -50%); width: 85%; max-width: 480px;'"
                                         class="absolute z-20 select-none group cursor-grab active:cursor-grabbing p-1 rounded-xl transition-all border border-transparent"
                                         :class="{
                                             'ring-2 ring-indigo-500 bg-indigo-500/15 shadow-lg border-indigo-400': activeDragKey === 'header' || selectedElementKey === 'header',
                                             'hover:border-dashed hover:border-indigo-400 hover:bg-slate-900/5': activeDragKey !== 'header' && selectedElementKey !== 'header'
                                         }">
                                        <!-- Drag Handle Badge -->
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-[8px] font-bold text-white shadow pointer-events-none whitespace-nowrap z-30"
                                             :class="{'opacity-100': activeDragKey === 'header' || selectedElementKey === 'header'}">
                                            ✥ Kop Lembaga (X:<span x-text="layout.header.x"></span>% Y:<span x-text="layout.header.y"></span>%)
                                        </div>

                                        <div class="flex items-center justify-center gap-2">
                                            <template x-if="certLogoPreview">
                                                <img :src="certLogoPreview" class="h-8 max-w-[120px] object-contain rounded drop-shadow-xs" alt="Logo Lembaga">
                                            </template>
                                            <template x-if="!certLogoPreview">
                                                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-slate-900 to-teal-600 text-white font-extrabold text-xs flex items-center justify-center shadow-xs">
                                                    OL
                                                </div>
                                            </template>
                                            <div class="text-left">
                                                <h3 class="font-extrabold uppercase leading-tight tracking-tight"
                                                    :style="'font-family: ' + getFontFamilyStyle(layout.header.fontFamily) + '; font-size: ' + layout.header.fontSize + 'px; color: ' + layout.header.color + ';'"
                                                    x-text="certInstitution"></h3>
                                                <p class="uppercase tracking-widest font-semibold"
                                                   :style="'font-family: ' + getFontFamilyStyle(layout.header.fontFamily) + '; font-size: ' + Math.max(6, Math.round(layout.header.fontSize * 0.72)) + 'px; color: ' + layout.header.color + '; opacity: 0.75;'"
                                                   x-text="certSub"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- 2. Judul Dokumen & Nomor Registrasi (Draggable) -->
                                <div @mousedown.prevent="startDrag('title', $event)"
                                     @click.stop="selectedElementKey = 'title'"
                                     :style="'left: ' + layout.title.x + '%; top: ' + layout.title.y + '%; transform: translate(-50%, -50%); width: 85%; max-width: 480px;'"
                                     class="absolute z-20 select-none group cursor-grab active:cursor-grabbing p-1 rounded-xl transition-all text-center border border-transparent"
                                     :class="{
                                         'ring-2 ring-indigo-500 bg-indigo-500/15 shadow-lg border-indigo-400': activeDragKey === 'title' || selectedElementKey === 'title',
                                         'hover:border-dashed hover:border-indigo-400 hover:bg-slate-900/5': activeDragKey !== 'title' && selectedElementKey !== 'title'
                                     }">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-[8px] font-bold text-white shadow pointer-events-none whitespace-nowrap z-30"
                                         :class="{'opacity-100': activeDragKey === 'title' || selectedElementKey === 'title'}">
                                        ✥ Judul Dokumen (X:<span x-text="layout.title.x"></span>% Y:<span x-text="layout.title.y"></span>%)
                                    </div>

                                    <h4 class="font-black tracking-[0.2em] uppercase leading-tight transition-all"
                                        :style="'font-family: ' + getFontFamilyStyle(layout.title.fontFamily) + '; font-size: ' + layout.title.fontSize + 'px; color: ' + layout.title.color + ';'"
                                        x-text="certTitle"></h4>
                                    <p x-show="certShowNumber" class="font-mono mt-0.5"
                                       :style="'font-size: ' + Math.max(6, Math.round(layout.title.fontSize * 0.7)) + 'px; color: ' + layout.title.color + '; opacity: 0.7;'">
                                        Nomor: CERT/OL-2026/09/88219
                                    </p>
                                </div>

                                <!-- 3. Penerima Sertifikat / Nama Peserta (Draggable) -->
                                <div @mousedown.prevent="startDrag('recipient', $event)"
                                     @click.stop="selectedElementKey = 'recipient'"
                                     :style="'left: ' + layout.recipient.x + '%; top: ' + layout.recipient.y + '%; transform: translate(-50%, -50%); width: 85%; max-width: 500px;'"
                                     class="absolute z-20 select-none group cursor-grab active:cursor-grabbing p-1 rounded-xl transition-all text-center border border-transparent"
                                     :class="{
                                         'ring-2 ring-indigo-500 bg-indigo-500/15 shadow-lg border-indigo-400': activeDragKey === 'recipient' || selectedElementKey === 'recipient',
                                         'hover:border-dashed hover:border-indigo-400 hover:bg-slate-900/5': activeDragKey !== 'recipient' && selectedElementKey !== 'recipient'
                                     }">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-[8px] font-bold text-white shadow pointer-events-none whitespace-nowrap z-30"
                                         :class="{'opacity-100': activeDragKey === 'recipient' || selectedElementKey === 'recipient'}">
                                        ✥ Nama Peserta (X:<span x-text="layout.recipient.x"></span>% Y:<span x-text="layout.recipient.y"></span>%)
                                    </div>

                                    <p class="text-slate-500 mb-0.5"
                                       :style="'font-size: ' + Math.max(7, Math.round(layout.recipient.fontSize * 0.42)) + 'px;'">
                                        Diberikan dengan bangga atas kelulusan dan kompetensi:
                                    </p>
                                    <div class="py-0.5">
                                        <h2 class="tracking-tight border-b inline-block pb-0.5 px-4 sm:px-6 transition-all leading-tight font-bold"
                                            :style="'color: ' + layout.recipient.color + '; border-color: ' + layout.recipient.color + '40; font-family: ' + getFontFamilyStyle(layout.recipient.fontFamily) + '; font-size: ' + layout.recipient.fontSize + 'px;'"
                                            x-text="previewStudentName"></h2>
                                    </div>
                                </div>

                                <!-- 4. Pernyataan & Informasi Kursus (Draggable) -->
                                <div @mousedown.prevent="startDrag('course', $event)"
                                     @click.stop="selectedElementKey = 'course'"
                                     :style="'left: ' + layout.course.x + '%; top: ' + layout.course.y + '%; transform: translate(-50%, -50%); width: 85%; max-width: 480px;'"
                                     class="absolute z-20 select-none group cursor-grab active:cursor-grabbing p-1 rounded-xl transition-all text-center border border-transparent"
                                     :class="{
                                         'ring-2 ring-indigo-500 bg-indigo-500/15 shadow-lg border-indigo-400': activeDragKey === 'course' || selectedElementKey === 'course',
                                         'hover:border-dashed hover:border-indigo-400 hover:bg-slate-900/5': activeDragKey !== 'course' && selectedElementKey !== 'course'
                                     }">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-[8px] font-bold text-white shadow pointer-events-none whitespace-nowrap z-30"
                                         :class="{'opacity-100': activeDragKey === 'course' || selectedElementKey === 'course'}">
                                        ✥ Keterangan & Kursus (X:<span x-text="layout.course.x"></span>% Y:<span x-text="layout.course.y"></span>%)
                                    </div>

                                    <p class="max-w-sm mx-auto leading-tight mb-1"
                                       :style="'font-family: ' + getFontFamilyStyle(layout.course.fontFamily) + '; font-size: ' + Math.max(6, Math.round(layout.course.fontSize * 0.82)) + 'px; color: ' + layout.course.color + '; opacity: 0.85;'"
                                       x-text="certStatement"></p>
                                    <div class="inline-block bg-white/95 border border-slate-200/90 rounded-lg px-2.5 py-0.5 shadow-2xs">
                                        <p class="font-extrabold leading-snug"
                                           :style="'font-family: ' + getFontFamilyStyle(layout.course.fontFamily) + '; font-size: ' + layout.course.fontSize + 'px; color: ' + layout.course.color + ';'">{{ $course->title }}</p>
                                        <span x-show="certShowJp" class="inline-block font-bold font-mono"
                                              :style="'font-size: ' + Math.max(6, Math.round(layout.course.fontSize * 0.75)) + 'px; color: ' + layout.course.color + '; opacity: 0.9;'">
                                            Bobot: {{ $course->jp ?? 2 }} Jam Pelajaran (JP)
                                        </span>
                                    </div>
                                </div>

                                <!-- 5. Tanda Tangan & Pejabat Resmi (FREE DRAG & MOVE) -->
                                <div @mousedown.prevent="startDrag('signature', $event)"
                                     @click.stop="selectedElementKey = 'signature'"
                                     :style="'left: ' + layout.signature.x + '%; top: ' + layout.signature.y + '%; transform: translate(-50%, -50%); min-width: 130px; max-width: 220px;'"
                                     class="absolute z-20 select-none group cursor-grab active:cursor-grabbing p-1.5 rounded-xl transition-all text-center border border-transparent"
                                     :class="{
                                         'ring-2 ring-indigo-500 bg-indigo-500/15 shadow-lg border-indigo-400': activeDragKey === 'signature' || selectedElementKey === 'signature',
                                         'hover:border-dashed hover:border-indigo-400 hover:bg-slate-900/5': activeDragKey !== 'signature' && selectedElementKey !== 'signature'
                                     }">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-[8px] font-bold text-white shadow pointer-events-none whitespace-nowrap z-30"
                                         :class="{'opacity-100': activeDragKey === 'signature' || selectedElementKey === 'signature'}">
                                        ✥ Tanda Tangan & Pejabat (X:<span x-text="layout.signature.x"></span>% Y:<span x-text="layout.signature.y"></span>%)
                                    </div>

                                    <div class="space-y-0.5">
                                        <p class="font-medium"
                                           :style="'font-size: ' + Math.max(6, Math.round(layout.signature.fontSize * 0.7)) + 'px; color: ' + layout.signature.color + '; opacity: 0.65;'">Pejabat Penandatangan:</p>
                                        <div class="h-6 flex items-center justify-center">
                                            <template x-if="sigPreview">
                                                <img :src="sigPreview" class="h-6 object-contain" alt="Signature">
                                            </template>
                                            <template x-if="!sigPreview">
                                                <span class="font-['Great_Vibes'] text-sm sm:text-base opacity-75"
                                                      :style="'color: ' + layout.signature.color">Tanda Tangan Sah</span>
                                            </template>
                                        </div>
                                        <p class="font-bold leading-tight"
                                           :style="'font-family: ' + getFontFamilyStyle(layout.signature.fontFamily) + '; font-size: ' + layout.signature.fontSize + 'px; color: ' + layout.signature.color + ';'"
                                           x-text="signatoryName"></p>
                                        <p class="font-medium leading-tight"
                                           :style="'font-size: ' + Math.max(6, Math.round(layout.signature.fontSize * 0.78)) + 'px; color: ' + layout.signature.color + '; opacity: 0.8;'"
                                           x-text="signatoryRole"></p>
                                        <p class="font-mono"
                                           :style="'font-size: ' + Math.max(6, Math.round(layout.signature.fontSize * 0.68)) + 'px; color: ' + layout.signature.color + '; opacity: 0.6;'"
                                           x-text="'NIP. ' + signatoryNip"></p>
                                    </div>
                                </div>

                                <!-- 6. Cap Stempel Digital Resmi (Draggable) -->
                                <template x-if="certShowSeal">
                                    <div @mousedown.prevent="startDrag('seal', $event)"
                                         @click.stop="selectedElementKey = 'seal'"
                                         :style="'left: ' + layout.seal.x + '%; top: ' + layout.seal.y + '%; transform: translate(-50%, -50%);'"
                                         class="absolute z-20 select-none group cursor-grab active:cursor-grabbing p-1 rounded-xl transition-all flex flex-col items-center justify-center border border-transparent"
                                         :class="{
                                             'ring-2 ring-indigo-500 bg-indigo-500/15 shadow-lg border-indigo-400': activeDragKey === 'seal' || selectedElementKey === 'seal',
                                             'hover:border-dashed hover:border-indigo-400 hover:bg-slate-900/5': activeDragKey !== 'seal' && selectedElementKey !== 'seal'
                                         }">
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-[8px] font-bold text-white shadow pointer-events-none whitespace-nowrap z-30"
                                             :class="{'opacity-100': activeDragKey === 'seal' || selectedElementKey === 'seal'}">
                                            ✥ Cap Stempel (X:<span x-text="layout.seal.x"></span>% Y:<span x-text="layout.seal.y"></span>%)
                                        </div>

                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 border-double flex flex-col items-center justify-center shadow-xs transform rotate-[-4deg]"
                                             :style="'border-color: ' + layout.seal.color + '; color: ' + layout.seal.color + '; background-color: ' + layout.seal.color + '15;'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>
                                            <span class="font-black uppercase tracking-wider text-center px-1"
                                                  :style="'font-family: ' + getFontFamilyStyle(layout.seal.fontFamily) + '; font-size: ' + layout.seal.fontSize + 'px; color: ' + layout.seal.color + ';'"
                                                  x-text="certSealText"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- 7. QR Code & Verifikasi Online (Draggable) -->
                                <template x-if="certShowQr">
                                    <div @mousedown.prevent="startDrag('qr', $event)"
                                         @click.stop="selectedElementKey = 'qr'"
                                         :style="'left: ' + layout.qr.x + '%; top: ' + layout.qr.y + '%; transform: translate(-50%, -50%); min-width: 110px; max-width: 170px;'"
                                         class="absolute z-20 select-none group cursor-grab active:cursor-grabbing p-1 rounded-xl transition-all text-left border border-transparent"
                                         :class="{
                                             'ring-2 ring-indigo-500 bg-indigo-500/15 shadow-lg border-indigo-400': activeDragKey === 'qr' || selectedElementKey === 'qr',
                                             'hover:border-dashed hover:border-indigo-400 hover:bg-slate-900/5': activeDragKey !== 'qr' && selectedElementKey !== 'qr'
                                         }">
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-[8px] font-bold text-white shadow pointer-events-none whitespace-nowrap z-30"
                                             :class="{'opacity-100': activeDragKey === 'qr' || selectedElementKey === 'qr'}">
                                            ✥ QR Verifikasi (X:<span x-text="layout.qr.x"></span>% Y:<span x-text="layout.qr.y"></span>%)
                                        </div>

                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-7 h-7 rounded bg-white p-0.5 border border-slate-300 shadow-2xs flex items-center justify-center font-mono font-bold text-[6px] text-slate-800">
                                                    [QR]
                                                </div>
                                                <div class="leading-tight">
                                                    <p class="font-bold" :style="'font-family: ' + getFontFamilyStyle(layout.qr.fontFamily) + '; font-size: ' + layout.qr.fontSize + 'px; color: ' + layout.qr.color + ';'">Scan Verifikasi</p>
                                                    <p class="font-mono text-[5px]" :style="'color: ' + layout.qr.color + '; opacity: 0.7;'">A78B99C12E4F</p>
                                                </div>
                                            </div>
                                            <p x-show="certShowDate" class="text-[6px]" :style="'color: ' + layout.qr.color + '; opacity: 0.75;'">Diterbitkan: {{ date('d F Y') }}</p>
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>

    <!-- TAB 2: SILABUS & MODUL PEMBELAJARAN (KURIKULUM) -->
    <div x-show="activeTab === 'syllabus'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Add Lesson Form (5 Cols) -->
            <div class="lg:col-span-5 p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900">Tambah Modul / Bab Baru</h2>
                    <p class="text-xs text-slate-500">Tambahkan materi video, dokumen bacaan, pre-test, post-test, atau tugas praktik.</p>
                </div>

                <form action="{{ route('admin.courses.lessons.store', $course->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Bagian / Section Judul <span class="text-rose-500">*</span></label>
                        <input type="text" name="section_title" required
                               placeholder="Contoh: Modul 1: Pengenalan & Konsep Dasar"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Judul Materi / Bab <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required
                               placeholder="Contoh: Arsitektur Sistem & Alur Registrasi"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 outline-none font-bold">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tipe Materi <span class="text-rose-500">*</span></label>
                            <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                <option value="video">🎥 Video Interaktif</option>
                                <option value="pdf">📄 Modul PDF / Slide</option>
                                <option value="reading">📖 Teks Bacaan / Modul</option>
                                <option value="pre_test">📝 Evaluasi Pre-Test</option>
                                <option value="post_test">🏆 Uji Post-Test Akhir</option>
                                <option value="assignment">🛠️ Penugasan Praktik</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Durasi (Menit)</label>
                            <input type="number" name="duration_minutes" value="15" min="1"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">URL Video (YouTube / Vimeo / Cloud)</label>
                        <input type="url" name="video_url"
                               placeholder="https://www.youtube.com/watch?v=..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">URL Berkas PDF / Slide Presentasi</label>
                        <input type="url" name="pdf_url"
                               placeholder="https://domain.com/files/modul-1.pdf"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Konten / Ringkasan Teks</label>
                        <textarea name="content" rows="3"
                                  placeholder="Tuliskan catatan penting atau instruksi belajar untuk bab ini..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_free_preview" value="1" id="free_prev"
                               class="w-4 h-4 text-brand-600 rounded border-slate-300">
                        <label for="free_prev" class="text-xs font-semibold text-slate-700">Izinkan Pratinjau Gratis (Free Preview)</label>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        + Tambahkan ke Silabus Kursus
                    </button>
                </form>
            </div>

            <!-- Right: Existing Syllabus List (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Daftar Silabus & Modul Aktif</h2>
                            <p class="text-xs text-slate-500">Materi tersusun berurutan sesuai alur pembelajaran peserta.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full font-bold bg-slate-100 text-slate-700 text-[10px] font-mono">
                            {{ $course->lessons->count() }} Modul
                        </span>
                    </div>

                    @forelse($course->lessons as $l)
                        <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 flex items-center justify-between gap-3 hover:bg-slate-100/60 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-700 font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ $l->order }}
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">{{ $l->section_title }}</span>
                                    <p class="font-bold text-slate-900 text-xs truncate">{{ $l->title }}</p>
                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500">
                                        <span class="font-semibold text-brand-700 uppercase font-mono text-[9px] px-1.5 py-0.2 rounded bg-brand-50 border border-brand-200/60">{{ $l->type }}</span>
                                        <span>•</span>
                                        <span>{{ $l->duration_minutes }} Menit</span>
                                        @if($l->is_free_preview)
                                            <span>•</span>
                                            <span class="text-emerald-600 font-bold text-[10px]">Gratis</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('classroom.show', [$course->slug, $l->slug]) }}" target="_blank"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-2xs shrink-0">
                                Buka
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Belum ada modul materi dalam silabus kursus ini. Gunakan formulir di samping untuk menambahkan modul pertama.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
        {{-- TAB 4: EVALUASI PEMBELAJARAN (KAK Poin 16) --}}
        <div x-show="activeTab === 'evaluasi'" class="space-y-6">

            {{-- Header --}}
            <div class="p-6 bg-gradient-to-br from-teal-900 to-teal-700 rounded-3xl text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-black tracking-tight flex items-center gap-2">
                            <span>📊</span> Evaluasi Pembelajaran
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 border border-white/30">KAK Poin 16</span>
                        </h2>
                        <p class="text-teal-200 text-xs mt-1">Rekap nilai akademik peserta dan evaluasi reaksi kepuasan terhadap kualitas pelatihan ini.</p>
                    </div>
                    @php
                        $totalEnrollments = $course->enrollments()->count();
                        $completedEnrollments = $course->enrollments()->whereNotNull('completed_at')->count();
                        $evaluations = $course->evaluations ?? collect();
                        $evalCount = method_exists($course, 'evaluations') ? $course->evaluations()->count() : 0;
                        $avgRating = $evalCount > 0 ? round($course->evaluations()->avg('rating_overall'), 1) : 0;
                    @endphp
                    <div class="flex items-center gap-4 text-center">
                        <div>
                            <p class="text-2xl font-black">{{ $totalEnrollments }}</p>
                            <p class="text-[10px] text-teal-300 uppercase tracking-wider">Total Peserta</p>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black">{{ $completedEnrollments }}</p>
                            <p class="text-[10px] text-teal-300 uppercase tracking-wider">Lulus</p>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black">{{ $evalCount }}</p>
                            <p class="text-[10px] text-teal-300 uppercase tracking-wider">Evaluasi</p>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black flex items-center gap-1">
                                {{ $avgRating > 0 ? $avgRating : '-' }}
                                @if($avgRating > 0)
                                    <span class="text-yellow-300 text-base">★</span>
                                @endif
                            </p>
                            <p class="text-[10px] text-teal-300 uppercase tracking-wider">Rata Reaksi</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Rekap Nilai Akademik --}}
                <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">🎓 Rekap Nilai Akademik</h3>
                        <p class="text-slate-500">Rata-rata nilai peserta dari pre-test, post-test, dan tugas di kursus ini.</p>
                    </div>

                    @php
                        use App\Models\QuizAttempt;
                        use App\Models\AssignmentSubmission;

                        // Pre-test avg
                        $pretestLessons = $course->lessons->where('type', 'pre_test');
                        $pretestAvg = 0;
                        if ($pretestLessons->isNotEmpty()) {
                            $quizIds = $pretestLessons->map(fn($l) => optional($l->quiz)->id)->filter();
                            if ($quizIds->isNotEmpty()) {
                                $pretestAvg = round(QuizAttempt::whereIn('quiz_id', $quizIds)->avg('score') ?? 0, 1);
                            }
                        }

                        // Post-test avg
                        $posttestLessons = $course->lessons->where('type', 'post_test');
                        $posttestAvg = 0;
                        if ($posttestLessons->isNotEmpty()) {
                            $quizIds2 = $posttestLessons->map(fn($l) => optional($l->quiz)->id)->filter();
                            if ($quizIds2->isNotEmpty()) {
                                $posttestAvg = round(QuizAttempt::whereIn('quiz_id', $quizIds2)->avg('score') ?? 0, 1);
                            }
                        }

                        // Assignment avg
                        $assignmentLessons = $course->lessons->where('type', 'assignment');
                        $assignmentAvg = 0;
                        if ($assignmentLessons->isNotEmpty()) {
                            $assignIds = $assignmentLessons->map(fn($l) => optional($l->assignment)->id)->filter();
                            if ($assignIds->isNotEmpty()) {
                                $assignmentAvg = round(AssignmentSubmission::whereIn('assignment_id', $assignIds)->whereNotNull('score')->avg('score') ?? 0, 1);
                            }
                        }

                        // Completionrate
                        $completionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;
                    @endphp

                    <div class="space-y-3">
                        {{-- Pre-Test --}}
                        <div class="flex items-center justify-between p-3 bg-teal-50/60 rounded-2xl border border-teal-200/80">
                            <div class="flex items-center gap-2">
                                <span class="text-base">📝</span>
                                <div>
                                    <p class="font-bold text-slate-900">Rata-rata Pre-Test</p>
                                    <p class="text-slate-500">{{ $pretestLessons->count() }} modul pre-test</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black {{ $pretestAvg >= 70 ? 'text-teal-700' : 'text-rose-600' }}">
                                    {{ $pretestAvg > 0 ? $pretestAvg : '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400">/ 100</p>
                            </div>
                        </div>

                        {{-- Post-Test --}}
                        <div class="flex items-center justify-between p-3 bg-indigo-50/60 rounded-2xl border border-indigo-200/80">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🎓</span>
                                <div>
                                    <p class="font-bold text-slate-900">Rata-rata Post-Test</p>
                                    <p class="text-slate-500">{{ $posttestLessons->count() }} modul post-test</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black {{ $posttestAvg >= 70 ? 'text-indigo-700' : 'text-rose-600' }}">
                                    {{ $posttestAvg > 0 ? $posttestAvg : '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400">/ 100</p>
                            </div>
                        </div>

                        {{-- Assignment --}}
                        <div class="flex items-center justify-between p-3 bg-purple-50/60 rounded-2xl border border-purple-200/80">
                            <div class="flex items-center gap-2">
                                <span class="text-base">💼</span>
                                <div>
                                    <p class="font-bold text-slate-900">Rata-rata Nilai Tugas</p>
                                    <p class="text-slate-500">{{ $assignmentLessons->count() }} modul assignment</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black {{ $assignmentAvg >= 70 ? 'text-purple-700' : ($assignmentAvg > 0 ? 'text-rose-600' : 'text-slate-400') }}">
                                    {{ $assignmentAvg > 0 ? $assignmentAvg : '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400">/ 100</p>
                            </div>
                        </div>

                        {{-- Completion Rate --}}
                        <div class="flex items-center justify-between p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200/80">
                            <div class="flex items-center gap-2">
                                <span class="text-base">✅</span>
                                <div>
                                    <p class="font-bold text-slate-900">Tingkat Penyelesaian</p>
                                    <p class="text-slate-500">{{ $completedEnrollments }} dari {{ $totalEnrollments }} peserta lulus</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black text-emerald-700">{{ $completionRate }}%</p>
                                <div class="w-20 h-1.5 bg-emerald-100 rounded-full mt-1 overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $completionRate }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Evaluasi Reaksi Peserta --}}
                <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">⭐ Evaluasi Reaksi Peserta</h3>
                        <p class="text-slate-500">Hasil penilaian kepuasan peserta terhadap penyelenggaraan pelatihan (Kirkpatrick Level 1).</p>
                    </div>

                    @if($evalCount === 0)
                        <div class="py-8 text-center">
                            <span class="text-4xl">📋</span>
                            <p class="font-bold text-slate-700 mt-3">Belum ada evaluasi masuk</p>
                            <p class="text-slate-500 text-[11px] mt-1 max-w-xs mx-auto">Peserta dapat mengisi form evaluasi setelah menyelesaikan 100% materi kursus ini.</p>
                        </div>
                    @else
                        @php
                            $evalData = $course->evaluations();
                            $avgMateri = round($evalData->avg('rating_materi'), 1);
                            $avgInstruktur = round($evalData->avg('rating_instruktur'), 1);
                            $avgPlatform = round($evalData->avg('rating_platform'), 1);
                            $avgRelevansi = round($evalData->avg('rating_relevansi'), 1);
                            $avgOverall = round($evalData->avg('rating_overall'), 1);
                            $willRecommend = $evalData->where('will_recommend', true)->count();
                        @endphp

                        {{-- Rating Grid --}}
                        <div class="space-y-2.5">
                            @foreach([
                                ['label' => 'Kualitas Materi & Konten', 'val' => $avgMateri, 'color' => 'blue'],
                                ['label' => 'Kualitas Instruktur/Narasumber', 'val' => $avgInstruktur, 'color' => 'indigo'],
                                ['label' => 'Kemudahan Platform', 'val' => $avgPlatform, 'color' => 'teal'],
                                ['label' => 'Relevansi dengan Pekerjaan', 'val' => $avgRelevansi, 'color' => 'emerald'],
                                ['label' => 'Rating Keseluruhan', 'val' => $avgOverall, 'color' => 'yellow'],
                            ] as $item)
                                <div class="flex items-center gap-3">
                                    <span class="w-40 text-slate-600 shrink-0">{{ $item['label'] }}</span>
                                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-{{ $item['color'] }}-500 rounded-full transition-all duration-500"
                                             style="width: {{ ($item['val'] / 5) * 100 }}%"></div>
                                    </div>
                                    <span class="font-bold text-slate-900 w-8 text-right">{{ $item['val'] }}/5</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center gap-3 text-[11px]">
                            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold">
                                👍 {{ $willRecommend }} dari {{ $evalCount }} ({{ $evalCount > 0 ? round(($willRecommend/$evalCount)*100) : 0 }}%) merekomendasikan
                            </span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Info tentang form evaluasi untuk peserta --}}
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-200/80 flex items-start gap-3 text-xs">
                <span class="text-blue-600 text-base shrink-0">ℹ️</span>
                <div>
                    <p class="font-bold text-blue-900">Form Evaluasi untuk Peserta</p>
                    <p class="text-blue-700 mt-0.5">Form evaluasi reaksi peserta (rating + komentar) akan otomatis muncul di halaman ruang belajar setelah peserta menyelesaikan 100% materi. Data evaluasi ini terintegrasi langsung ke dashboard laporan ini.</p>
                </div>
            </div>

        </div>

</div>
@endsection
