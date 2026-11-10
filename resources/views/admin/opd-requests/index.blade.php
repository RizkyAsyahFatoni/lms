@extends('layouts.admin')

@section('title', 'Pengajuan Kegiatan OPD')
@section('page_title', 'Pengajuan Kegiatan OPD')

@section('content')
<!-- Google Font: Urbanist (Kinlo signature font) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

<style>
.kinlo-root {
  font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --kinlo-ink: #15212a;
  --kinlo-ink-muted: #55616a;
  --kinlo-accent: #0e6c84;
  --kinlo-accent-hi: #127d98;
  --kinlo-accent-soft: rgba(14, 108, 132, 0.12);
  --kinlo-glass: rgba(255, 255, 255, 0.85);
  --kinlo-glass-hi: rgba(255, 255, 255, 0.98);
  --kinlo-glass-rim: rgba(255, 255, 255, 0.9);
  --kinlo-glass-edge: rgba(23, 37, 45, 0.08);
  --kinlo-line: rgba(23, 37, 45, 0.08);
  --kinlo-line-2: rgba(23, 37, 45, 0.14);
  --kinlo-shadow: 0 2px 4px rgba(23, 37, 45, 0.04), 0 16px 36px -18px rgba(23, 37, 45, 0.18);
  --kinlo-ease: cubic-bezier(0.22, 1, 0.36, 1);
  --kinlo-r-lg: 18px;
}

.kinlo-card {
  position: relative;
  background: radial-gradient(120% 80% at 0% 0%, var(--kinlo-glass-hi), transparent 60%), var(--kinlo-glass);
  backdrop-filter: blur(24px) saturate(170%);
  -webkit-backdrop-filter: blur(24px) saturate(170%);
  border-radius: var(--kinlo-r-lg);
  box-shadow: inset 0 1px 0 var(--kinlo-glass-rim), inset 0 0 0 1px var(--kinlo-glass-edge), var(--kinlo-shadow);
  transition: transform 0.25s var(--kinlo-ease), box-shadow 0.25s var(--kinlo-ease);
}

.kinlo-hstat {
  background: rgba(23, 37, 45, 0.035);
  box-shadow: inset 0 0 0 1px var(--kinlo-line);
  border-radius: 16px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 2px;
  transition: all 0.2s var(--kinlo-ease);
}
.kinlo-hstat:hover {
  background: #ffffff;
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px rgba(23, 37, 45, 0.12), 0 10px 24px -10px rgba(23, 37, 45, 0.15);
  transform: translateY(-2px);
}

.kinlo-btn {
  height: 40px;
  font-size: 13.5px;
  font-weight: 700;
  border-radius: 12px;
  padding: 0 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all 0.2s var(--kinlo-ease);
  cursor: pointer;
  white-space: nowrap;
}
.kinlo-btn:active {
  transform: scale(0.97);
}
.kinlo-btn-primary {
  background: var(--kinlo-accent);
  color: #ffffff;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 8px 20px -6px var(--kinlo-accent);
}
.kinlo-btn-primary:hover {
  background: var(--kinlo-accent-hi);
  transform: translateY(-1.5px);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35), 0 12px 24px -6px rgba(14, 108, 132, 0.55);
}
.kinlo-btn-ghost {
  background: rgba(255, 255, 255, 0.7);
  color: var(--kinlo-ink);
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px var(--kinlo-line-2);
}
.kinlo-btn-ghost:hover {
  background: #ffffff;
  transform: translateY(-1.5px);
}

