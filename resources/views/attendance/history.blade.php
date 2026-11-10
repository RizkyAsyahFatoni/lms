@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Riwayat Presensi Digital Saya</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $attendances->total() }} Kegiatan
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Daftar rekaman kehadiran resmi pembelajaran, webinar, dan pelatihan yang Anda ikuti.</p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-2xs transition self-start sm:self-auto">
            <span>&larr;</span>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <span>ℹ️</span>
                <span>{{ session('info') }}</span>
            </div>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Hadir</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</p>
            <p class="text-[10px] text-slate-400">Sesi Terdaftar</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-emerald-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Tepat Waktu</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['present'] }}</p>
            <p class="text-[10px] text-slate-400">Sesuai Toleransi</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-amber-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Terlambat</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $stats['late'] }}</p>
            <p class="text-[10px] text-slate-400">Keterlambatan</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-teal-200/90 shadow-2xs">
            <p class="text-[10px] font-bold uppercase tracking-wider text-teal-600">Akumulasi Waktu</p>
            <p class="text-2xl font-black text-teal-600 mt-1">{{ number_format($stats['total_min']) }} <span class="text-xs text-slate-400 font-bold">Menit</span></p>
            <p class="text-[10px] text-slate-400">Waktu Belajar Hadir</p>
        </div>
    </div>

    <!-- Attendance History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Riwayat Catatan Presensi</h3>
            <span class="text-xs text-slate-500 font-mono">{{ $attendances->total() }} Catatan</span>
        </div>

        @if($attendances->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                <div class="text-3xl mb-2">📜</div>
                <p class="font-bold text-slate-600">Belum ada riwayat presensi tercatat.</p>
                <p class="text-slate-400 mt-1">Presensi Anda akan otomatis muncul di sini saat Anda mengikuti webinar atau kelas pelatihan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/60 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Kegiatan & Sesi</th>
                            <th class="py-3 px-4">Tanggal Pelaksanaan</th>
                            <th class="py-3 px-4">Waktu Check-In</th>
                            <th class="py-3 px-4 text-center">Status Kehadiran</th>
                            <th class="py-3 px-4 text-center">Metode</th>
                            <th class="py-3 px-4 text-center">Durasi Hadir</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($attendances as $att)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-900 leading-snug">{{ $att->activity_title }}</p>
                                    @if($att->session_code)
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-slate-100 text-slate-500">
                                            {{ $att->session_code }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">{{ $att->date ? $att->date->format('d M Y') : '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap font-mono text-[11px]">
                                    {{ $att->check_in_at ? $att->check_in_at->format('H:i:s') : $att->time }} WIB
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    {!! $att->status_badge !!}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    {!! $att->method_badge !!}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-slate-800">{{ $att->formatted_duration }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if(!$att->check_out_at)
                                        <form action="{{ route('attendance.checkout', $att) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda ingin menyelesaikan sesi presensi kegiatan ini sekarang?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition">
                                                Selesai / Keluar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] font-mono text-slate-400">
                                            Selesai ({{ $att->check_out_at->format('H:i') }})
                                        </span>
                                    @endif
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

</div>
@endsection
