@extends('layouts.admin')

@section('page_title', 'Edit Artikel & Berkas Lampiran')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12"
     x-data="{
         // New attachments repeater
         newAttachments: [],
         addNewAttachment() {
             this.newAttachments.push({ title: '', type: 'dokumen', sourceType: 'upload', external_url: '' });
         },
         removeNewAttachment(index) {
             this.newAttachments.splice(index, 1);
         },

         // Modal Edit Lampiran Eksisting
         editModal: false,
         editItem: {
             id: null,
             actionUrl: '',
             title: '',
             attachment_type: 'dokumen',
             sourceType: 'keep',
             fileName: '',
             externalUrl: '',
             currentTypeLabel: ''
         },
         openEdit(item) {
             this.editItem = {
                 id: item.id,
                 actionUrl: item.actionUrl,
                 title: item.title,
                 attachment_type: item.attachment_type,
                 sourceType: item.hasExternal ? 'link' : 'keep',
                 fileName: item.fileName || '',
                 externalUrl: item.externalUrl || '',
                 currentTypeLabel: item.currentTypeLabel || ''
             };
             this.editModal = true;
         },
         closeEdit() {
             this.editModal = false;
         }
     }">
    
    <!-- Top Nav / Back -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.articles.index') }}" class="text-xs text-brand-600 font-bold hover:underline flex items-center gap-1">
                &larr; Kembali ke Daftar Artikel
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                Edit Artikel & Berkas Lampiran
            </h1>
        </div>

        <a href="{{ route('articles.show', $article->slug) }}" target="_blank" 
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
            <span>🌐 Pratinjau di Web</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
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

    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Card 1: Informasi Pokok Artikel -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                <span>📝</span> 1. Informasi Utama Artikel & Pengetahuan
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Artikel / Publikasi Materi <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Konten <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <option value="regulasi" {{ old('category', $article->category) === 'regulasi' ? 'selected' : '' }}>⚖️ Regulasi & Kebijakan Daerah</option>
                            <option value="panduan" {{ old('category', $article->category) === 'panduan' ? 'selected' : '' }}>📘 Panduan Teknis & SOP</option>
                            <option value="presentasi" {{ old('category', $article->category) === 'presentasi' ? 'selected' : '' }}>📊 Materi Presentasi & Paparan</option>
                            <option value="best_practice" {{ old('category', $article->category) === 'best_practice' ? 'selected' : '' }}>💡 Best Practice & Inovasi OPD</option>
                            <option value="materi_sharing" {{ old('category', $article->category) === 'materi_sharing' ? 'selected' : '' }}>🤝 Knowledge Sharing ASN</option>
                            <option value="umum" {{ old('category', $article->category) === 'umum' ? 'selected' : '' }}>📰 Artikel Umum / Wawasan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Instansi / OPD Kontributor</label>
                        <input type="text" name="opd_name" value="{{ old('opd_name', $article->opd_name) }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penulis / Pemateri</label>
                        <input type="text" name="author_name" value="{{ old('author_name', $article->author_name) }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ringkasan Singkat (Excerpt / Abstrak)</label>
                    <textarea name="excerpt" rows="2" 
                              class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">{{ old('excerpt', $article->excerpt) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Lengkap Artikel & Penjelasan Materi <span class="text-rose-500">*</span></label>
                    <p class="text-[11px] text-slate-400 mb-2">Gunakan toolbar untuk menambahkan Heading, List (Bullet / Nomor), format tebal/miring, tautan, atau menyisipkan gambar di dalam teks.</p>

                    <!-- Quill Editor Container -->
                    <div id="quill-editor" class="bg-white rounded-b-xl min-h-[260px] text-xs">
                        {!! old('content', $article->content) !!}
                    </div>
                    <input type="hidden" name="content" id="article_content" value="{{ old('content', $article->content) }}">
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-between gap-1 mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Gambar Sampul (Opsional)</label>
                        <span class="text-[11px] font-semibold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-lg border border-teal-200">
                            📐 Rekomendasi: 1200 × 630 px (Rasio 16:9 Landscape)
                        </span>
                    </div>

                    @if($article->cover_image)
                        <div class="mb-2.5 flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-200/80">
                            <img src="{{ asset('storage/' . $article->cover_image) }}" alt="" class="w-20 h-14 rounded-lg object-cover border border-slate-200 shrink-0">
                            <div class="text-xs">
                                <span class="font-bold text-slate-800 block">Gambar sampul saat ini</span>
                                <span class="text-[11px] text-slate-500 block">Pilih berkas baru di bawah jika ingin menggantinya.</span>
                            </div>
                        </div>
                    @endif

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

        <!-- Card 2: Berkas Lampiran Eksisting & Tambah Lampiran Baru -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span>📎</span> 2. Berkas Lampiran & Dokumen Terkait
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kelola dokumen tersemat pada artikel ini atau tambahkan dokumen baru.
                    </p>
                </div>

                <button type="button" @click="addNewAttachment()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 font-bold text-xs transition">
                    <span>+ Tambah Lampiran Baru</span>
                </button>
            </div>

            <!-- Existing Attachments List -->
            @if($article->attachments->isNotEmpty())
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-700">Lampiran yang Sudah Tersimpan ({{ $article->attachments->count() }}):</p>
                        <span class="text-[11px] text-slate-400">Gunakan tombol ✏️ Edit untuk mengubah nama, jenis berkas, atau link Google Drive.</span>
                    </div>

                    <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                        @foreach($article->attachments as $attach)
                            @php
                                $style = $attach->extension_style;
                                $badge = $attach->type_badge;
                            @endphp
                            <div class="p-3.5 bg-slate-50/70 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-[10px] font-black font-mono shrink-0 {{ $style['bg'] }} {{ $style['text'] }} shadow-2xs">
                                        {{ $style['name'] }}
                                    </span>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 truncate flex items-center gap-1.5">
                                            <span>{{ $attach->title }}</span>
                                            @if($attach->external_url)
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    🔗 Link Eksternal
                                                </span>
                                            @endif
                                        </h4>
                                        <p class="text-[11px] text-slate-500 font-mono flex flex-wrap items-center gap-1.5 mt-0.5">
                                            <span>{{ $attach->formatted_size }}</span>
                                            <span>•</span>
                                            <span class="text-slate-600 font-semibold">{{ $badge['label'] }}</span>
                                            @if($attach->downloads_count > 0)
                                                <span>•</span>
                                                <span>{{ $attach->downloads_count }}x diunduh</span>
                                            @endif
                                            @if($attach->external_url)
                                                <span>•</span>
                                                <span class="text-emerald-600 truncate max-w-xs">{{ $attach->external_url }}</span>
                                            @elseif($attach->file_name)
                                                <span>•</span>
                                                <span class="text-slate-400 truncate max-w-xs">{{ $attach->file_name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                    @if($attach->external_url)
                                        <a href="{{ $attach->external_url }}" target="_blank" 
                                           class="px-2.5 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition flex items-center gap-1">
                                            <span>🔗 Buka Link</span>
                                        </a>
                                    @else
                                        <a href="{{ route('articles.attachments.download', $attach->id) }}" 
                                           class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition flex items-center gap-1">
                                            <span>Unduh</span>
                                        </a>
                                    @endif

                                    <!-- Tombol Edit Lampiran -->
                                    <button type="button" 
                                            @click="openEdit({
                                                id: {{ $attach->id }},
                                                actionUrl: '{{ route('admin.articles.attachments.update', $attach->id) }}',
                                                title: '{{ addslashes($attach->title) }}',
                                                attachment_type: '{{ $attach->attachment_type }}',
                                                hasExternal: {{ $attach->external_url ? 'true' : 'false' }},
                                                externalUrl: '{{ addslashes($attach->external_url ?? '') }}',
                                                fileName: '{{ addslashes($attach->file_name ?? '') }}',
                                                currentTypeLabel: '{{ addslashes($badge['label']) }}'
                                            })"
                                            class="px-2.5 py-1.5 rounded-lg bg-teal-50 border border-teal-200 text-xs font-bold text-teal-700 hover:bg-teal-100 transition flex items-center gap-1 shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Tombol Hapus Lampiran -->
                                    <button type="submit" 
                                            form="delete-attach-{{ $attach->id }}"
                                            onclick="return confirm('Hapus lampiran ini secara permanen?')"
                                            title="Hapus Lampiran"
                                            class="p-1.5 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-50 transition border border-transparent hover:border-rose-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Repeater for NEW attachments -->
            <div class="space-y-4 pt-2">
                <template x-for="(att, index) in newAttachments" :key="index">
                    <div class="p-4 rounded-2xl bg-teal-50/50 border border-teal-200 space-y-3 relative">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-900 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-teal-200 flex items-center justify-center text-[10px] font-mono text-teal-800" x-text="index + 1"></span>
                                <span>Lampiran Baru yang Akan Ditambahkan</span>
                            </span>

                            <button type="button" @click="removeNewAttachment(index)" 
                                    class="text-rose-500 hover:text-rose-700 text-xs font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Batal</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                            <div class="sm:col-span-6">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul / Nama Dokumen</label>
                                <input type="text" :name="`new_attachments[${index}][title]`" x-model="att.title"
                                       placeholder="Misal: SOP Tata Kelola Pelatihan Mandiri"
                                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-brand-500">
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Jenis Dokumen</label>
                                <select :name="`new_attachments[${index}][attachment_type]`" x-model="att.type"
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

                        <div>
                            <div x-show="att.sourceType === 'upload'">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih File Berkas (PDF, PPTX, DOCX, XLSX, ZIP maks 20 MB)</label>
                                <input type="file" :name="`new_attachments[${index}][file]`"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:border file:border-slate-200 file:text-slate-700 hover:file:bg-slate-100">
                            </div>

                            <div x-show="att.sourceType === 'link'">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                    Tautan Berkas Google Drive / Cloud URL <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">🔗</span>
                                    <input type="url" :name="`new_attachments[${index}][external_url]`" x-model="att.external_url"
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
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }} 
                       class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                <div>
                    <span class="text-xs font-bold text-slate-900 block">Publikasikan di Portal Web</span>
                    <span class="text-[11px] text-slate-500 block">Status terbit memungkinkan artikel dan lampirannya diakses oleh seluruh ASN.</span>
                </div>
            </label>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.articles.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-brand-600 text-white text-xs font-bold transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </div>

    </form>

    <!-- Modal Edit Lampiran Eksisting -->
    <div x-show="editModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <!-- Backdrop Blur -->
        <div x-show="editModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeEdit()"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-full items-end sm:items-center justify-center p-4 text-center sm:p-0">
            <div x-show="editModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl p-6 sm:p-7 border border-slate-200 space-y-5">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-sm font-bold border border-teal-200">
                            ✏️
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900" id="modal-title">Edit Dokumen Lampiran</h3>
                            <p class="text-[11px] text-slate-500">Perbarui judul, jenis dokumen, atau ubah sumber berkas / link Google Drive.</p>
                        </div>
                    </div>
                    <button type="button" @click="closeEdit()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form Edit Attachment -->
                <form :action="editItem.actionUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Judul / Nama Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" x-model="editItem.title" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Jenis Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <select name="attachment_type" x-model="editItem.attachment_type"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <option value="regulasi">⚖️ Regulasi / Hukum</option>
                            <option value="panduan">📘 Panduan / SOP</option>
                            <option value="presentasi">📊 Materi Presentasi</option>
                            <option value="modul">📑 Modul Pembelajaran</option>
                            <option value="dokumen">📎 Dokumen Lainnya</option>
                        </select>
                    </div>

                    <!-- Pilihan Sumber Berkas -->
                    <div class="space-y-2.5 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-700">Sumber / Berkas Lampiran</label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border text-xs cursor-pointer transition"
                                   :class="editItem.sourceType === 'keep' ? 'border-teal-500 bg-teal-50/60 text-teal-900 font-bold' : 'border-slate-200 bg-slate-50/60 text-slate-600 hover:bg-slate-100'">
                                <input type="radio" name="source_type" value="keep" x-model="editItem.sourceType" class="text-teal-600 focus:ring-teal-500">
                                <span>Tetap Saat Ini</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border text-xs cursor-pointer transition"
                                   :class="editItem.sourceType === 'upload' ? 'border-teal-500 bg-teal-50/60 text-teal-900 font-bold' : 'border-slate-200 bg-slate-50/60 text-slate-600 hover:bg-slate-100'">
                                <input type="radio" name="source_type" value="upload" x-model="editItem.sourceType" class="text-teal-600 focus:ring-teal-500">
                                <span>Unggah Baru</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border text-xs cursor-pointer transition"
                                   :class="editItem.sourceType === 'link' ? 'border-teal-500 bg-teal-50/60 text-teal-900 font-bold' : 'border-slate-200 bg-slate-50/60 text-slate-600 hover:bg-slate-100'">
                                <input type="radio" name="source_type" value="link" x-model="editItem.sourceType" class="text-teal-600 focus:ring-teal-500">
                                <span>Tautan Luar / Link</span>
                            </label>
                        </div>

                        <!-- Keterangan Berkas Saat Ini -->
                        <div x-show="editItem.sourceType === 'keep'" class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs space-y-1">
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Status Berkas Saat Ini:</div>
                            <template x-if="editItem.externalUrl">
                                <div class="flex items-center gap-1.5 text-emerald-700 font-mono text-[11px] truncate">
                                    <span>🔗</span>
                                    <span x-text="editItem.externalUrl" class="truncate"></span>
                                </div>
                            </template>
                            <template x-if="!editItem.externalUrl && editItem.fileName">
                                <div class="flex items-center gap-1.5 text-slate-700 font-mono text-[11px] truncate">
                                    <span>📁</span>
                                    <span x-text="editItem.fileName" class="truncate"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Input Unggah Berkas Baru -->
                        <div x-show="editItem.sourceType === 'upload'" class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-600">Pilih Berkas Pengganti (PDF, PPTX, DOCX, XLSX, ZIP maks 20 MB)</label>
                            <input type="file" name="file"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-200 rounded-xl bg-slate-50">
                            <p class="text-[10px] text-slate-400">Berkas sebelumnya akan digantikan secara aman oleh berkas baru yang Anda unggah.</p>
                        </div>

                        <!-- Input Tautan Google Drive / Cloud -->
                        <div x-show="editItem.sourceType === 'link'" class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-600">
                                Tautan Berkas Google Drive / Cloud URL <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">🔗</span>
                                <input type="url" name="external_url" x-model="editItem.externalUrl"
                                       placeholder="https://drive.google.com/file/d/... atau https://..."
                                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            </div>
                            <p class="text-[10px] text-slate-400">Pastikan tautan Google Drive disetel ke <strong>'Siapa saja yang memiliki link' (Anyone with the link)</strong> agar ASN dapat membuka berkas.</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="closeEdit()" 
                                class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan Lampiran</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Separate hidden delete forms for existing attachments -->
    @foreach($article->attachments as $attach)
        <form id="delete-attach-{{ $attach->id }}" 
              action="{{ route('admin.articles.attachments.destroy', $attach->id) }}" 
              method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

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

        const form = document.querySelector('form[action="{{ route('admin.articles.update', $article->id) }}"]');
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
