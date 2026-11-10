@extends('layouts.admin')

@section('page_title', 'Tulis Artikel & Unggah Berkas Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    
    <!-- Top Nav / Back -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.articles.index') }}" class="text-xs text-brand-600 font-bold hover:underline flex items-center gap-1">
                &larr; Kembali ke Daftar Artikel
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                Tulis Artikel & Unggah Berkas Baru
            </h1>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs space-y-1">
            <p class="font-bold">Terdapat beberapa kesalahan pengisian formulir:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
          x-data="{
              attachments: [
                  { title: '', type: 'regulasi', sourceType: 'upload', external_url: '' }
              ],
              addAttachment() {
                  this.attachments.push({ title: '', type: 'dokumen', sourceType: 'upload', external_url: '' });
              },
              removeAttachment(index) {
                  this.attachments.splice(index, 1);
              }
          }">
        @csrf

        <!-- Card 1: Informasi Pokok Artikel -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                <span>📝</span> 1. Informasi Utama Artikel & Pengetahuan
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Artikel / Publikasi Materi <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                           placeholder="Contoh: Peraturan Bupati Berau No. 24 Tahun 2024 tentang Tata Kelola Corporate University"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Konten <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <option value="regulasi" {{ old('category') === 'regulasi' ? 'selected' : '' }}>⚖️ Regulasi & Kebijakan Daerah</option>
                            <option value="panduan" {{ old('category') === 'panduan' ? 'selected' : '' }}>📘 Panduan Teknis & SOP</option>
                            <option value="presentasi" {{ old('category') === 'presentasi' ? 'selected' : '' }}>📊 Materi Presentasi & Paparan</option>
                            <option value="best_practice" {{ old('category') === 'best_practice' ? 'selected' : '' }}>💡 Best Practice & Inovasi OPD</option>
                            <option value="materi_sharing" {{ old('category') === 'materi_sharing' ? 'selected' : '' }}>🤝 Knowledge Sharing ASN</option>
                            <option value="umum" {{ old('category') === 'umum' ? 'selected' : '' }}>📰 Artikel Umum / Wawasan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Instansi / OPD Kontributor</label>
                        <input type="text" name="opd_name" value="{{ old('opd_name', $user->opd_name ?: 'BKPSDM Kabupaten Berau') }}" 
                               placeholder="Nama Dinas / Badan / OPD"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penulis / Pemateri</label>
                        <input type="text" name="author_name" value="{{ old('author_name', $user->name) }}" 
                               placeholder="Nama Lengkap & Gelar"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ringkasan Singkat (Excerpt / Abstrak)</label>
                    <textarea name="excerpt" rows="2" placeholder="Tuliskan 1-2 kalimat ringkasan yang menggambarkan isi dokumen atau artikel..."
                              class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">{{ old('excerpt') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Lengkap Artikel & Penjelasan Materi <span class="text-rose-500">*</span></label>
                    <p class="text-[11px] text-slate-400 mb-2">Gunakan toolbar untuk menambahkan Heading, List (Bullet / Nomor), format tebal/miring, tautan, atau menyisipkan gambar di dalam teks.</p>
                    
                    <!-- Quill Editor Container -->
                    <div id="quill-editor" class="bg-white rounded-b-xl min-h-[260px] text-xs">
                        {!! old('content') !!}
                    </div>
                    <input type="hidden" name="content" id="article_content" value="{{ old('content') }}">
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-between gap-1 mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Gambar Sampul (Opsional)</label>
                        <span class="text-[11px] font-semibold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-lg border border-teal-200">
                            📐 Rekomendasi: 1200 × 630 px (Rasio 16:9 Landscape)
                        </span>
                    </div>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-200 rounded-xl bg-slate-50/50">
                    <p class="text-[11px] text-slate-500 mt-1.5 flex flex-wrap items-center gap-1.5">
                        <span>💡 Format: <strong>JPG, PNG, atau WebP</strong> (Maks. 2 MB).</span>
                        <span>•</span>
                        <span>Disarankan rasio landscape <strong>16:9</strong> (1200 × 630 px atau 800 × 450 px) agar tidak terpotong saat ditampilkan di portal ASN.</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Multi Attachments Repeater (Regulasi, SOP, Paparan) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span>📎</span> 2. Berkas Lampiran & Dokumen Terkait (Attachments)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Anda dapat menyertakan berkas PDF Regulasi, Panduan SOP, slide presentasi PPTX, atau dokumen pendukung lainnya.
                    </p>
                </div>

                <button type="button" @click="addAttachment()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 font-bold text-xs transition">
                    <span>+ Tambah Lampiran</span>
                </button>
            </div>

            <!-- Repeater Container -->
            <div class="space-y-4">
                <template x-for="(att, index) in attachments" :key="index">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-3 relative">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-mono text-slate-700" x-text="index + 1"></span>
                                <span>Dokumen Lampiran</span>
                            </span>

                            <button type="button" @click="removeAttachment(index)" 
                                    x-show="attachments.length > 1"
                                    class="text-rose-500 hover:text-rose-700 text-xs font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                            <div class="sm:col-span-6">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul / Nama Dokumen</label>
                                <input type="text" :name="`attachments[${index}][title]`" x-model="att.title"
                                       placeholder="Misal: Perbup Berau No. 24 Th 2024 / Slide Paparan Modul 1"
                                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-brand-500">
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Jenis Dokumen</label>
                                <select :name="`attachments[${index}][attachment_type]`" x-model="att.type"
                                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-brand-500">
                                    <option value="regulasi">⚖️ Regulasi / Hukum</option>
                                    <option value="panduan">📘 Panduan / SOP</option>
                                    <option value="presentasi">📊 Materi Presentasi</option>
                                    <option value="modul">📑 Modul Pembelajaran</option>
                                    <option value="dokumen">📎 Dokumen Lainnya</option>
                                </select>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Sumber Berkas</label>
                                <select x-model="att.sourceType" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-brand-500 font-semibold">
                                    <option value="upload">📁 Unggah File Langsung</option>
                                    <option value="link">🔗 Tautan Google Drive / Cloud</option>
                                </select>
                            </div>
                        </div>

                        <!-- File Input or External Link -->
                        <div>
                            <div x-show="att.sourceType === 'upload'">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih File Berkas (PDF, PPTX, DOCX, XLSX, ZIP maks 20 MB)</label>
                                <input type="file" :name="`attachments[${index}][file]`"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:border file:border-slate-200 file:text-slate-700 hover:file:bg-slate-100">
                            </div>

                            <div x-show="att.sourceType === 'link'">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                    Tautan Berkas Google Drive / Cloud URL <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">🔗</span>
                                    <input type="url" :name="`attachments[${index}][external_url]`" x-model="att.external_url"
                                           placeholder="https://drive.google.com/file/d/... atau https://..."
                                           class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Tempelkan link share publik dari Google Drive / Dropbox (pastikan hak akses disetel 'Siapa saja yang memiliki link').</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Card 3: Opsi Publikasi & Aksi -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_published" value="1" checked 
                       class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                <div>
                    <span class="text-xs font-bold text-slate-900 block">Langsung Publikasikan ke Portal Web</span>
                    <span class="text-[11px] text-slate-500 block">Jika tidak dicentang, artikel akan tersimpan sebagai draf.</span>
                </div>
            </label>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.articles.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-brand-600 text-white text-xs font-bold transition shadow-sm">
                    Simpan & Terbitkan
                </button>
            </div>
        </div>

    </form>

</div>

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Tuliskan ulasan materi, uraian regulasi, atau panduan secara lengkap...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        const form = document.querySelector('form[action="{{ route('admin.articles.store') }}"]');
        if (form) {
            form.addEventListener('submit', function () {
                const contentInput = document.getElementById('article_content');
                contentInput.value = quill.root.innerHTML;
            });
        }
    });
</script>
@endsection
@endsection
