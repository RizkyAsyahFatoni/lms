@extends('layouts.admin')

@section('page_title', 'Certificate Builder: ' . Str::limit($course->title, 25))

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
function certBuilderStudio() {
    return {
        certTemplateMode: @json($course->cert_template_mode ?? 'default'),
        certBgPreview: @json($course->cert_bg_image ?? asset('uploads/certificates/blank_certificate_gold_navy.png')),
        certTheme: @json($course->cert_theme_color ?: 'teal'),
        certTitle: @json($course->cert_title ?: 'SERTIFIKAT KOMPETENSI RESMI'),
        institutionName: @json($course->cert_institution_name ?: 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA'),
        institutionSub: @json($course->cert_institution_sub ?: 'Pemerintah Kabupaten Berau'),
        signatoryName: @json($course->cert_signatory_name ?: ($course->instructor->name ?? 'Dra. Hj. Sri Eka Yuliana, M.Si')),
        signatoryRole: @json($course->cert_signatory_role ?: 'Kepala BKPSDM Kabupaten Berau'),
        signatoryNip: @json($course->cert_signatory_nip ?: '19720514 199803 2 004'),
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
        courseTitle: @json($course->title),
        recipientName: 'dr. H. Muhammad Fathurrahman, Sp.A / S.Kom',
        sigPreview: @json($course->cert_signature_image ?? ''),
        certLogoPreview: @json($course->cert_logo_url ?? ''),
        removeCertLogo: false,
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
window.certBuilderStudio = certBuilderStudio;
document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('certBuilderStudio', certBuilderStudio);
    }
});
</script>

