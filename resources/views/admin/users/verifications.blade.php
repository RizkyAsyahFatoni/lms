@extends('layouts.admin')

@section('page_title', 'Verifikasi Perubahan Data ASN')

@section('content')
<div class="space-y-6" x-data="{
    rejectModal: false,
    selectedRequest: { id: null, name: '' }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Verifikasi Perubahan Data ASN</h1>
                @if($counts['pending'] > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                        {{ $counts['pending'] }} Menunggu
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Tinjau permohonan mutasi, perubahan OPD, bagian, jabatan, atau pangkat yang diajukan mandiri oleh ASN sebelum disinkronkan ke data resmi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>👥</span>
                <span>Kembali ke Daftar ASN</span>
            </a>
        </div>
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

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Status Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('admin.users.verifications.index', ['status' => 'pending']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('status', 'pending') === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            <span>⏳ Menunggu Verifikasi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status', 'pending') === 'pending' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('admin.users.verifications.index', ['status' => 'approved']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            <span>✅ Telah Disetujui</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'approved' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['approved'] }}</span>
        </a>
        <a href="{{ route('admin.users.verifications.index', ['status' => 'rejected']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            <span>❌ Ditolak</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'rejected' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    <!-- Table of Requests -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        @if($verifications->isEmpty())
            <div class="p-12 text-center space-y-3">
                <span class="text-4xl">🎉</span>
                <h3 class="text-base font-bold text-slate-900">Tidak ada pengajuan {{ request('status', 'pending') === 'pending' ? 'yang menunggu verifikasi' : 'pada kategori ini' }}.</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Semua permohonan pembaruan data kepegawaian ASN dari OPD telah diproses dengan lengkap.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5">Pegawai ASN</th>
                            <th class="px-4 py-3.5">Komparasi Perubahan Data (Lama ➔ Baru)</th>
                            <th class="px-4 py-3.5">Alasan / Catatan Pengajuan</th>
                            <th class="px-4 py-3.5">Waktu Pengajuan</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi BKPSDM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($verifications as $req)
                            @php
                                $u = $req->user;
                                $proposed = $req->proposed_changes ?? [];
                                $old = $req->old_values ?? [];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- User Info -->
                                <td class="px-4 py-3.5 align-top">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-teal-400 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ substr($u->name ?? 'A', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-900 leading-snug">{{ $u->name ?? 'Akun Terhapus' }}</div>
                                            <div class="text-[11px] text-slate-500 font-mono">NIP: {{ $u->nip ?: '-' }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Changes Comparison -->
                                <td class="px-4 py-3.5 align-top">
                                    <div class="space-y-2 max-w-md">
                                        @if(isset($proposed['opd_name']))
                                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px]">
                                                <div class="font-bold text-slate-600 text-[10px] uppercase">Perangkat Daerah / OPD:</div>
                                                <div class="text-rose-600 line-through text-[11px]">{{ $old['opd_name'] ?: '(Belum diatur)' }}</div>
                                                <div class="text-emerald-700 font-bold flex items-center gap-1 mt-0.5">
                                                    <span>➔</span>
                                                    <span>{{ $proposed['opd_name'] }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($proposed['bagian']))
                                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px]">
                                                <div class="font-bold text-slate-600 text-[10px] uppercase">Bagian / Bidang / Seksi:</div>
                                                <div class="text-rose-600 line-through text-[11px]">{{ $old['bagian'] ?: '(Belum diatur)' }}</div>
                                                <div class="text-emerald-700 font-bold flex items-center gap-1 mt-0.5">
                                                    <span>➔</span>
                                                    <span>{{ $proposed['bagian'] }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($proposed['jabatan']))
                                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px]">
                                                <div class="font-bold text-slate-600 text-[10px] uppercase">Jabatan:</div>
                                                <div class="text-rose-600 line-through text-[11px]">{{ $old['jabatan'] ?: '(Belum diatur)' }}</div>
                                                <div class="text-emerald-700 font-bold flex items-center gap-1 mt-0.5">
                                                    <span>➔</span>
                                                    <span>{{ $proposed['jabatan'] }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($proposed['pangkat_golongan']))
                                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px]">
                                                <div class="font-bold text-slate-600 text-[10px] uppercase">Pangkat / Golongan:</div>
                                                <div class="text-rose-600 line-through text-[11px]">{{ $old['pangkat_golongan'] ?: '(Belum diatur)' }}</div>
                                                <div class="text-emerald-700 font-bold flex items-center gap-1 mt-0.5">
                                                    <span>➔</span>
                                                    <span>{{ $proposed['pangkat_golongan'] }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($proposed['nip']))
                                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px]">
                                                <div class="font-bold text-slate-600 text-[10px] uppercase">NIP:</div>
                                                <div class="text-rose-600 line-through font-mono text-[11px]">{{ $old['nip'] ?: '-' }}</div>
                                                <div class="text-emerald-700 font-bold font-mono flex items-center gap-1 mt-0.5">
                                                    <span>➔</span>
                                                    <span>{{ $proposed['nip'] }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($proposed['name']))
                                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px]">
                                                <div class="font-bold text-slate-600 text-[10px] uppercase">Nama Lengkap & Gelar:</div>
                                                <div class="text-rose-600 line-through text-[11px]">{{ $old['name'] ?: '-' }}</div>
                                                <div class="text-emerald-700 font-bold flex items-center gap-1 mt-0.5">
                                                    <span>➔</span>
                                                    <span>{{ $proposed['name'] }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Reason -->
                                <td class="px-4 py-3.5 align-top">
                                    <p class="text-slate-700 italic text-[11px] leading-relaxed">
                                        "{{ $req->reason ?: 'Pengkinian mandiri data kepegawaian' }}"
                                    </p>
                                    @if($req->admin_notes)
                                        <div class="mt-2 p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-[10px]">
                                            <span class="font-bold">Catatan Penolakan:</span> {{ $req->admin_notes }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Timestamp -->
                                <td class="px-4 py-3.5 align-top whitespace-nowrap">
                                    <div class="font-medium text-slate-700">{{ $req->created_at->format('d M Y, H:i') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $req->created_at->diffForHumans() }}</div>
                                    @if($req->reviewed_at)
                                        <div class="mt-2 text-[10px] text-slate-500 border-t border-slate-100 pt-1">
                                            Ditinjau: {{ $req->reviewed_at->format('d M Y') }}<br>
                                            Oleh: <span class="font-bold text-slate-700">{{ $req->reviewer->name ?? 'Admin' }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3.5 align-top whitespace-nowrap">
                                    {!! $req->status_badge !!}
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-4 py-3.5 align-top text-right whitespace-nowrap">
                                    @if($req->status === 'pending')
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Approve Form -->
                                            <form action="{{ route('admin.users.verifications.approve', $req) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui perubahan data kepegawaian untuk {{ addslashes($u->name ?? 'ASN') }}?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1">
                                                    <span>✅</span>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>

                                            <!-- Reject Button (Trigger Modal) -->
                                            <button @click="selectedRequest = { id: {{ $req->id }}, name: '{{ addslashes($u->name ?? 'ASN') }}' }; rejectModal = true"
                                                    class="px-3 py-1.5 rounded-xl bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-xs shadow-2xs transition flex items-center gap-1">
                                                <span>❌</span>
                                                <span>Tolak</span>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Selesai diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($verifications->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $verifications->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Modal Penolakan Verifikasi -->
    <div x-show="rejectModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="rejectModal = false"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚠️</span>
                    <h3 class="text-base font-extrabold text-slate-900">Tolak Permohonan Perubahan</h3>
                </div>
                <button @click="rejectModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <p class="text-xs text-slate-600">
                Anda akan menolak pengajuan perubahan data dari ASN: <strong x-text="selectedRequest.name" class="text-slate-900"></strong>.
            </p>

            <form :action="'{{ url('/admin/users/verifications') }}/' + selectedRequest.id + '/reject'" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="admin_notes" rows="3" required placeholder="Contoh: Lampiran SK mutasi belum terverifikasi di SI-ASN atau nama jabatan tidak sesuai formatur."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition text-xs"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Catatan ini akan dapat dibaca oleh ASN yang bersangkutan di halaman profilnya.</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                        Ya, Tolak Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