.kinlo-pill {
  height: 24px;
  font-size: 11.5px;
  font-weight: 700;
  border-radius: 99px;
  padding: 0 10px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
}
.kinlo-pill::before {
  content: "";
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}
@keyframes kinloBreathe {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.35; transform: scale(1.4); }
}
.kinlo-pill-pulse::before {
  animation: kinloBreathe 2s ease-in-out infinite;
}
.kinlo-pill-warn {
  background: rgba(196, 110, 18, 0.12);
  color: #9a5208;
  box-shadow: inset 0 0 0 1px rgba(196, 110, 18, 0.25);
}
.kinlo-pill-good {
  background: rgba(35, 115, 63, 0.12);
  color: #23733f;
  box-shadow: inset 0 0 0 1px rgba(35, 115, 63, 0.22);
}
.kinlo-pill-bad {
  background: rgba(179, 38, 58, 0.11);
  color: #b3263a;
  box-shadow: inset 0 0 0 1px rgba(179, 38, 58, 0.22);
}
.kinlo-pill-info {
  background: rgba(44, 88, 160, 0.12);
  color: #2c58a0;
  box-shadow: inset 0 0 0 1px rgba(44, 88, 160, 0.22);
}
.kinlo-pill-ghost {
  background: rgba(23, 37, 45, 0.05);
  color: var(--kinlo-ink-muted);
  box-shadow: inset 0 0 0 1px var(--kinlo-line);
}
</style>