<div class="space-y-6" x-data="certBuilderStudio()">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Interactive Certificate Builder</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-teal-50 text-teal-700 border border-teal-200">
                    Studio Pro
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Desain piagam resmi, unggah background kosongan kustom, dan kelola atribut otomatis kelulusan.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.edit', $course->id) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                &larr; Kembali ke Edit Kursus
            </a>
            <button type="submit" form="certForm" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Desain Sertifikat</span>
            </button>
        </div>
    </div>

    <!-- MAIN TWO COLUMN BUILDER -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
        <!-- LEFT COLUMN: FORM CONTROLS (6 COLS) -->
        <div class="lg:col-span-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5 text-xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Konfigurasi Desain & Atribut Sertifikat</h3>
                <span class="text-[11px] text-teal-600 font-bold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    Live Preview Aktif
                </span>
            </div>

            <form id="certForm" action="{{ route('admin.courses.certificate-builder.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Status Penerbitan -->
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                    <div>
                        <p class="font-bold text-slate-900">Terbitkan E-Sertifikat Digital Otomatis</p>
                        <p class="text-[11px] text-slate-500">Sertifikat otomatis terbit saat siswa tuntas belajar 100%</p>
                    </div>
                    <input type="checkbox" name="certificate_available" value="1" {{ $course->certificate_available ? 'checked' : '' }} class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 w-4 h-4">
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
                            <span class="text-[10px] text-slate-500 leading-snug">Bingkai ganda ornamen resmi dengan tema warna modern.</span>
                        </label>

                        <label class="flex flex-col p-3 rounded-2xl border cursor-pointer transition text-left relative"
                               :class="certTemplateMode === 'custom_bg' ? 'bg-teal-50/70 border-teal-500 ring-2 ring-teal-500/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/60'">
                            <input type="radio" name="cert_template_mode" value="custom_bg" x-model="certTemplateMode" class="sr-only">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-lg">🖼️</span>
                                <span class="font-bold text-slate-900 text-xs">Background Kosongan</span>
                            </div>
                            <span class="text-[10px] text-slate-500 leading-snug">Upload template piagam kosong, teks & nomor otomatis di atas gambar.</span>
                        </label>
                    </div>
                </div>

                <!-- 2. Sub-Settings: Custom Background Image Upload & Offset -->
                <div x-show="certTemplateMode === 'custom_bg'" x-transition class="p-4 bg-amber-50/60 border border-amber-200/80 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span>🎨</span>
                            <span>Upload Template Piagam Kosongan</span>
                        </h4>
                        <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">📐 Rekomendasi: 2970 × 2100 px (A4 Landscape 300 DPI, min. 1920×1357 px)</span>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pilih Berkas Gambar Template (JPG / PNG / WebP)</label>
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

                <!-- 3. Sub-Settings: Tema Warna Bingkai Default -->
                <div x-show="certTemplateMode === 'default'" x-transition>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tema Aksen Warna Sertifikat <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-5 gap-2">
                        <label class="flex flex-col items-center p-2 rounded-xl border cursor-pointer text-center"
                               :class="certTheme === 'teal' ? 'bg-teal-50 border-teal-500 ring-2 ring-teal-500/20' : 'bg-slate-50 border-slate-200'">
                            <input type="radio" name="cert_theme_color" value="teal" x-model="certTheme" class="sr-only">
                            <span class="w-5 h-5 rounded-full bg-teal-600 mb-1"></span>
                            <span class="text-[10px] font-bold text-slate-700">Teal</span>
                        </label>

                        <label class="flex flex-col items-center p-2 rounded-xl border cursor-pointer text-center"
                               :class="certTheme === 'indigo' ? 'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-500/20' : 'bg-slate-50 border-slate-200'">
                            <input type="radio" name="cert_theme_color" value="indigo" x-model="certTheme" class="sr-only">
                            <span class="w-5 h-5 rounded-full bg-indigo-600 mb-1"></span>
                            <span class="text-[10px] font-bold text-slate-700">Indigo</span>
                        </label>

                        <label class="flex flex-col items-center p-2 rounded-xl border cursor-pointer text-center"
                               :class="certTheme === 'blue' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200'">
                            <input type="radio" name="cert_theme_color" value="blue" x-model="certTheme" class="sr-only">
                            <span class="w-5 h-5 rounded-full bg-blue-600 mb-1"></span>
                            <span class="text-[10px] font-bold text-slate-700">Blue</span>
                        </label>

                        <label class="flex flex-col items-center p-2 rounded-xl border cursor-pointer text-center"
                               :class="certTheme === 'emerald' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20' : 'bg-slate-50 border-slate-200'">
                            <input type="radio" name="cert_theme_color" value="emerald" x-model="certTheme" class="sr-only">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 mb-1"></span>
                            <span class="text-[10px] font-bold text-slate-700">Emerald</span>
                        </label>

                        <label class="flex flex-col items-center p-2 rounded-xl border cursor-pointer text-center"
                               :class="certTheme === 'slate' ? 'bg-slate-100 border-slate-700 ring-2 ring-slate-700/20' : 'bg-slate-50 border-slate-200'">
                            <input type="radio" name="cert_theme_color" value="slate" x-model="certTheme" class="sr-only">
                            <span class="w-5 h-5 rounded-full bg-slate-800 mb-1"></span>
                            <span class="text-[10px] font-bold text-slate-700">Classic</span>
                        </label>
                    </div>
                </div>

                <!-- 4. Institution Header & Titles -->
                <div class="space-y-3">
                    <!-- Pengaturan Logo Kop Lembaga (Upload / Replace / Delete) -->
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2.5">
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
                                <p class="text-[10px] text-slate-500">Logo lembaga pada kop sertifikat. <strong class="text-teal-700">Rekomendasi: 300 × 300 px</strong> (Format PNG transparan atau SVG).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lembaga / Institusi Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="cert_institution_name" x-model="institutionName" required
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Sub-Unit / Bidang Lembaga</label>
                    <input type="text" name="cert_institution_sub" x-model="institutionSub"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tajuk / Judul Sertifikat <span class="text-red-500">*</span></label>
                    <input type="text" name="cert_title" x-model="certTitle" required
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Pernyataan Capaian Kelulusan</label>
                    <textarea name="cert_statement" x-model="certStatement" rows="2"
                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"></textarea>
                </div>

                <!-- 6. Pejabat Penandatangan & Tanda Tangan Basah -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span>🖋️</span>
                        <span>Data Penandatangan Resmi</span>
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Lengkap & Gelar</label>
                            <input type="text" name="cert_signatory_name" x-model="signatoryName"
                                   class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jabatan Resmi</label>
                            <input type="text" name="cert_signatory_role" x-model="signatoryRole"
                                   class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor Induk Pegawai (NIP / SK)</label>
                            <input type="text" name="cert_signatory_nip" x-model="signatoryNip"
                                   class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-semibold text-slate-600">Unggah Tanda Tangan Basah Transparan (Opsional)</label>
                            <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                📐 Rekomendasi: 400 × 200 px (Rasio 2:1 PNG Transparan)
                            </span>
                        </div>
                        <input type="file" name="cert_signature_file" accept="image/png,image/webp"
                               @change="handleSigFileSelect($event)"
                               class="w-full px-3 py-1.5 text-xs bg-white rounded-xl border border-slate-200 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-800 file:text-white cursor-pointer">
                        <input type="hidden" name="cert_signature_image" :value="sigPreview">
                        <p class="text-[10px] text-slate-400 mt-1">Gunakan format PNG berlatar transparan (400×200 px) agar tanda tangan pejabat menyatu dengan piagam.</p>
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
                            <span>Free Drag & Move Studio (Posisi Bebas)</span>
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

            </form>
        </div>

        <!-- RIGHT COLUMN: LIVE VISUAL CANVAS (6 COLS) -->
        <div class="lg:col-span-6 sticky top-20 space-y-3">
            
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
                    <input type="text" x-model="recipientName"
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
                                            x-text="institutionName"></h3>
                                        <p class="uppercase tracking-widest font-semibold"
                                           :style="'font-family: ' + getFontFamilyStyle(layout.header.fontFamily) + '; font-size: ' + Math.max(6, Math.round(layout.header.fontSize * 0.72)) + 'px; color: ' + layout.header.color + '; opacity: 0.75;'"
                                           x-text="institutionSub"></p>
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
                                    x-text="recipientName"></h2>
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
                                   :style="'font-family: ' + getFontFamilyStyle(layout.course.fontFamily) + '; font-size: ' + layout.course.fontSize + 'px; color: ' + layout.course.color + ';'"
                                   x-text="courseTitle"></p>
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
@endsection
