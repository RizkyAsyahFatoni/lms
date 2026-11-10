@extends('layouts.admin')
@section('title', 'Edit Pengajuan: ' . $opdRequest->title)
@section('page_title', 'Edit & Kirim Ulang Pengajuan')

@section('content')
<div class="max-w-3xl space-y-6">

    <div>
        <a href="{{ route('admin.opd-requests.show', $opdRequest) }}" class="text-xs text-blue-600 hover:underline font-semibold">← Kembali ke Detail</a>
        <h1 class="text-xl font-extrabold text-slate-900 mt-1">✏️ Edit & Kirim Ulang Pengajuan</h1>
        @if($opdRequest->revision_request)
        <div class="mt-3 p-4 rounded-xl bg-orange-50 border border-orange-200">
            <p class="text-xs font-bold text-orange-700">📌 Permintaan Revisi dari BKPSDM:</p>
            <p class="text-sm text-orange-800 mt-1">{{ $opdRequest->revision_request }}</p>
        </div>
        @endif
    </div>

    <form action="{{ route('admin.opd-requests.update', $opdRequest) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="font-bold text-slate-900 border-b border-slate-100 pb-2">📋 Informasi Kegiatan</h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $opdRequest->title) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Kegiatan</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('description', $opdRequest->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tujuan Pembelajaran</label>
                <textarea name="objectives" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('objectives', $opdRequest->objectives) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembelajaran <span class="text-rose-500">*</span></label>
                    <select name="learning_method" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach(['E-Learning Mandiri', 'Live Webinar', 'Pelatihan Tatap Muka', 'Bimtek', 'Workshop', 'Seminar', 'Hybrid'] as $method)
                        <option value="{{ $method }}" {{ old('learning_method', $opdRequest->learning_method) === $method ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah JP</label>
                    <input type="number" name="jp" value="{{ old('jp', $opdRequest->jp) }}" min="1"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="font-bold text-slate-900 border-b border-slate-100 pb-2">🗓️ Jadwal & Peserta</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="proposed_start_date" value="{{ old('proposed_start_date', $opdRequest->proposed_start_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="proposed_end_date" value="{{ old('proposed_end_date', $opdRequest->proposed_end_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi</label>
                    <input type="text" name="proposed_location" value="{{ old('proposed_location', $opdRequest->proposed_location) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Peserta</label>
                    <input type="number" name="estimated_participants" value="{{ old('estimated_participants', $opdRequest->estimated_participants) }}" min="1"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sasaran Peserta</label>
                <input type="text" name="target_audience" value="{{ old('target_audience', $opdRequest->target_audience) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Narasumber</label>
                <input type="text" name="speaker_name" value="{{ old('speaker_name', $opdRequest->speaker_name) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pendukung</label>
                <textarea name="supporting_notes" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('supporting_notes', $opdRequest->supporting_notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('admin.opd-requests.show', $opdRequest) }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-100 transition">
                ← Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                📤 Kirim Ulang ke BKPSDM
            </button>
        </div>
    </form>

</div>
@endsection
