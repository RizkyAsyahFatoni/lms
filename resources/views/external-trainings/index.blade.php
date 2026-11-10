@extends('layouts.app')

@section('content')
<div class="bg-white border-b border-slate-200/80 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat JP Pelatihan Eksternal
                </h1>
                <p class="text-xs text-slate-500 mt-1">Kelola dan pantau pengajuan Jam Pelajaran dari pelatihan di luar platform.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('external-trainings.create') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Ajukan JP Baru
                </a>
            </div>
        </div>

        {{-- JP Summary Card --}}
        <div class="mt-6 p-5 bg-gradient-to-r from-brand-50 via-teal-50 to-emerald-50 rounded-2xl border border-brand-200/40">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-brand-600">JP Tahun {{ $currentYear }}</p>
                    <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $jpData['total'] }}<span class="text-sm text-slate-400 font-normal">/{{ $jpData['target'] }}</span></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">JP Internal</p>
                    <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $jpData['internal'] }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-teal-600">JP Eksternal</p>
                    <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $jpData['external'] }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Status</p>
                    @if($jpData['achieved'])
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                            Target Tercapai
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Kurang {{ $jpData['target'] - $jpData['total'] }} JP
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    @if($trainings->count() > 0)
        @foreach($trainingsByYear as $year => $yearTrainings)
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 flex items-center gap-2">
                    <span class="w-8 h-[1px] bg-slate-200"></span>
                    Tahun {{ $year }}
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $yearTrainings->count() }} pelatihan</span>
                </h3>

                <div class="space-y-3">
                    @foreach($yearTrainings as $t)
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:border-brand-200 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $t->training_name }}</h4>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-{{ $t->status_color }}-50 text-{{ $t->status_color }}-700 border border-{{ $t->status_color }}-200">
                                            {{ $t->status_label }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $t->institution_name }}</p>
                                    <div class="flex items-center gap-4 text-[11px] text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $t->training_date->format('d M Y') }}@if($t->training_end_date) — {{ $t->training_end_date->format('d M Y') }}@endif
                                        </span>
                                        <span class="font-bold text-brand-700 bg-brand-50 px-1.5 py-0.5 rounded text-[11px]">{{ $t->jp_count }} JP</span>
                                    </div>
                                    @if($t->description)
                                        <p class="text-xs text-slate-400 pt-1">{{ $t->description }}</p>
                                    @endif
                                    @if($t->isRejected() && $t->rejection_reason)
                                        <div class="flex items-start gap-1.5 p-2 rounded-lg bg-red-50 border border-red-200/60 text-xs text-red-700 mt-2">
                                            <svg class="w-3.5 h-3.5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                            <span><strong>Alasan:</strong> {{ $t->rejection_reason }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($t->certificate_file)
                                        <a href="{{ route('external-trainings.file', [$t->id, 'certificate']) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-brand-700 bg-brand-50 border border-brand-200/60 hover:bg-brand-100 transition" title="Lihat Sertifikat (Terlindungi)">
                                            📄 Sertifikat
                                        </a>
                                    @endif
                                    @if($t->supporting_file)
                                        <a href="{{ route('external-trainings.file', [$t->id, 'supporting']) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition" title="Lihat Bukti Pendukung (Terlindungi)">
                                            📎 Bukti
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 p-10 text-center">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan JP eksternal</p>
            <p class="text-xs text-slate-400 mt-1">Ajukan JP dari pelatihan di luar platform untuk melengkapi target JP tahunan Anda.</p>
            <a href="{{ route('external-trainings.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Ajukan JP Pertama Anda
            </a>
        </div>
    @endif
</div>
@endsection
