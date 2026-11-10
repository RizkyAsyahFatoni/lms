@extends('layouts.admin')

@section('page_title', 'Manajemen Trainer & Instruktur')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white flex items-center justify-center font-black shadow-sm text-lg">
                    🎓
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pendaftaran & Akun Trainer</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Tinjau dan setujui permohonan calon pemateri pelatihan, webinar, workshop, dan bimtek.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('trainer.register') }}" target="_blank"
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition flex items-center gap-2">
                <span>Form Pendaftaran Publik</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Quick Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[11px] font-medium text-slate-500">Menunggu Verifikasi</span>
            <p class="text-2xl font-black text-amber-600 mt-0.5">{{ $pendingCount }}</p>
            <span class="text-[10px] font-mono text-amber-600 font-semibold">● Memerlukan Persetujuan Admin</span>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[11px] font-medium text-slate-500">Trainer & Instruktur Aktif</span>
            <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ $approvedCount }}</p>
            <span class="text-[10px] font-mono text-emerald-600 font-semibold">✓ Dapat Membuat Materi</span>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[11px] font-medium text-slate-500">Total Permohonan Masuk</span>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $trainers->total() }}</p>
            <span class="text-[10px] font-mono text-slate-400 font-semibold">Data Tersimpan di Database</span>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs">
        <a href="{{ route('admin.trainers.index') }}" 
           class="px-3.5 py-1.5 rounded-lg font-semibold transition {{ !$status ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua ({{ $trainers->total() }})
        </a>
        <a href="{{ route('admin.trainers.index', ['status' => 'pending']) }}" 
           class="px-3.5 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5 {{ $status === 'pending' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>Menunggu Review</span>
            @if($pendingCount > 0)
                <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[10px] font-bold font-mono">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.trainers.index', ['status' => 'approved']) }}" 
           class="px-3.5 py-1.5 rounded-lg font-semibold transition {{ $status === 'approved' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Disetujui
        </a>
        <a href="{{ route('admin.trainers.index', ['status' => 'rejected']) }}" 
           class="px-3.5 py-1.5 rounded-lg font-semibold transition {{ $status === 'rejected' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Ditolak
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-4">Trainer / Pemohon</th>
                        <th class="p-4">Keahlian & Pengalaman</th>
                        <th class="p-4">Dokumen CV</th>
                        <th class="p-4 text-center">Materi Dibuat</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Tanggal Pengajuan</th>
                        <th class="p-4 text-right">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($trainers as $t)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-teal-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ substr($t->name, 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 truncate">{{ $t->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono truncate">{{ $t->email }}</p>
                                        @if($t->headline)
                                            <p class="text-[10px] text-teal-700 font-semibold truncate mt-0.5">{{ $t->headline }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 max-w-xs">
                                <span class="px-2 py-0.5 rounded-md bg-teal-50 text-teal-700 font-semibold text-[10px] border border-teal-200/60">
                                    {{ $t->trainer_expertise ?? 'Umum' }}
                                </span>
                                @if($t->trainer_bio)
                                    <p class="text-[11px] text-slate-500 line-clamp-2 mt-1">{{ $t->trainer_bio }}</p>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($t->trainer_cv_path)
                                    <a href="{{ asset($t->trainer_cv_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Lihat CV</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full font-bold {{ $t->created_courses_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $t->created_courses_count }} Kursus
                                </span>
                            </td>
                            <td class="p-4">
                                @if($t->trainer_status === 'approved' || $t->role === 'instructor')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                @elseif($t->trainer_status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu Review
                                    </span>
                                @elseif($t->trainer_status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 text-[11px] font-mono">
                                {{ $t->trainer_applied_at ? $t->trainer_applied_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($t->trainer_status !== 'approved')
                                        <form action="{{ route('admin.trainers.approve', $t->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui {{ $t->name }} sebagai Trainer/Instruktur resmi?');">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-2xs">
                                                ✓ Setujui
                                            </button>
                                        </form>
                                    @endif

                                    @if($t->trainer_status !== 'rejected')
                                        <form action="{{ route('admin.trainers.reject', $t->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menolak permohonan {{ $t->name }}?');">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 transition border border-rose-200">
                                                ✕ Tolak
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-xs">
                                Belum ada pendaftar trainer dalam status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($trainers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $trainers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
