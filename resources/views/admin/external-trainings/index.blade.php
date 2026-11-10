@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Verifikasi JP Eksternal
            </h1>
            <p class="text-xs text-slate-500 mt-1">Review dan verifikasi pengajuan JP pelatihan dari luar platform.</p>
        </div>
        @if($pendingCount > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $pendingCount }} menunggu verifikasi
            </span>
        @endif
    </div>

    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 border-b border-slate-200 pb-0">
        @php $currentStatus = request('status', ''); @endphp
        <a href="{{ route('admin.external-trainings.index') }}" class="px-3 py-2 text-xs font-semibold border-b-2 transition {{ !$currentStatus ? 'text-brand-700 border-brand-600' : 'text-slate-500 border-transparent hover:text-slate-700' }}">Semua</a>
        <a href="{{ route('admin.external-trainings.index', ['status' => 'pending']) }}" class="px-3 py-2 text-xs font-semibold border-b-2 transition {{ $currentStatus === 'pending' ? 'text-amber-700 border-amber-600' : 'text-slate-500 border-transparent hover:text-slate-700' }}">Pending</a>
        <a href="{{ route('admin.external-trainings.index', ['status' => 'approved']) }}" class="px-3 py-2 text-xs font-semibold border-b-2 transition {{ $currentStatus === 'approved' ? 'text-emerald-700 border-emerald-600' : 'text-slate-500 border-transparent hover:text-slate-700' }}">Disetujui</a>
        <a href="{{ route('admin.external-trainings.index', ['status' => 'rejected']) }}" class="px-3 py-2 text-xs font-semibold border-b-2 transition {{ $currentStatus === 'rejected' ? 'text-red-700 border-red-600' : 'text-slate-500 border-transparent hover:text-slate-700' }}">Ditolak</a>
    </div>

    {{-- Submissions List --}}
    @if($submissions->count() > 0)
        <div class="space-y-4">
            @foreach($submissions as $s)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 {{ $s->isPending() ? 'ring-2 ring-amber-200/60' : '' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2 flex-1 min-w-0">
                            {{-- Header --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase
                                    {{ $s->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ $s->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                    {{ $s->status === 'rejected' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}">
                                    {{ $s->status_label }}
                                </span>
                                <h4 class="text-sm font-bold text-slate-900">{{ $s->training_name }}</h4>
                            </div>

                            {{-- Meta --}}
                            <div class="flex items-center gap-4 text-[11px] text-slate-500">
                                <span class="font-semibold text-slate-700">{{ $s->user->name }}</span>
                                @if($s->user->nip)<span class="font-mono text-[10px]">NIP: {{ $s->user->nip }}</span>@endif
                                <span>{{ $s->institution_name }}</span>
                                <span>{{ $s->training_date->format('d M Y') }}</span>
                                <span class="font-bold text-brand-700 bg-brand-50 px-1.5 py-0.5 rounded">{{ $s->jp_count }} JP</span>
                            </div>

                            @if($s->description)
                                <p class="text-xs text-slate-400">{{ $s->description }}</p>
                            @endif

                            {{-- Files --}}
                                @if($s->certificate_file)
                                    <a href="{{ route('external-trainings.file', [$s->id, 'certificate']) }}" target="_blank" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-brand-700 bg-brand-50 border border-brand-200/60 hover:bg-brand-100 transition" title="Lihat Berkas Terverifikasi">📄 Sertifikat</a>
                                @endif
                                @if($s->supporting_file)
                                    <a href="{{ route('external-trainings.file', [$s->id, 'supporting']) }}" target="_blank" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition" title="Lihat Berkas Terverifikasi">📎 Bukti Pendukung</a>
                                @endif
                                @if(!$s->certificate_file && !$s->supporting_file)
                                    <span class="text-[10px] text-slate-400 italic">Tidak ada file yang diupload</span>
                                @endif
                            </div>

                            @if($s->isRejected() && $s->rejection_reason)
                                <div class="p-2.5 rounded-lg bg-red-50 border border-red-200/60 text-xs text-red-700 mt-1">
                                    <strong>Alasan penolakan:</strong> {{ $s->rejection_reason }}
                                </div>
                            @endif

                            @if($s->reviewer)
                                <p class="text-[10px] text-slate-400">Direview oleh {{ $s->reviewer->name }} pada {{ $s->reviewed_at->format('d M Y H:i') }}</p>
                            @endif
                        </div>

                        {{-- Actions --}}
                        @if($s->isPending())
                            <div class="flex flex-col gap-2 shrink-0" x-data="{ showReject: false }">
                                <form action="{{ route('admin.external-trainings.approve', $s) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                                        ✓ Setujui
                                    </button>
                                </form>
                                <button @click="showReject = !showReject" class="px-4 py-2 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200/60 transition">
                                    ✕ Tolak
                                </button>
                                <form x-show="showReject" x-transition action="{{ route('admin.external-trainings.reject', $s) }}" method="POST" class="mt-1 space-y-2">
                                    @csrf
                                    <textarea name="rejection_reason" rows="2" required placeholder="Alasan penolakan..." class="w-full px-3 py-2 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-500 resize-none"></textarea>
                                    <button type="submit" class="w-full px-3 py-1.5 rounded-lg text-[10px] font-bold text-white bg-red-600 hover:bg-red-700 transition">Kirim Penolakan</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 p-10 text-center">
            <p class="text-sm text-slate-500">Tidak ada submission yang ditemukan.</p>
        </div>
    @endif
</div>
@endsection
