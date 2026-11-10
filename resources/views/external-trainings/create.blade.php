@extends('layouts.app')

@section('content')
<div class="bg-white border-b border-slate-200/80 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Ajukan JP Pelatihan Eksternal
                </h1>
                <p class="text-xs text-slate-500 mt-1">Upload bukti sertifikat/kehadiran pelatihan yang diikuti di luar platform LMS KAB Berau.</p>
            </div>
            <a href="{{ route('external-trainings.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <form action="{{ route('external-trainings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Card: Informasi Pelatihan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-black">1</span>
                    Informasi Pelatihan
                </h2>
            </div>
            <div class="p-6 space-y-5">
                {{-- Nama Pelatihan --}}
                <div>
                    <label for="training_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Pelatihan <span class="text-red-500">*</span></label>
                    <input type="text" name="training_name" id="training_name" value="{{ old('training_name') }}" 
                           placeholder="Contoh: Bimtek Manajemen Kinerja ASN 2026" required
                           class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                    @error('training_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Lembaga Penyelenggara --}}
                <div>
                    <label for="institution_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Lembaga Penyelenggara <span class="text-red-500">*</span></label>
                    <input type="text" name="institution_name" id="institution_name" value="{{ old('institution_name') }}" 
                           placeholder="Contoh: BPSDM Provinsi Jawa Timur" required
                           class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                    @error('institution_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Kegiatan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="training_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="training_date" id="training_date" value="{{ old('training_date') }}" required
                               class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                        @error('training_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="training_end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Selesai <span class="text-slate-400 text-[10px] normal-case">(opsional)</span></label>
                        <input type="date" name="training_end_date" id="training_end_date" value="{{ old('training_end_date') }}"
                               class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                    </div>
                </div>

                {{-- Jumlah JP --}}
                <div class="max-w-xs">
                    <label for="jp_count" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jumlah JP (Jam Pelajaran) <span class="text-red-500">*</span></label>
                    <input type="number" name="jp_count" id="jp_count" value="{{ old('jp_count', 2) }}" min="1" max="100" required
                           class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                    <p class="text-[11px] text-slate-400 mt-1">Sesuai yang tercantum pada sertifikat pelatihan.</p>
                    @error('jp_count') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Tambahan <span class="text-slate-400 text-[10px] normal-case">(opsional)</span></label>
                    <textarea name="description" id="description" rows="3" placeholder="Informasi tambahan tentang pelatihan..."
                              class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition resize-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Card: Upload Bukti --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-black">2</span>
                    Upload Bukti Keikutsertaan
                </h2>
            </div>
            <div class="p-6 space-y-5">
                {{-- Sertifikat --}}
                <div>
                    <label for="certificate_file" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Sertifikat Pelatihan
                        <span class="text-slate-400 text-[10px] normal-case">(PDF, JPG, PNG — maks 5MB)</span>
                    </label>
                    <div class="relative">
                        <input type="file" name="certificate_file" id="certificate_file" accept=".pdf,.jpg,.jpeg,.png,.webp"
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1">
                    </div>
                    @error('certificate_file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Bukti Pendukung --}}
                <div>
                    <label for="supporting_file" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Bukti Pendukung 
                        <span class="text-slate-400 text-[10px] normal-case">(opsional — foto tanda tangan hadir, screenshot Zoom, dll.)</span>
                    </label>
                    <div class="relative">
                        <input type="file" name="supporting_file" id="supporting_file" accept=".pdf,.jpg,.jpeg,.png,.webp"
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1">
                    </div>
                    @error('supporting_file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-start gap-2 p-3 rounded-xl bg-amber-50 border border-amber-200/60 text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Submission akan diverifikasi oleh admin sebelum JP dihitung. Pastikan dokumen yang diupload jelas dan terbaca.</span>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Kirim Pengajuan JP
            </button>
            <a href="{{ route('external-trainings.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
