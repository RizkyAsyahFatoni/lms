@extends('layouts.admin')

@section('page_title', 'Manajemen IDP (Individual Development Plan)')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen IDP ASN</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">INDIVIDUAL DEVELOPMENT PLAN</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Upload file Excel berisi rencana pengembangan ASN per tahun. Data IDP akan muncul otomatis di dashboard ASN sesuai NIP masing-masing.</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Statistik Ringkas --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Total NIP Terdaftar</p>
            <p class="text-2xl font-black text-indigo-600 tracking-tight mt-1">{{ number_format($totalNip) }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Total Materi IDP</p>
            <p class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($totalMateri) }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Batch Upload</p>
            <p class="text-2xl font-black text-amber-600 tracking-tight mt-1">{{ $batches->count() }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Rata-rata Materi/ASN</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tight mt-1">{{ $totalNip > 0 ? number_format($totalMateri / $totalNip, 1) : '—' }}</p>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-800 mb-1">📥 Upload File IDP</h2>
        <p class="text-xs text-slate-500 mb-5">Format Excel (.xlsx / .xls): <strong>Kolom A</strong> = NIP | <strong>Kolom B</strong> = Nama ASN | <strong>Kolom C</strong> = Tahun (1-5) | <strong>Kolom D</strong> = Judul Materi. Baris pertama adalah header (akan dilewati).</p>

        <form action="{{ route('admin.idp.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">File Excel IDP <span class="text-red-500">*</span></label>
                    <input type="file" name="idp_file" accept=".xlsx,.xls,.csv" required
                        class="block w-full text-xs text-slate-600 border border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mode Upload</label>
                    <select name="upload_mode" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="replace">🔄 Timpa (Replace per NIP)</option>
                        <option value="append">➕ Tambahkan (Append)</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Upload & Proses IDP
                </button>
                <a href="#template" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                    📋 Lihat Format Template
                </a>
            </div>
        </form>
    </div>

    {{-- Template Format Info --}}
    <div id="template" class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <h3 class="text-xs font-bold text-amber-800 mb-3">📋 Format Template Excel IDP</h3>
        <div class="overflow-x-auto">
            <table class="text-xs text-slate-700 border-collapse min-w-full">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border border-amber-200 px-3 py-2 text-left font-bold">Kolom A — NIP</th>
                        <th class="border border-amber-200 px-3 py-2 text-left font-bold">Kolom B — Nama ASN</th>
                        <th class="border border-amber-200 px-3 py-2 text-left font-bold">Kolom C — Tahun (1-5)</th>
                        <th class="border border-amber-200 px-3 py-2 text-left font-bold">Kolom D — Judul Materi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr class="text-slate-400 italic"><td class="border border-amber-100 px-3 py-1.5" colspan="4">← Baris 1 adalah header, akan dilewati otomatis →</td></tr>
                    <tr>
                        <td class="border border-amber-100 px-3 py-1.5">198501012010011001</td>
                        <td class="border border-amber-100 px-3 py-1.5">Ahmad Fauzi</td>
                        <td class="border border-amber-100 px-3 py-1.5">1</td>
                        <td class="border border-amber-100 px-3 py-1.5">BerAKHLAK dan Nilai Dasar ASN</td>
                    </tr>
                    <tr class="bg-amber-50/50">
                        <td class="border border-amber-100 px-3 py-1.5">198501012010011001</td>
                        <td class="border border-amber-100 px-3 py-1.5">Ahmad Fauzi</td>
                        <td class="border border-amber-100 px-3 py-1.5">1</td>
                        <td class="border border-amber-100 px-3 py-1.5">Etika dan Integritas Pelayan Publik</td>
                    </tr>
                    <tr>
                        <td class="border border-amber-100 px-3 py-1.5">198501012010011001</td>
                        <td class="border border-amber-100 px-3 py-1.5">Ahmad Fauzi</td>
                        <td class="border border-amber-100 px-3 py-1.5">2</td>
                        <td class="border border-amber-100 px-3 py-1.5">Digitalisasi Pelayanan Publik</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-[11px] text-amber-700 mt-3">💡 Satu ASN bisa memiliki banyak baris dengan NIP yang sama (1 baris = 1 materi). Kolom C (Tahun) diisi angka 1 sampai 5 sesuai tahun IDP ASN tersebut.</p>
    </div>

    {{-- Riwayat Batch Upload --}}
    @if($batches->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">📦 Riwayat Batch Upload</h2>
            <form action="{{ route('admin.idp.destroy-all') }}" method="POST" onsubmit="return confirm('PERHATIAN! Ini akan menghapus SELURUH data IDP dari semua ASN. Yakin lanjut?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-lg text-[11px] font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition">🗑️ Reset Semua Data IDP</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Batch ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Diupload Oleh</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-500">Total Baris</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Waktu Upload</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($batches as $batch)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-4 py-3 font-mono text-indigo-600 font-semibold">{{ $batch->batch_id }}</td>
                        <td class="px-4 py-3">{{ $batch->uploader?->name ?? 'Admin' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">{{ number_format($batch->total_rows) }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ \Carbon\Carbon::parse($batch->uploaded_at)->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.idp.destroy-batch', $batch->batch_id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus semua data batch {{ $batch->batch_id }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition">Hapus Batch</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Preview Data IDP --}}
    @if($plans->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">📋 Preview Data IDP (200 baris terakhir)</h2>
            <p class="text-[11px] text-slate-400 mt-0.5">Diurutkan berdasarkan NIP dan Tahun IDP</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">NIP</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Nama ASN</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-500">Tahun IDP</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Judul Materi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($plans as $plan)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-4 py-2.5 font-mono text-slate-800">{{ $plan->nip }}</td>
                        <td class="px-4 py-2.5">{{ $plan->nama_asn ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">Tahun {{ $plan->tahun }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-slate-700">{{ $plan->judul_materi }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-dashed border-slate-200">
        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4 text-2xl">📋</div>
        <p class="text-sm font-semibold text-slate-600">Belum ada data IDP</p>
        <p class="text-xs text-slate-400 mt-1">Upload file Excel IDP di atas untuk memulai.</p>
    </div>
    @endif

</div>
@endsection
