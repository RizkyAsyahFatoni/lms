@extends('layouts.admin')

@section('page_title', 'Rekapitulasi Presensi Digital')

@section('content')
<div class="space-y-6" x-data="{
    statusModal: false,
    selectedAttendance: { id: null, name: '', status: 'present', notes: '' }
}">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Presensi Digital Kegiatan & Pembelajaran</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $attendances->total() }} Catatan Kehadiran
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan kehadiran digital berbasis QR Code dan Tombol Presensi dengan kalkulasi durasi & status keterlambatan.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.attendances.sessions') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>⚙️</span>
                <span>Kelola Sesi & QR Code</span>
            </a>
            <a href="{{ route('admin.attendances.export', ['session' => request('session_id')]) }}" 
               class="px-3.5 py-1.5 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Ekspor CSV (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kehadiran</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
            <p class="text-[10px] text-slate-400">Semua Kegiatan</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-emerald-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Tepat Waktu</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['present']) }}</p>
            <p class="text-[10px] text-slate-400">{{ $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100) : 0 }}% dari total</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-amber-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Terlambat</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['late']) }}</p>
            <p class="text-[10px] text-slate-400">Melewati toleransi</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-blue-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Izin / Sakit</p>
            <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['excused']) }}</p>
            <p class="text-[10px] text-slate-400">Dispensasi resmi</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-teal-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-teal-600">Memenuhi Durasi</p>
            <p class="text-2xl font-black text-teal-600 mt-1">{{ number_format($stats['qualified']) }}</p>
            <p class="text-[10px] text-slate-400">Sesuai KAK Konfigurasi</p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
        <form action="{{ route('admin.attendances.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama peserta, NIP, OPD, atau judul kegiatan..."
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <!-- Filter Sesi -->
                <select name="session_id" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none font-medium text-slate-700 max-w-[200px] truncate">
                    <option value="">Semua Sesi Kegiatan</option>
                    @foreach($sessions as $sess)
                        <option value="{{ $sess->id }}" {{ request('session_id') == $sess->id ? 'selected' : '' }}>
                            {{ $sess->title }} ({{ $sess->session_code }})
                        </option>
                    @endforeach
                </select>

                <!-- Filter Status -->
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none font-medium text-slate-700">
                    <option value="">Semua Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>🟢 Hadir Tepat Waktu</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>🟡 Terlambat</option>
                    <option value="excused" {{ request('status') === 'excused' ? 'selected' : '' }}>🔵 Izin Dinas</option>
                    <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>🟣 Sakit</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>🔴 Alpa</option>
                </select>

                <!-- Filter Metode -->
                <select name="method" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none font-medium text-slate-700">
                    <option value="">Semua Metode</option>
                    <option value="qr_code" {{ request('method') === 'qr_code' ? 'selected' : '' }}>📱 QR Code</option>
                    <option value="button" {{ request('method') === 'button' ? 'selected' : '' }}>🖱️ Tombol Presensi</option>
                    <option value="manual" {{ request('method') === 'manual' ? 'selected' : '' }}>✍️ Input Manual</option>
                </select>

                <!-- Filter Tanggal -->
                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                       class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none font-medium text-slate-700">

                <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition">
                    Filter
                </button>
                @if(request('q') || request('session_id') || request('status') || request('method') || request('date'))
                    <a href="{{ route('admin.attendances.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:bg-slate-100 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Attendances -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Kehadiran Peserta Terverifikasi</h3>
            <span class="text-xs text-slate-500 font-mono">Ditemukan {{ $attendances->total() }} catatan</span>
        </div>

        @if($attendances->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                <div class="text-3xl mb-2">📋</div>
                <p class="font-bold text-slate-600">Belum ada catatan presensi peserta yang sesuai filter.</p>
                <p class="text-slate-400 mt-1">Data kehadiran akan otomatis terekam saat peserta melakukan scan QR Code atau menekan tombol presensi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Peserta & NIP</th>
                            <th class="py-3 px-4">Instansi / OPD</th>
                            <th class="py-3 px-4">Kegiatan Pembelajaran</th>
                            <th class="py-3 px-4">Tanggal & Waktu</th>
                            <th class="py-3 px-4 text-center">Status Kehadiran</th>
                            <th class="py-3 px-4 text-center">Metode</th>
                            <th class="py-3 px-4 text-center">Durasi Hadir</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($attendances as $att)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Peserta -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ substr($att->participant_name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $att->participant_name }}</div>
                                            @if($att->participant_nip)
                                                <div class="text-[10px] font-mono text-slate-400">NIP: {{ $att->participant_nip }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- OPD -->
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div class="font-semibold text-slate-800 max-w-xs truncate">{{ $att->participant_opd ?: '-' }}</div>
                                    @if($att->participant_jabatan)
                                        <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $att->participant_jabatan }}</div>
                                    @endif
                                </td>

                                <!-- Kegiatan -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 max-w-xs truncate">{{ $att->activity_title }}</div>
                                    @if($att->session_code)
                                        <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-slate-100 text-slate-500">
                                            {{ $att->session_code }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Tanggal & Waktu -->
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $att->date ? $att->date->format('d M Y') : '-' }}</div>
                                    <div class="text-[10px] font-mono text-slate-400">
                                        {{ $att->check_in_at ? $att->check_in_at->format('H:i:s') : $att->time }} WIB
                                    </div>
                                </td>

                                <!-- Status Kehadiran (KAK) -->
                                <td class="py-3.5 px-4 text-center">
                                    {!! $att->status_badge !!}
                                </td>

                                <!-- Metode (QR / Tombol) -->
                                <td class="py-3.5 px-4 text-center">
                                    {!! $att->method_badge !!}
                                </td>

                                <!-- Durasi / Kehadiran Sesuai Konfigurasi (KAK) -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-900">{{ $att->formatted_duration }}</span>
                                        @if($att->is_duration_qualified)
                                            <span class="block text-[9px] font-bold text-emerald-600">✓ Memenuhi Target</span>
                                        @else
                                            <span class="block text-[9px] font-bold text-rose-500">✕ Kurang Durasi</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="selectedAttendance = { id: {{ $att->id }}, name: '{{ addslashes($att->participant_name) }}', status: '{{ $att->status }}', notes: '{{ addslashes($att->notes ?? '') }}' }; statusModal = true"
                                                class="px-2 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                                title="Ubah Status Kehadiran">
                                            ✏️ Status
                                        </button>
                                        <form action="{{ route('admin.attendances.destroy', $att) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus catatan presensi peserta {{ addslashes($att->participant_name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                    title="Hapus Catatan">
                                                ✕
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL EDIT STATUS KEHADIRAN -->
    <div x-show="statusModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="statusModal = false" 
             x-show="statusModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Sesuaikan Status Kehadiran</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="selectedAttendance.name"></p>
                </div>
                <button @click="statusModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/attendances/' + selectedAttendance.id + '/status'" method="POST" class="p-5 space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
                    <select name="status" x-model="selectedAttendance.status" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold focus:bg-white transition">
                        <option value="present">🟢 Hadir Tepat Waktu</option>
                        <option value="late">🟡 Terlambat</option>
                        <option value="excused">🔵 Izin Dinas / Dispensasi</option>
                        <option value="sick">🟣 Sakit</option>
                        <option value="absent">🔴 Alpa / Tidak Hadir</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan / Alasan Dispensasi</label>
                    <textarea name="notes" x-model="selectedAttendance.notes" rows="3" placeholder="Tuliskan keterangan permohonan izin atau surat dinas..."
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="statusModal = false" type="button" class="px-3.5 py-1.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        Simpan Perubahan Status
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