<div class="kinlo-root space-y-6 pb-12">

    {{-- KINLO HEADER BANNER --}}
    <div class="kinlo-card p-6 lg:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                    <span>Manajemen Usulan OPD</span>
                    <span>&bull;</span>
                    <span class="text-cyan-800">SPBE & KAK Pasal VII</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    🏛️ Pengajuan Kegiatan Diklat OPD
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Sistem desentralisasi usulan pelatihan kedinasan dari masing-masing Organisasi Perangkat Daerah ke BKPSDM Kabupaten Berau untuk standardisasi Jam Pelajaran (JP).
                </p>
            </div>
            @if(auth()->user()->isAdminOpd() || auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.opd-requests.create') }}" class="kinlo-btn kinlo-btn-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Ajukan Kegiatan Baru</span>
            </a>
            @endif
        </div>

        {{-- KINLO STATS TILES (HSTAT ROW) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mt-6 pt-5 border-t border-slate-200/70">
            @php
            $statCards = [
                ['label' => 'Total Usulan', 'value' => $stats['total'], 'color' => 'slate', 'icon' => '📋'],
                ['label' => 'Menunggu Review', 'value' => $stats['pending'], 'color' => 'amber', 'icon' => '⏳'],
                ['label' => 'Dalam Verifikasi', 'value' => $stats['under_review'], 'color' => 'blue', 'icon' => '🔍'],
                ['label' => 'Telah Disetujui', 'value' => $stats['approved'], 'color' => 'emerald', 'icon' => '✅'],
                ['label' => 'Ditolak', 'value' => $stats['rejected'], 'color' => 'rose', 'icon' => '❌'],
                ['label' => 'Perlu Revisi', 'value' => $stats['revision_needed'], 'color' => 'orange', 'icon' => '✏️'],
            ];
            @endphp
            @foreach($statCards as $card)
            <div class="kinlo-hstat">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">{{ $card['label'] }}</span>
                    <span class="text-base">{{ $card['icon'] }}</span>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $card['value'] }}</div>
                <span class="text-[10px] text-slate-400 font-mono">Dokumen</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- KINLO FILTER BAR --}}
    <div class="kinlo-card p-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Cari Kegiatan / OPD</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Ketik judul kegiatan, OPD, atau narasumber..."
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 outline-none bg-white">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Status Tahapan</label>
                <select name="status" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-cyan-600 outline-none bg-white font-medium">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Menunggu Review</option>
                    <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>🔍 Dalam Verifikasi</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>✅ Disetujui & Terbit</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                    <option value="revision_needed" {{ request('status') === 'revision_needed' ? 'selected' : '' }}>✏️ Perlu Revisi</option>
                </select>
            </div>
            @if(!auth()->user()->isAdminOpd() && $opdList->count() > 0)
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Filter OPD</label>
                <select name="opd" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-cyan-600 outline-none bg-white font-medium max-w-[220px]">
                    <option value="">Seluruh OPD</option>
                    @foreach($opdList as $opd)
                    <option value="{{ $opd }}" {{ request('opd') === $opd ? 'selected' : '' }}>{{ $opd }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <button type="submit" class="kinlo-btn kinlo-btn-primary">
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['q', 'status', 'opd']))
            <a href="{{ route('admin.opd-requests.index') }}" class="kinlo-btn kinlo-btn-ghost">
                <span>Reset</span>
            </a>
            @endif
        </form>
    </div>

    {{-- KINLO DATA TABLE --}}
    <div class="kinlo-card overflow-hidden">
        @if($requests->isEmpty())
        <div class="text-center py-16 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto mb-3">
                📭
            </div>
            <h3 class="text-base font-extrabold text-slate-800">Belum Ada Pengajuan Kegiatan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Usulan diklat dari Organisasi Perangkat Daerah akan muncul di sini untuk ditinjau oleh tim BKPSDM.</p>
            @if(auth()->user()->isAdminOpd())
            <a href="{{ route('admin.opd-requests.create') }}" class="mt-4 kinlo-btn kinlo-btn-primary">
                + Ajukan Kegiatan Pertama
            </a>
            @endif
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-200/80">
                    <tr>
                        <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Kegiatan Pelatihan</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">OPD & Pengusul</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Metode</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Estimasi Waktu</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status Tahapan</th>
                        <th class="text-right px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($requests as $req)
                    <tr class="hover:bg-slate-50/80 transition group">
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.opd-requests.show', $req) }}" class="font-extrabold text-slate-900 group-hover:text-cyan-800 transition line-clamp-1">
                                {{ $req->title }}
                            </a>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                <span class="px-2 py-0.5 rounded bg-cyan-50 text-cyan-800 font-bold text-[11px]">{{ $req->jp ? $req->jp . ' JP' : '2 JP' }}</span>
                                <span>&bull;</span>
                                <span>{{ $req->estimated_participants ? $req->estimated_participants . ' peserta' : 'Kuota Fleksibel' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-bold text-slate-800 text-xs">{{ $req->opd_name }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $req->submitter?->name ?? 'Admin OPD' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $req->learning_method ?? 'E-Learning' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs text-slate-600">
                            @if($req->proposed_start_date)
                                <div class="font-semibold text-slate-800">{{ $req->proposed_start_date->format('d M Y') }}</div>
                                @if($req->proposed_end_date && $req->proposed_end_date != $req->proposed_start_date)
                                    <div class="text-slate-400 text-[11px]">s/d {{ $req->proposed_end_date->format('d M Y') }}</div>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Sesuai Tayang</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($req->status === 'pending')
                                <span class="kinlo-pill kinlo-pill-warn kinlo-pill-pulse">Menunggu</span>
                            @elseif($req->status === 'under_review')
                                <span class="kinlo-pill kinlo-pill-info kinlo-pill-pulse">Verifikasi</span>
                            @elseif($req->status === 'approved')
                                <span class="kinlo-pill kinlo-pill-good">Disetujui</span>
                            @elseif($req->status === 'rejected')
                                <span class="kinlo-pill kinlo-pill-bad">Ditolak</span>
                            @elseif($req->status === 'revision_needed')
                                <span class="kinlo-pill kinlo-pill-warn">Revisi</span>
                            @else
                                <span class="kinlo-pill kinlo-pill-ghost">{{ $req->status_label }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.opd-requests.show', $req) }}"
                                   class="kinlo-btn kinlo-btn-ghost text-xs h-8 px-3">
                                    <span>Buka</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
                                @if(in_array($req->status, ['draft', 'revision_needed']) && (auth()->user()->isSuperAdmin() || $req->submitted_by === auth()->id()))
                                <a href="{{ route('admin.opd-requests.edit', $req) }}"
                                   class="kinlo-btn kinlo-btn-warn text-xs h-8 px-3">
                                    <span>Edit</span>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $requests->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
