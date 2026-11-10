@extends('layouts.admin')

@section('page_title', 'Report Builder Sederhana')

@section('content')
<div class="space-y-6 pb-12" 
     x-data="{
         entity: '{{ $entity }}',
         selectAll(cols) {
             const checkboxes = document.querySelectorAll('input[name=\'columns[]\']');
             checkboxes.forEach(cb => cb.checked = true);
         },
         resetDefault(defaults) {
             const checkboxes = document.querySelectorAll('input[name=\'columns[]\']');
             checkboxes.forEach(cb => {
                 cb.checked = defaults.includes(cb.value);
             });
         }
     }">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.index') }}" class="text-xs text-brand-600 font-bold hover:underline flex items-center gap-1">
                    &larr; Pusat Laporan
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs text-slate-500 font-semibold">Report Builder Dinamis</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                Report Builder Sederhana
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pilih parameter entitas, centang kolom yang ingin ditampilkan, terapkan filter, dan langsung ekspor ke Excel atau format cetak PDF resmi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
                Katalog 10 Laporan
            </a>
        </div>
    </div>

    <!-- Filter & Configuration Form -->
    <form id="builder-form" action="{{ route('admin.reports.builder') }}" method="GET" class="space-y-6">
        
        <!-- Step 1: Pilih Sumber Data / Entitas -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">1</span>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Pilih Entitas Sumber Data</h2>
                </div>
                <span class="text-[11px] text-slate-400">Pilih modul data yang ingin ditarik</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5">
                @foreach($entityDefinitions as $key => $def)
                    <label class="flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition text-center select-none {{ $entity === $key ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200/80' }}">
                        <input type="radio" name="entity" value="{{ $key }}" class="sr-only" onchange="this.form.submit()" {{ $entity === $key ? 'checked' : '' }}>
                        <span class="text-xl mb-1">{{ $def['icon'] }}</span>
                        <span class="text-[11px] font-bold leading-tight">{{ $def['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Step 2: Pilih Kolom yang Ditampilkan -->
        @php
            $currentDef = $entityDefinitions[$entity] ?? null;
            $allColumns = $currentDef ? $currentDef['columns'] : [];
            $defaultCols = $currentDef ? $currentDef['default_columns'] : [];
        @endphp

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">2</span>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Pilih Kolom Laporan yang Ditampilkan</h2>
                        <p class="text-[11px] text-slate-500">Centang atribut yang ingin dimunculkan dalam tabel & hasil ekspor</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <button type="button" @click="selectAll()" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition text-[11px]">
                        Pilih Semua
                    </button>
                    <button type="button" @click="resetDefault({{ json_encode($defaultCols) }})" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition text-[11px]">
                        Kolom Standar
                    </button>
                </div>
            </div>

            <!-- Checkbox Grid Kolom -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 pt-1">
                @foreach($allColumns as $colKey => $colLabel)
                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition select-none">
                        <input type="checkbox" name="columns[]" value="{{ $colKey }}" 
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300 mt-0.5"
                               {{ in_array($colKey, $selectedColumns) ? 'checked' : '' }}>
                        <div class="flex-1 min-w-0">
                            <span class="block text-xs font-bold text-slate-800 leading-tight">{{ $colLabel }}</span>
                            <span class="block text-[10px] font-mono text-slate-400 mt-0.5">({{ $colKey }})</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Step 3: Parameter Filter & Pencarian -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">3</span>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Parameter Filter & Rentang Data</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Pencarian Kata Kunci</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="NIP, nama, atau kata kunci..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- OPD Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Perangkat Daerah (OPD)</label>
                    <select name="opd_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="all">Semua Perangkat Daerah</option>
                        @foreach($availableOpds as $opd)
                            <option value="{{ $opd }}" {{ ($filters['opd_name'] ?? '') === $opd ? 'selected' : '' }}>
                                {{ $opd }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tahun Periode -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Tahun Anggaran / Periode</label>
                    <select name="year" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ ($filters['year'] ?? date('Y')) == $y ? 'selected' : '' }}>
                                Tahun {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Batas Baris -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Jumlah Data Ditampilkan</label>
                    <select name="limit" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="50" {{ ($filters['limit'] ?? '') == '50' ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ ($filters['limit'] ?? '100') == '100' ? 'selected' : '' }}>100 Baris</option>
                        <option value="250" {{ ($filters['limit'] ?? '') == '250' ? 'selected' : '' }}>250 Baris</option>
                        <option value="all" {{ ($filters['limit'] ?? '') == 'all' ? 'selected' : '' }}>Semua Data</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500">
                    Menampilkan pratinjau <strong class="text-slate-900">{{ count($dataResult['rows']) }}</strong> baris data.
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Perbarui Pratinjau</span>
                    </button>

                    <!-- Export Excel Button -->
                    <button type="submit" formaction="{{ route('admin.reports.export-excel') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Ekspor Excel (.csv)</span>
                    </button>

                    <!-- Print PDF Button -->
                    <button type="submit" formaction="{{ route('admin.reports.print') }}" formtarget="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Dokumen PDF</span>
                    </button>
                </div>
            </div>
        </div>

    </form>

    <!-- Step 4: Live Table Data Preview -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden space-y-0">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="text-sm">👁️</span>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Pratinjau Hasil Laporan: {{ $currentDef['label'] }}
                </h3>
            </div>
            <span class="text-[11px] font-mono text-slate-500">
                Kolom Terpilih: {{ count($selectedColumns) }} kolom
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3 w-12 text-center text-slate-400">#</th>
                        @foreach($selectedColumns as $colKey)
                            <th class="p-3 whitespace-nowrap">
                                {{ $allColumns[$colKey] ?? $colKey }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dataResult['rows'] as $index => $row)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3 text-center text-slate-400 font-mono text-[11px]">
                                {{ $index + 1 }}
                            </td>
                            @foreach($selectedColumns as $colKey)
                                <td class="p-3 whitespace-nowrap text-slate-800">
                                    @php
                                        $val = $row[$colKey] ?? '-';
                                    @endphp
                                    @if($colKey === 'status' || $colKey === 'jp_status')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ str_contains(strtolower($val), 'memenuhi') || str_contains(strtolower($val), 'terbit') || str_contains(strtolower($val), 'aktif') || str_contains(strtolower($val), 'approved') || str_contains(strtolower($val), 'hadir') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                            {{ $val }}
                                        </span>
                                    @elseif($colKey === 'verify_url')
                                        <a href="{{ $val }}" target="_blank" class="text-brand-600 hover:underline font-bold">
                                            Buka Sertifikat &rarr;
                                        </a>
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($selectedColumns) + 1 }}" class="p-8 text-center text-slate-400">
                                Tidak ada data yang sesuai dengan kombinasi filter dan kolom yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
