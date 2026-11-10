@extends('layouts.admin')
@section('title', 'Ajukan Kegiatan OPD')
@section('page_title', 'Form Pengajuan Kegiatan')

@section('content')
<div class="max-w-3xl space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">📝 Pengajuan Kegiatan Diklat OPD</h1>
        <p class="text-sm text-slate-500 mt-1">Isi formulir berikut untuk mengusulkan kegiatan diklat kepada BKPSDM. Kegiatan akan dipublikasikan setelah disetujui.</p>
    </div>

    <form action="{{ route('admin.opd-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Info Dasar --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="font-bold text-slate-900 border-b border-slate-100 pb-2">📋 Informasi Kegiatan</h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="Contoh: Bimtek Pengelolaan Aset Daerah Tahun 2026"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('title') border-rose-400 @enderror">
                @error('title')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Kegiatan</label>
                <textarea name="description" rows="4" placeholder="Uraian singkat tentang kegiatan, latar belakang, dan manfaatnya..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tujuan Pembelajaran</label>
                <textarea name="objectives" rows="3" placeholder="Tuliskan tujuan pembelajaran yang ingin dicapai peserta..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('objectives') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembelajaran <span class="text-rose-500">*</span></label>
                    <select name="learning_method" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Pilih Metode --</option>
                        <option value="E-Learning Mandiri" {{ old('learning_method') === 'E-Learning Mandiri' ? 'selected' : '' }}>E-Learning Mandiri</option>
                        <option value="Live Webinar" {{ old('learning_method') === 'Live Webinar' ? 'selected' : '' }}>Live Webinar</option>
                        <option value="Pelatihan Tatap Muka" {{ old('learning_method') === 'Pelatihan Tatap Muka' ? 'selected' : '' }}>Pelatihan Tatap Muka (In-Class)</option>
                        <option value="Bimtek" {{ old('learning_method') === 'Bimtek' ? 'selected' : '' }}>Bimtek</option>
                        <option value="Workshop" {{ old('learning_method') === 'Workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="Seminar" {{ old('learning_method') === 'Seminar' ? 'selected' : '' }}>Seminar</option>
                        <option value="Hybrid" {{ old('learning_method') === 'Hybrid' ? 'selected' : '' }}>Hybrid (Online + Tatap Muka)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Jam Pelajaran (JP)</label>
                    <input type="number" name="jp" value="{{ old('jp') }}" min="1" max="500"
                           placeholder="Contoh: 8"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
        </div>

        {{-- Jadwal & Peserta --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="font-bold text-slate-900 border-b border-slate-100 pb-2">🗓️ Jadwal & Peserta</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai (Usulan)</label>
                    <input type="date" name="proposed_start_date" value="{{ old('proposed_start_date') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Selesai (Usulan)</label>
                    <input type="date" name="proposed_end_date" value="{{ old('proposed_end_date') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi / Tempat</label>
                    <input type="text" name="proposed_location" value="{{ old('proposed_location') }}"
                           placeholder="Contoh: Aula BKPSDM Berau / Online Zoom"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Jumlah Peserta</label>
                    <input type="number" name="estimated_participants" value="{{ old('estimated_participants') }}" min="1"
                           placeholder="Contoh: 30"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sasaran Peserta</label>
                <input type="text" name="target_audience" value="{{ old('target_audience') }}"
                       placeholder="Contoh: ASN Dinas Keuangan Kabupaten Berau, minimal golongan III/a"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        {{-- Narasumber & Anggaran --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="font-bold text-slate-900 border-b border-slate-100 pb-2">👨‍🏫 Narasumber & Anggaran</h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Narasumber</label>
                <input type="text" name="speaker_name" value="{{ old('speaker_name') }}"
                       placeholder="Nama narasumber yang diusulkan (boleh lebih dari satu, pisah koma)"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sumber Anggaran</label>
                <input type="text" name="budget_source" value="{{ old('budget_source') }}"
                       placeholder="Contoh: DPA OPD 2026, APBD Kabupaten Berau"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pendukung</label>
                <textarea name="supporting_notes" rows="3" placeholder="Informasi tambahan yang relevan untuk reviewer BKPSDM..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('supporting_notes') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Lampiran (TOR, Proposal, dll)</label>
                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <p class="text-xs text-slate-400 mt-1">Format: PDF, Word, Excel. Maks. 5 MB</p>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('admin.opd-requests.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-100 transition">
                ← Kembali
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                📤 Kirim Pengajuan ke BKPSDM
            </button>
        </div>
    </form>

</div>
@endsection
