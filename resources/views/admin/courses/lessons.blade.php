@extends('layouts.admin')

@section('page_title', 'Silabus: ' . Str::limit($course->title, 25))

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false }">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kurikulum & Modul Pembelajaran</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $course->lessons->count() }} Modul
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Kursus: <span class="font-bold text-slate-800">{{ $course->title }}</span> &bull; Kategori: {{ $course->category->name }}
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.courses.certificate-builder', $course->id) }}"
               class="px-3.5 py-2 rounded-xl border border-teal-200 bg-teal-50 text-xs font-semibold text-teal-700 hover:bg-teal-100 transition flex items-center gap-1.5 shadow-2xs">
                <span>🎨 Certificate Builder</span>
            </a>
            <a href="{{ route('classroom.show', [$course->slug, $course->lessons->first()->slug ?? '']) }}" target="_blank"
               class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Ruang Belajar</span>
            </a>
            <button @click="addModalOpen = true" 
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah Modul Materi</span>
            </button>
        </div>
    </div>

    <!-- ADD LESSON MODAL -->
    <div x-show="addModalOpen" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="addModalOpen = false" 
             x-show="addModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-2xl rounded-2xl shadow-xl border border-slate-200 overflow-hidden"
             x-data="{ lessonType: 'video' }">
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Tambah Materi Pembelajaran Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Mendukung Video, PDF, Tautan Eksternal, Pre-Test, Post-Test, dan Assignment.</p>
                </div>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.courses.lessons.store', $course->id) }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Type Selector Badges -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tipe Format Materi <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-6 gap-2">
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border cursor-pointer transition text-center"
                               :class="lessonType === 'video' ? 'bg-blue-50 border-blue-500 text-blue-700 ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="type" value="video" x-model="lessonType" class="sr-only">
                            <span class="text-lg mb-1">📹</span>
                            <span class="text-[11px] font-bold leading-tight">Video</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border cursor-pointer transition text-center"
                               :class="lessonType === 'pdf' ? 'bg-rose-50 border-rose-500 text-rose-700 ring-2 ring-rose-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="type" value="pdf" x-model="lessonType" class="sr-only">
                            <span class="text-lg mb-1">📄</span>
                            <span class="text-[11px] font-bold leading-tight">Dokumen PDF</span>
                        </label>
                        {{-- KAK Poin 13: Tautan Pembelajaran --}}
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border cursor-pointer transition text-center"
                               :class="lessonType === 'link' ? 'bg-orange-50 border-orange-500 text-orange-700 ring-2 ring-orange-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="type" value="link" x-model="lessonType" class="sr-only">
                            <span class="text-lg mb-1">🔗</span>
                            <span class="text-[11px] font-bold leading-tight">Tautan</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border cursor-pointer transition text-center"
                               :class="lessonType === 'pre_test' ? 'bg-teal-50 border-teal-500 text-teal-700 ring-2 ring-teal-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="type" value="pre_test" x-model="lessonType" class="sr-only">
                            <span class="text-lg mb-1">📝</span>
                            <span class="text-[11px] font-bold leading-tight">Pre-Test</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border cursor-pointer transition text-center"
                               :class="lessonType === 'post_test' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 ring-2 ring-indigo-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="type" value="post_test" x-model="lessonType" class="sr-only">
                            <span class="text-lg mb-1">🎓</span>
                            <span class="text-[11px] font-bold leading-tight">Post-Test</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border cursor-pointer transition text-center"
                               :class="lessonType === 'assignment' ? 'bg-purple-50 border-purple-500 text-purple-700 ring-2 ring-purple-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="type" value="assignment" x-model="lessonType" class="sr-only">
                            <span class="text-lg mb-1">💼</span>
                            <span class="text-[11px] font-bold leading-tight">Assignment</span>
                        </label>
                    </div>
                </div>

                <!-- Section & Lesson Title -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Bab / Seksi <span class="text-red-500">*</span></label>
                        <input type="text" name="section_title" required placeholder="Contoh: Bab 1: Fondasi Teknis" value="Bab 1: Pengenalan & Regulasi"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Estimasi Durasi (Menit) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" value="15" min="1" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Materi Pembelajaran <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Tata Cara Pembuatan FHIR Resource Enrolment"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                </div>

                <!-- Dynamic inputs based on type -->
                <div x-show="lessonType === 'video'" class="p-3 bg-blue-50/60 rounded-xl border border-blue-100 space-y-2">
                    <label class="block text-xs font-bold text-blue-900">URL Video Pembelajaran (YouTube / MP4)</label>
                    <input type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full px-3 py-2 text-xs bg-white border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <p class="text-[11px] text-blue-600">Video akan dimuat responsif dalam player ruang belajar.</p>
                </div>

                <div x-show="lessonType === 'pdf'" class="p-3 bg-rose-50/60 rounded-xl border border-rose-100 space-y-2">
                    <label class="block text-xs font-bold text-rose-900">URL Berkas Dokumen PDF</label>
                    <input type="url" name="pdf_url" placeholder="https://contoh.com/dokumen-petunjuk.pdf"
                           class="w-full px-3 py-2 text-xs bg-white border border-rose-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    <p class="text-[11px] text-rose-600">Peserta dapat membaca langsung melalui viewer terintegrasi di browser.</p>
                </div>

                <div x-show="lessonType === 'pre_test' || lessonType === 'post_test'" class="p-3 bg-teal-50/60 rounded-xl border border-teal-100 space-y-1">
                    <p class="text-xs font-bold text-teal-900">Konfigurasi Evaluasi Ujian</p>
                    <p class="text-[11px] text-teal-700">Sistem otomatis men-generate template soal pilihan ganda standar dengan ambang batas kelulusan 70%.</p>
                </div>

                {{-- KAK Poin 13: Tautan Pembelajaran Eksternal --}}
                <div x-show="lessonType === 'link'" class="p-3 bg-orange-50/60 rounded-xl border border-orange-100 space-y-2">
                    <label class="block text-xs font-bold text-orange-900">🔗 Tautan Pembelajaran Eksternal (KAK Poin 13)</label>
                    <input type="url" name="external_url" placeholder="https://lmsspeserta.go.id/materi/001 atau https://youtube.com/..."
                           class="w-full px-3 py-2 text-xs bg-white border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    <p class="text-[11px] text-orange-700">Peserta akan diarahkan ke tautan eksternal ini. Cocok untuk referensi e-Gov, portal belajar instansi lain, atau sumber resmi.</p>
                </div>

                <div x-show="lessonType === 'assignment'" class="p-3 bg-purple-50/60 rounded-xl border border-purple-100 space-y-1">
                    <p class="text-xs font-bold text-purple-900">Instruksi Penugasan Praktik</p>
                    <p class="text-[11px] text-purple-700">Siswa akan diberikan form upload berkas solusi dan catatan penyelesaian yang masuk ke antrean penilaian admin.</p>
                </div>

                <!-- Text Content / Notes -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Uraian / Ringkasan Materi / Panduan</label>
                    <textarea name="content" rows="3" placeholder="Tuliskan catatan materi atau instruksi khusus untuk peserta..."
                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"></textarea>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_free_preview" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>Buka sebagai Modul Pratinjau Gratis (Free Preview)</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button @click="addModalOpen = false" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                            Simpan Modul
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
    
    <!-- Table of existing lessons -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Struktur Silabus (Urutan Belajar)</h3>
                <p class="text-xs text-slate-500">Semua materi yang akan dipelajari peserta dari awal hingga kelulusan sertifikat.</p>
            </div>
            <span class="text-xs text-slate-400 font-mono">Total durasi: {{ $course->duration_hours }} Jam</span>
        </div>

        @if($course->lessons->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                    📚
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum ada modul materi</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">Klik tombol "Tambah Modul / Materi" di atas untuk mulai menyusun silabus video, PDF, kuis, atau tugas.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($course->lessons->sortBy('order') as $lesson)
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/70 transition">
                        
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200/80 text-slate-600 font-mono font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                {{ $lesson->order }}
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="text-[11px] font-semibold text-slate-400">{{ $lesson->section_title }}</span>
                                    
                                    <!-- Lesson Type Badges -->
                                    @if($lesson->type === 'video')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            📹 Video
                                        </span>
                                    @elseif($lesson->type === 'pdf')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            📄 Dokumen PDF
                                        </span>
                                    @elseif($lesson->type === 'pre_test')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            📝 Pre-Test (Evaluasi Awal)
                                        </span>
                                    @elseif($lesson->type === 'post_test')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            🎓 Post-Test (Uji Kelulusan)
                                        </span>
                                    @elseif($lesson->type === 'assignment')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            💼 Penugasan Praktik
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            📖 Bacaan
                                        </span>
                                    @endif

                                    @if($lesson->is_free_preview)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Free Preview
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-sm font-bold text-slate-900">{{ $lesson->title }}</h4>

                                <div class="flex items-center gap-4 text-[11px] text-slate-500 mt-1">
                                    <span>⏱️ {{ $lesson->duration_minutes }} Menit</span>
                                    @if($lesson->video_url)
                                        <span class="truncate max-w-[240px] text-blue-600 font-mono">{{ $lesson->video_url }}</span>
                                    @elseif($lesson->pdf_url)
                                        <span class="truncate max-w-[240px] text-rose-600 font-mono">{{ $lesson->pdf_url }}</span>
                                    @elseif($lesson->quiz)
                                        <span class="text-teal-700 font-semibold">Ambang batas: {{ $lesson->quiz->passing_score }}%</span>
                                    @elseif($lesson->assignment)
                                        <span class="text-purple-700 font-semibold">Skor maks: {{ $lesson->assignment->max_score }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <a href="{{ route('classroom.show', [$course->slug, $lesson->slug]) }}" target="_blank"
                               class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Preview</span>
                            </a>
                            <form method="POST" action="{{ route('admin.courses.lessons.destroy', $lesson->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-red-200 text-xs font-medium text-red-600 hover:bg-red-50 transition" title="Hapus Modul">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
