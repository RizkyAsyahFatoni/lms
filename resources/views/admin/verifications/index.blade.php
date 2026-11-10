@extends('layouts.admin')

@section('page_title', 'Pusat Verifikasi & Approval')

@section('content')
<div class="space-y-6" x-data="{
    tab: '{{ $activeTab }}',
    rejectAsnModal: false,
    selectedAsnRequest: { id: null, name: '' },
    rejectJpModal: false,
    selectedJpSubmission: { id: null, name: '', jp: 0 }
}">

    <!-- Page Header & Notification Summary Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pusat Verifikasi & Approval</h1>
                @if($stats['total_pending'] > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-500 text-white shadow-sm animate-pulse flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-white"></span>
                        {{ $stats['total_pending'] }} Antrean Menunggu
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        ✅ Semua Terverifikasi
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Panel kendali terpusat untuk memproses permohonan mutasi data kepegawaian ASN dan validasi sertifikat JP pelatihan eksternal.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>👥</span>
                <span>Manajemen User</span>
            </a>
            <a href="{{ route('admin.jp.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>⏱️</span>
                <span>Rekapitulasi JP</span>
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

    <!-- 4 Metric Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Antrean -->
        <div class="p-5 rounded-2xl border transition shadow-sm {{ $stats['total_pending'] > 0 ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white border-rose-600' : 'bg-white border-slate-200 text-slate-900' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $stats['total_pending'] > 0 ? 'text-rose-100' : 'text-slate-400' }}">Total Antrean Verifikasi</span>
                <span class="text-xl">🔔</span>
            </div>
            <div class="text-3xl font-black mt-2 leading-none">
                {{ $stats['total_pending'] }}
            </div>
            <p class="text-[11px] mt-2 {{ $stats['total_pending'] > 0 ? 'text-rose-100' : 'text-slate-500' }}">
                {{ $stats['total_pending'] > 0 ? 'Perlu tindakan review segera' : 'Tidak ada permohonan tertunda' }}
            </p>
        </div>

        <!-- Card 2: Verifikasi Data ASN -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Verifikasi Data ASN</span>
                <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">👤</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-2 leading-none">
                {{ $stats['pending_asn'] }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                <span class="text-slate-500">Mutasi / Ganti OPD</span>
                <span class="font-bold text-emerald-600">{{ $stats['approved_asn'] }} Disetujui</span>
            </div>
        </div>

        <!-- Card 3: Verifikasi JP Eksternal (Diklat) -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Verifikasi JP Eksternal</span>
                <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">🏛️</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-2 leading-none">
                {{ $stats['pending_external'] }}
                <span class="text-sm font-semibold text-slate-400">Berkas</span>
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                <span class="text-slate-500">Menunggu Pengakuan</span>
                <span class="font-bold text-amber-600">{{ $stats['pending_external_jp'] }} JP</span>
            </div>
        </div>

        <!-- Card 4: Total JP yang Diajukan -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total JP Eksternal Diakui</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">⏱️</span>
            </div>
            <div class="text-3xl font-black text-emerald-700 mt-2 leading-none">
                {{ number_format($stats['approved_external_jp']) }}
                <span class="text-sm font-semibold text-slate-400">JP</span>
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                <span class="text-slate-500">Dari total diklat luar</span>
                <span class="font-bold text-slate-700">{{ $stats['approved_external'] }} Sertifikat</span>
            </div>
        </div>
    </div>

    <!-- Interactive Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 flex-wrap">
        <button @click="tab = 'all'" 
                :class="tab === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>📋 Semua Antrean</span>
            <span class="px-2 py-0.5 rounded-full text-[10px]" :class="tab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                {{ $stats['total_pending'] }}
            </span>
        </button>

        <button @click="tab = 'asn'" 
                :class="tab === 'asn' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>👤 Verifikasi Data ASN</span>
            <span class="px-2 py-0.5 rounded-full text-[10px]" :class="tab === 'asn' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700'">
                {{ $stats['pending_asn'] }}
            </span>
        </button>

        <button @click="tab = 'jp'" 
                :class="tab === 'jp' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>🏛️ Verifikasi JP Eksternal</span>
            <span class="px-2 py-0.5 rounded-full text-[10px]" :class="tab === 'jp' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-700'">
                {{ $stats['pending_external'] }} Diklat ({{ $stats['pending_external_jp'] }} JP)
            </span>
        </button>
    </div>

    <!-- SECTION 1: Antrean Verifikasi Data ASN -->
    <div x-show="tab === 'all' || tab === 'asn'" class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                <span>👤</span>
                <span>Permohonan Perubahan Data Kepegawaian ASN</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $stats['pending_asn'] }} Pengajuan
                </span>
            </h3>
            <a href="{{ route('admin.users.verifications.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                Buka Halaman Lengkap & Riwayat &rarr;
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            @if($pendingAsnRequests->isEmpty())
                <div class="p-8 text-center text-xs text-slate-500 space-y-1">
                    <span class="text-2xl block">🎉</span>
                    <p class="font-bold text-slate-700">Tidak ada antrean verifikasi data ASN.</p>
                    <p class="text-[11px] text-slate-400">Seluruh permohonan mutasi OPD, bagian, dan jabatan telah diproses.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Pegawai ASN</th>
                                <th class="px-4 py-3">Komparasi Perubahan (Lama ➔ Baru)</th>
                                <th class="px-4 py-3">Alasan / SK</th>
                                <th class="px-4 py-3">Diajukan</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingAsnRequests as $req)
                                @php
                                    $u = $req->user;
                                    $proposed = $req->proposed_changes ?? [];
                                    $old = $req->old_values ?? [];
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-4 py-3 align-top">
                                        <div class="font-extrabold text-slate-900">{{ $u->name ?? 'ASN' }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">NIP: {{ $u->nip ?: '-' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $u->email }}</div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="space-y-1.5 max-w-sm">
                                            @if(isset($proposed['opd_name']))
                                                <div class="text-[11px]">
                                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">OPD:</span>
                                                    <span class="line-through text-rose-500">{{ $old['opd_name'] ?: '(Belum diatur)' }}</span>
                                                    <span class="text-emerald-700 font-bold block">➔ {{ $proposed['opd_name'] }}</span>
                                                </div>
                                            @endif
                                            @if(isset($proposed['bagian']))
                                                <div class="text-[11px]">
                                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Bagian:</span>
                                                    <span class="line-through text-rose-500">{{ $old['bagian'] ?: '(Belum diatur)' }}</span>
                                                    <span class="text-emerald-700 font-bold block">➔ {{ $proposed['bagian'] }}</span>
                                                </div>
                                            @endif
                                            @if(isset($proposed['jabatan']))
                                                <div class="text-[11px]">
                                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Jabatan:</span>
                                                    <span class="line-through text-rose-500">{{ $old['jabatan'] ?: '(Belum diatur)' }}</span>
                                                    <span class="text-emerald-700 font-bold block">➔ {{ $proposed['jabatan'] }}</span>
                                                </div>
                                            @endif
                                            @if(isset($proposed['pangkat_golongan']))
                                                <div class="text-[11px]">
                                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Pangkat/Golongan:</span>
                                                    <span class="line-through text-rose-500">{{ $old['pangkat_golongan'] ?: '(Belum diatur)' }}</span>
                                                    <span class="text-emerald-700 font-bold block">➔ {{ $proposed['pangkat_golongan'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <p class="text-slate-600 italic text-[11px]">
                                            "{{ $req->reason ?: 'Pengkinian mandiri data kepegawaian' }}"
                                        </p>
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-500 whitespace-nowrap text-[11px]">
                                        {{ $req->created_at->diffForHumans() }}
                                    </td>

                                    <td class="px-4 py-3 align-top text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form action="{{ route('admin.users.verifications.approve', $req) }}" method="POST" onsubmit="return confirm('Setujui pembaruan data untuk {{ addslashes($u->name ?? 'ASN') }}?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition">
                                                    Setujui
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.users.verifications.index') }}" class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-2xs transition">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- SECTION 2: Antrean Verifikasi JP Eksternal -->
    <div x-show="tab === 'all' || tab === 'jp'" class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                <span>🏛️</span>
                <span>Pengajuan Validasi JP Pelatihan Eksternal</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                    {{ $stats['pending_external'] }} Berkas (Total: {{ $stats['pending_external_jp'] }} JP Diajukan)
                </span>
            </h3>
            <a href="{{ route('admin.external-trainings.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                Buka Halaman Lengkap Diklat Luar &rarr;
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            @if($pendingExternalTrainings->isEmpty())
                <div class="p-8 text-center text-xs text-slate-500 space-y-1">
                    <span class="text-2xl block">🎉</span>
                    <p class="font-bold text-slate-700">Tidak ada pengajuan JP eksternal yang menunggu.</p>
                    <p class="text-[11px] text-slate-400">Seluruh sertifikat pelatihan luar platform telah diverifikasi.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Nama Pelatihan / Diklat Luar</th>
                                <th class="px-4 py-3">Pegawai ASN</th>
                                <th class="px-4 py-3">Lembaga Penyelenggara</th>
                                <th class="px-4 py-3 text-center">Bobot JP Diajukan</th>
                                <th class="px-4 py-3">Berkas Sertifikat</th>
                                <th class="px-4 py-3 text-right">Aksi BKPSDM</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingExternalTrainings as $ext)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-4 py-3 align-top">
                                        <div class="font-extrabold text-slate-900 leading-snug">{{ $ext->training_name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            Tanggal: {{ $ext->training_date ? $ext->training_date->format('d M Y') : '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ $ext->user->name ?? 'ASN' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">NIP: {{ $ext->user->nip ?: '-' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $ext->user->opd_name ?: 'OPD Belum diatur' }}</div>
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-700">
                                        <span class="font-medium">{{ $ext->institution_name ?: '-' }}</span>
                                    </td>

                                    <td class="px-4 py-3 align-top text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-800 border border-amber-300">
                                            ⏱️ {{ $ext->jp_count }} JP
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 align-top whitespace-nowrap">
                                        @if($ext->certificate_file)
                                            <a href="{{ route('external-trainings.file', [$ext, 'certificate']) }}" target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-[11px] transition">
                                                <span>📄</span>
                                                <span>Lihat Sertifikat</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Tidak ada berkas</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 align-top text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Approve Form -->
                                            <form action="{{ route('admin.external-trainings.approve', $ext) }}" method="POST" onsubmit="return confirm('Setujui pengakuan {{ $ext->jp_count }} JP untuk pelatihan ini?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition">
                                                    Setujui ({{ $ext->jp_count }} JP)
                                                </button>
                                            </form>

                                            <!-- Link to Detail/Reject -->
                                            <a href="{{ route('admin.external-trainings.index', ['status' => 'pending']) }}" class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-2xs transition">
                                                Review
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
