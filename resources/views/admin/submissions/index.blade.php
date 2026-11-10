@extends('layouts.admin')

@section('page_title', 'Review & Penilaian Penugasan')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Review & Penilaian Penugasan</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $submissions->total() }} Pengumpulan
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Periksa berkas tugas studi kasus yang dikirim peserta, beri nilai dan catatan evaluasi.</p>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" x-data="{ activeModal: null, activeScore: 90, activeFeedback: '' }">
        
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Hasil Pengerjaan Peserta</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $submissions->count() }} dari {{ $submissions->total() }} pengumpulan</span>
        </div>

        @if($submissions->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                    📥
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum ada pengumpulan penugasan</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Saat peserta mengunggah berkas solusi tugas pada ruang belajar, berkas akan muncul di sini untuk dinilai.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Peserta</th>
                            <th class="py-3 px-4">Modul & Kursus</th>
                            <th class="py-3 px-4">Berkas Tugas</th>
                            <th class="py-3 px-4">Waktu Kirim</th>
                            <th class="py-3 px-4">Status & Nilai</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center">
                                            {{ substr($sub->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $sub->user->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $sub->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $sub->assignment->lesson->title ?? 'Tugas' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $sub->assignment->lesson->course->title ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($sub->file_url)
                                        <a href="{{ $sub->file_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium text-[11px] transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            <span>Buka Berkas</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Tanpa lampiran berkas</span>
                                    @endif

                                    @if($sub->notes)
                                        <p class="text-[11px] text-slate-600 mt-1 max-w-xs truncate" title="{{ $sub->notes }}">
                                            "{{ $sub->notes }}"
                                        </p>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                    {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($sub->status === 'graded')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span>✓ Dinilai:</span>
                                            <span class="text-xs">{{ $sub->score }}/100</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Penilaian
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button @click="activeModal = {{ $sub->id }}; activeScore = {{ $sub->score ?: 90 }}; activeFeedback = '{{ addslashes($sub->feedback ?: 'Pekerjaan sangat baik dan sesuai standar integrasi.') }}'"
                                            type="button" 
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $sub->status === 'graded' ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-brand-600 text-white hover:bg-brand-700' }} transition">
                                        {{ $sub->status === 'graded' ? 'Edit Nilai' : 'Beri Nilai' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $submissions->links() }}
            </div>
        @endif

        <!-- GRADING MODAL -->
        <div x-show="activeModal !== null" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            
            <div @click.away="activeModal = null" 
                 x-show="activeModal !== null"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Penilaian & Evaluasi Tugas</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Input nilai angka serta catatan evaluasi untuk peserta.</p>
                    </div>
                    <button @click="activeModal = null" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- We dynamically set the form action in Alpine -->
                <form :action="'/admin/submissions/' + activeModal + '/grade'" method="POST" class="p-5 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Skor Penilaian (0 - 100) <span class="text-red-500">*</span></label>
                        <input type="number" name="score" x-model="activeScore" min="0" max="100" required 
                               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-bold text-slate-900">
                        <span class="text-[11px] text-slate-400">Skor minimum kelulusan modul adalah 70.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Umpan Balik Instruktur (Feedback)</label>
                        <textarea name="feedback" rows="3" x-model="activeFeedback" placeholder="Tuliskan catatan apresiasi atau saran perbaikan teknis..."
                                  class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button @click="activeModal = null" type="button" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                            Simpan Nilai & Feedback
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</div>
@endsection
