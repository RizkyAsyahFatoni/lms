@extends('layouts.admin')
@section('title', 'Detail Pengajuan: ' . $opdRequest->title)
@section('page_title', 'Detail Pengajuan Kegiatan OPD')

@section('content')
<!-- Google Font: Urbanist (Kinlo signature font) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

<style>
/* ============================================================
   KINLO DESIGN SYSTEM TOKENS & ANIMATIONS
   Adapted for LMS Pemerintah Kabupaten Berau - OPD Requests
   ============================================================ */
.kinlo-root {
  font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --kinlo-ink: #15212a;
  --kinlo-ink-muted: #55616a;
  --kinlo-accent: #0e6c84;
  --kinlo-accent-hi: #127d98;
  --kinlo-accent-soft: rgba(14, 108, 132, 0.12);
  --kinlo-accent-text: #085569;
  --kinlo-glass: rgba(255, 255, 255, 0.82);
  --kinlo-glass-hi: rgba(255, 255, 255, 0.96);
  --kinlo-glass-rim: rgba(255, 255, 255, 0.9);
  --kinlo-glass-edge: rgba(23, 37, 45, 0.08);
  --kinlo-card: #fcfdfe;
  --kinlo-well: rgba(23, 37, 45, 0.035);
  --kinlo-line: rgba(23, 37, 45, 0.08);
  --kinlo-line-2: rgba(23, 37, 45, 0.14);
  --kinlo-shadow: 0 2px 4px rgba(23, 37, 45, 0.04), 0 16px 36px -18px rgba(23, 37, 45, 0.18);
  --kinlo-shadow-lg: 0 4px 12px rgba(23, 37, 45, 0.06), 0 28px 60px -20px rgba(23, 37, 45, 0.32);
  --kinlo-ease: cubic-bezier(0.22, 1, 0.36, 1);
  --kinlo-r-md: 14px;
  --kinlo-r-lg: 18px;
  --kinlo-r-xl: 22px;
}

/* Subtle Kinlo Organic Mesh Background */
.kinlo-canvas {
  position: relative;
  border-radius: 24px;
  padding: 4px;
}
.kinlo-canvas::before {
  content: "";
  position: absolute;
  inset: -15px -10px;
  background: 
    radial-gradient(120% 80% at 0% 0%, rgba(244, 191, 166, 0.18) 0%, transparent 45%),
    radial-gradient(100% 100% at 100% 0%, rgba(191, 226, 230, 0.28) 0%, transparent 50%),
    radial-gradient(80% 80% at 50% 100%, rgba(225, 234, 243, 0.22) 0%, transparent 60%);
  pointer-events: none;
  z-index: 0;
  border-radius: 32px;
}

/* Kinlo Glass Cards */
.kinlo-card {
  position: relative;
  z-index: 1;
  background: radial-gradient(120% 80% at 0% 0%, var(--kinlo-glass-hi), transparent 60%), var(--kinlo-glass);
  backdrop-filter: blur(24px) saturate(170%);
  -webkit-backdrop-filter: blur(24px) saturate(170%);
  border-radius: var(--kinlo-r-lg);
  box-shadow: inset 0 1px 0 var(--kinlo-glass-rim), inset 0 0 0 1px var(--kinlo-glass-edge), var(--kinlo-shadow);
  transition: transform 0.25s var(--kinlo-ease), box-shadow 0.25s var(--kinlo-ease);
}
.kinlo-card:hover {
  box-shadow: inset 0 1px 0 var(--kinlo-glass-rim), inset 0 0 0 1px rgba(23, 37, 45, 0.12), 0 20px 42px -18px rgba(23, 37, 45, 0.22);
}

/* Kinlo Night Card (Signature Dark Card) */
.kinlo-card-night {
  position: relative;
  z-index: 1;
  background: radial-gradient(130% 90% at 10% 10%, #164654 0%, #0d2932 70%, #091c22 100%);
  border-radius: var(--kinlo-r-lg);
  color: #f1f7f9;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 0 0 1px rgba(255, 255, 255, 0.08), 0 16px 36px -12px rgba(8, 26, 32, 0.5);
  overflow: hidden;
}
.kinlo-card-night::after {
  content: "";
  position: absolute;
  top: -60px;
  right: -60px;
  width: 180px;
  height: 180px;
  background: radial-gradient(circle, rgba(14, 108, 132, 0.35) 0%, transparent 70%);
  pointer-events: none;
}

/* Kinlo Inset Well */
.kinlo-well {
  background: var(--kinlo-well);
  border-radius: var(--kinlo-r-md);
  box-shadow: inset 0 0 0 1px var(--kinlo-line);
}

/* Kinlo Interactive Buttons */
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
  letter-spacing: -0.01em;
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
.kinlo-btn-warn {
  background: rgba(196, 110, 18, 0.12);
  color: #9a5208;
  box-shadow: inset 0 0 0 1px rgba(196, 110, 18, 0.28);
}
.kinlo-btn-warn:hover {
  background: #c46e12;
  color: #ffffff;
  box-shadow: 0 8px 18px -6px #c46e12;
  transform: translateY(-1.5px);
}
.kinlo-btn-danger {
  background: rgba(179, 38, 58, 0.1);
  color: #b3263a;
  box-shadow: inset 0 0 0 1px rgba(179, 38, 58, 0.25);
}
.kinlo-btn-danger:hover {
  background: #b3263a;
  color: #ffffff;
  box-shadow: 0 8px 18px -6px #b3263a;
  transform: translateY(-1.5px);
}
.kinlo-btn-ghost {
  background: rgba(255, 255, 255, 0.7);
  color: var(--kinlo-ink);
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px var(--kinlo-line-2);
}
.kinlo-btn-ghost:hover {
  background: #ffffff;
  transform: translateY(-1.5px);
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px rgba(23, 37, 45, 0.2), 0 6px 14px -6px rgba(23, 37, 45, 0.12);
}

/* Status Pills with Breathing Animation */
.kinlo-pill {
  height: 26px;
  font-size: 12px;
  font-weight: 700;
  border-radius: 99px;
  padding: 0 12px;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  letter-spacing: -0.005em;
  white-space: nowrap;
}
.kinlo-pill::before {
  content: "";
  width: 6.5px;
  height: 6.5px;
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
.kinlo-pill-accent {
  background: var(--kinlo-accent-soft);
  color: var(--kinlo-accent-text);
  box-shadow: inset 0 0 0 1px rgba(14, 108, 132, 0.22);
}

/* Stepped Lifecycle Timeline */
.kinlo-stepper {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  position: relative;
}
@media (max-width: 768px) {
  .kinlo-stepper {
    grid-template-columns: 1fr;
    gap: 8px;
  }
}
.kinlo-step-node {
  position: relative;
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid var(--kinlo-line);
  border-radius: 14px;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.2s var(--kinlo-ease);
}
.kinlo-step-node.is-done {
  background: rgba(35, 115, 63, 0.06);
  border-color: rgba(35, 115, 63, 0.25);
}
.kinlo-step-node.is-active {
  background: #ffffff;
  border-color: var(--kinlo-accent);
  box-shadow: 0 4px 16px -4px rgba(14, 108, 132, 0.25), inset 0 0 0 1px var(--kinlo-accent);
}
.kinlo-step-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 800;
  flex-shrink: 0;
}
.kinlo-step-node.is-done .kinlo-step-circle {
  background: #23733f;
  color: #ffffff;
}
.kinlo-step-node.is-active .kinlo-step-circle {
  background: var(--kinlo-accent);
  color: #ffffff;
  box-shadow: 0 0 0 4px rgba(14, 108, 132, 0.2);
}
.kinlo-step-node.is-pending .kinlo-step-circle {
  background: rgba(23, 37, 45, 0.08);
  color: var(--kinlo-ink-muted);
}

/* Kinlo Segmented Tabs */
.kinlo-tabs-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(23, 37, 45, 0.05);
  padding: 4px;
  border-radius: 14px;
  width: fit-content;
  max-width: 100%;
  overflow-x: auto;
  box-shadow: inset 0 1px 2px rgba(23, 37, 45, 0.06);
}
.kinlo-tab-btn {
  height: 34px;
  padding: 0 14px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  color: var(--kinlo-ink-muted);
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s var(--kinlo-ease);
  cursor: pointer;
  white-space: nowrap;
}
.kinlo-tab-btn:hover {
  color: var(--kinlo-ink);
}
.kinlo-tab-btn.is-active {
  background: #ffffff;
  color: var(--kinlo-accent);
  box-shadow: 0 2px 6px rgba(23, 37, 45, 0.08), 0 1px 0 rgba(255, 255, 255, 0.8);
}
</style>

<div class="kinlo-root max-w-6xl mx-auto space-y-6 pb-12" x-data="{ activeTab: 'curriculum' }">

    {{-- KINLO HERO BANNER CARD --}}
    <div class="kinlo-card p-6 lg:p-7">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
            
            {{-- Left: Breadcrumb & Title --}}
            <div class="space-y-3 flex-1 min-w-0">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                    <a href="{{ route('admin.opd-requests.index') }}" class="hover:text-cyan-800 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Daftar Pengajuan OPD</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono text-[11px] font-bold">REQ-{{ str_pad($opdRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    {{ $opdRequest->title }}
                </h1>

                {{-- Status & Meta Badges --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @if($opdRequest->status === 'pending')
                        <span class="kinlo-pill kinlo-pill-warn kinlo-pill-pulse">
                            Menunggu Review BKPSDM
                        </span>
                    @elseif($opdRequest->status === 'under_review')
                        <span class="kinlo-pill kinlo-pill-info kinlo-pill-pulse">
                            Sedang Diverifikasi Tim BKPSDM
                        </span>
                    @elseif($opdRequest->status === 'approved')
                        <span class="kinlo-pill kinlo-pill-good">
                            Disetujui & Diterbitkan
                        </span>
                    @elseif($opdRequest->status === 'rejected')
                        <span class="kinlo-pill kinlo-pill-bad">
                            Ditolak
                        </span>
                    @elseif($opdRequest->status === 'revision_needed')
                        <span class="kinlo-pill kinlo-pill-warn">
                            Perlu Revisi OPD
                        </span>
                    @else
                        <span class="kinlo-pill kinlo-pill-ghost">
                            {{ $opdRequest->status_label }}
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>{{ $opdRequest->opd_name }}</span>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ $opdRequest->submitter?->name ?? 'Admin OPD' }}</span>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Diajukan {{ $opdRequest->created_at->diffForHumans() }}</span>
                    </span>
                </div>
            </div>

            {{-- Right: Actions Toolbar (Kinlo Button Group) --}}
            <div class="flex flex-wrap lg:flex-nowrap items-center gap-2.5 shrink-0 pt-2 lg:pt-0">
                @if(auth()->user()->isSuperAdmin() && in_array($opdRequest->status, ['pending', 'under_review']))
                    {{-- Approve Button --}}
                    <form action="{{ route('admin.opd-requests.approve', $opdRequest) }}" method="POST" class="inline"
                          onsubmit="return confirm('Setujui pengajuan kegiatan ini? Course diklat akan dibuat dan dipublikasikan secara otomatis.')">
                        @csrf
                        <input type="hidden" name="review_notes" id="approve_notes">
                        <button type="submit" class="kinlo-btn kinlo-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Setujui & Buat Kursus</span>
                        </button>
                    </form>

                    {{-- Request Revision --}}
                    <button type="button" onclick="document.getElementById('revision-modal').classList.remove('hidden')"
                            class="kinlo-btn kinlo-btn-warn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>Minta Revisi</span>
                    </button>

                    {{-- Reject --}}
                    <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                            class="kinlo-btn kinlo-btn-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tolak</span>
                    </button>
                @endif

                {{-- Edit Button (Admin OPD) --}}
                @if(in_array($opdRequest->status, ['draft', 'revision_needed']) && (auth()->user()->isSuperAdmin() || $opdRequest->submitted_by === auth()->id()))
                    <a href="{{ route('admin.opd-requests.edit', $opdRequest) }}" class="kinlo-btn kinlo-btn-warn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Perbaiki & Kirim Ulang</span>
                    </a>
                @endif

                {{-- Delete / Cancel Button --}}
                @if(in_array($opdRequest->status, ['draft', 'pending', 'revision_needed']) && (auth()->user()->isSuperAdmin() || $opdRequest->submitted_by === auth()->id()))
                    <form action="{{ route('admin.opd-requests.destroy', $opdRequest) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus usulan kegiatan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="kinlo-btn kinlo-btn-ghost text-rose-600 hover:text-rose-700" title="Hapus Usulan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- KINLO STEPPER: ALUR SIKLUS HIDUP PENGUSULAN --}}
        <div class="mt-7 pt-6 border-t border-slate-200/80">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
                <span>Tahapan Siklus Hidup Usulan Pelatihan</span>
                <span class="text-slate-500 font-mono">Standar KAK Pasal VII — BKPSDM</span>
            </div>
            
            <div class="kinlo-stepper">
                {{-- Step 1 --}}
                <div class="kinlo-step-node is-done">
                    <div class="kinlo-step-circle">✓</div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">1. Inisiasi OPD</div>
                        <div class="text-[11px] text-slate-500 truncate">Rencana & Anggaran</div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="kinlo-step-node is-done">
                    <div class="kinlo-step-circle">✓</div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">2. Usulan Terkirim</div>
                        <div class="text-[11px] text-slate-500">{{ $opdRequest->created_at->format('d M Y') }}</div>
                    </div>
                </div>

                {{-- Step 3 --}}
                @php
                    $isStep3Done = in_array($opdRequest->status, ['approved', 'rejected', 'revision_needed']);
                    $isStep3Active = in_array($opdRequest->status, ['pending', 'under_review']);
                @endphp
                <div class="kinlo-step-node {{ $isStep3Done ? 'is-done' : ($isStep3Active ? 'is-active' : 'is-pending') }}">
                    <div class="kinlo-step-circle">
                        @if($isStep3Done)
                            ✓
                        @elseif($isStep3Active)
                            3
                        @else
                            3
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold {{ $isStep3Active ? 'text-cyan-900 font-extrabold' : 'text-slate-800' }}">3. Verifikasi BKPSDM</div>
                        <div class="text-[11px] text-slate-500 truncate">
                            @if($opdRequest->status === 'approved')
                                Disetujui
                            @elseif($opdRequest->status === 'rejected')
                                Ditolak
                            @elseif($opdRequest->status === 'revision_needed')
                                Minta Revisi
                            @else
                                Dalam Evaluasi
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Step 4 --}}
                @php
                    $isStep4Done = $opdRequest->status === 'approved';
                @endphp
                <div class="kinlo-step-node {{ $isStep4Done ? 'is-done' : 'is-pending' }}">
                    <div class="kinlo-step-circle">
                        @if($isStep4Done)
                            🎯
                        @else
                            4
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold {{ $isStep4Done ? 'text-emerald-900 font-extrabold' : 'text-slate-700' }}">4. Terbit & Berjalan</div>
                        <div class="text-[11px] text-slate-500 truncate">
                            @if($isStep4Done)
                                Kursus Aktif di Katalog
                            @else
                                Menunggu Review
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- REVISION / REJECTION / APPROVAL CALLOUT BANNER --}}
    @if($opdRequest->status === 'revision_needed' && $opdRequest->revision_request)
        <div class="kinlo-card p-5 border-l-4 border-l-orange-500 bg-orange-50/50">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center text-xl shrink-0">
                    ✏️
                </div>
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-orange-950 text-base">Permintaan Revisi dari BKPSDM</h3>
                        <span class="text-xs text-orange-700 font-semibold">Tindakan Diperlukan</span>
                    </div>
                    <p class="text-sm text-orange-900 leading-relaxed font-medium bg-white/70 p-3.5 rounded-xl border border-orange-200">
                        {{ $opdRequest->revision_request }}
                    </p>
                    @if($opdRequest->review_notes)
                        <p class="text-xs text-orange-800 italic mt-1">Catatan Tambahan: {{ $opdRequest->review_notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($opdRequest->status === 'rejected' && $opdRequest->review_notes)
        <div class="kinlo-card p-5 border-l-4 border-l-rose-500 bg-rose-50/50">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl shrink-0">
                    ❌
                </div>
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-rose-950 text-base">Pengajuan Ditolak oleh BKPSDM</h3>
                        <span class="text-xs text-rose-700 font-semibold">{{ $opdRequest->reviewed_at?->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-sm text-rose-900 leading-relaxed font-medium bg-white/70 p-3.5 rounded-xl border border-rose-200">
                        {{ $opdRequest->review_notes }}
                    </p>
                    <p class="text-xs text-rose-600">Diverifikasi oleh: <strong>{{ $opdRequest->reviewer?->name ?? 'Verifikator BKPSDM' }}</strong></p>
                </div>
            </div>
        </div>
    @endif

    @if($opdRequest->status === 'approved' && $opdRequest->course)
        <div class="kinlo-card p-5 border-l-4 border-l-emerald-500 bg-emerald-50/50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                        ✅
                    </div>
                    <div>
                        <h3 class="font-extrabold text-emerald-950 text-base">Pengajuan Telah Disetujui & Diterbitkan!</h3>
                        <p class="text-xs text-emerald-800 mt-0.5">
                            Materi diklat telah otomatis dikonversi menjadi kursus resmi dengan alokasi <strong>{{ $opdRequest->jp ?? 2 }} JP</strong>: 
                            <span class="font-bold underline">{{ $opdRequest->course->title }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('courses.show', $opdRequest->course->slug) }}" target="_blank"
                       class="kinlo-btn kinlo-btn-ghost text-xs">
                        <span>🌐 Lihat di Publik</span>
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.courses.edit', $opdRequest->course) }}"
                           class="kinlo-btn kinlo-btn-primary text-xs">
                            <span>⚙️ Kelola Kursus</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- MAIN 2-COLUMN GRID (KINLO ARCHITECTURE) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN (2 Col Span): SEGMENTED TABS & CURRICULUM --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Kinlo Segmented Tabs Header --}}
            <div class="kinlo-card p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="kinlo-tabs-wrap">
                        <button type="button" @click="activeTab = 'curriculum'"
                                :class="activeTab === 'curriculum' ? 'is-active' : ''"
                                class="kinlo-tab-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Kurikulum & Sasaran</span>
                        </button>

                        <button type="button" @click="activeTab = 'technical'"
                                :class="activeTab === 'technical' ? 'is-active' : ''"
                                class="kinlo-tab-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>Fasilitator & Pelaksanaan</span>
                        </button>

                        <button type="button" @click="activeTab = 'attachments'"
                                :class="activeTab === 'attachments' ? 'is-active' : ''"
                                class="kinlo-tab-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span>Lampiran & Berkas DPA</span>
                        </button>
                    </div>

                    <span class="text-xs text-slate-400 font-bold px-2">LMS KAB Berau v2.0</span>
                </div>
            </div>

            {{-- TAB 1: KURIKULUM & SASARAN --}}
            <div x-show="activeTab === 'curriculum'" x-transition class="space-y-4">
                
                {{-- Deskripsi Pelatihan --}}
                <div class="kinlo-card p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-800 flex items-center justify-center font-bold text-sm">
                            📝
                        </div>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Deskripsi & Urgensi Kegiatan</h2>
                    </div>
                    
                    <div class="kinlo-well p-4 text-slate-700 leading-relaxed text-sm">
                        {{ $opdRequest->description ?? 'Tidak ada deskripsi yang dilampirkan.' }}
                    </div>
                </div>

                {{-- Tujuan Pembelajaran --}}
                <div class="kinlo-card p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold text-sm">
                            🎯
                        </div>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Tujuan Pembelajaran & Kompetensi</h2>
                    </div>
                    
                    <div class="kinlo-well p-4 text-slate-700 leading-relaxed text-sm">
                        {{ $opdRequest->objectives ?? 'Tidak ada tujuan pembelajaran yang dirinci secara spesifik.' }}
                    </div>
                </div>

                {{-- Sasaran Peserta ASN --}}
                <div class="kinlo-card p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-800 flex items-center justify-center font-bold text-sm">
                            👥
                        </div>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Sasaran Aparatur Sipil Negara (ASN)</h2>
                    </div>
                    
                    <div class="kinlo-well p-4 text-slate-700 text-sm flex items-center justify-between">
                        <span>{{ $opdRequest->target_audience ?? 'Seluruh pegawai unit kerja terkait.' }}</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Target Kuota: {{ $opdRequest->estimated_participants ?? '-' }} ASN</span>
                    </div>
                </div>

                {{-- Catatan Pendukung --}}
                @if($opdRequest->supporting_notes)
                <div class="kinlo-card p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center font-bold text-sm">
                            📌
                        </div>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Catatan Pendukung dari OPD</h2>
                    </div>
                    <div class="kinlo-well p-4 text-slate-600 italic text-sm">
                        "{{ $opdRequest->supporting_notes }}"
                    </div>
                </div>
                @endif
            </div>

            {{-- TAB 2: FASILITATOR & TEKNIS PELAKSANAAN --}}
            <div x-show="activeTab === 'technical'" x-transition class="space-y-4" style="display: none;">
                <div class="kinlo-card p-6">
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span>🎤</span> Fasilitator & Tenaga Pengajar
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="kinlo-well p-4">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Narasumber</span>
                            <div class="text-base font-extrabold text-slate-900 mt-1">{{ $opdRequest->speaker_name ?? 'Ditentukan Menyusul' }}</div>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded text-[11px] font-semibold bg-cyan-100 text-cyan-800">Tenaga Ahli Terakreditasi</span>
                        </div>
                        <div class="kinlo-well p-4">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Metode Penyelenggaraan</span>
                            <div class="text-base font-extrabold text-slate-900 mt-1">{{ $opdRequest->learning_method ?? 'E-Learning' }}</div>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">Sertifikasi Jam Pelajaran (JP)</span>
                        </div>
                    </div>
                </div>

                <div class="kinlo-card p-6">
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span>📍</span> Lokasi & Jadwal Pelaksanaan
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="kinlo-well p-4">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lokasi / Ruang Kelas</span>
                            <div class="text-sm font-bold text-slate-800 mt-1">{{ $opdRequest->proposed_location ?? 'Portal LMS KAB Berau (Daring Mandiri)' }}</div>
                        </div>
                        <div class="kinlo-well p-4">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rentang Jadwal</span>
                            <div class="text-sm font-bold text-slate-800 mt-1">
                                @if($opdRequest->proposed_start_date)
                                    {{ $opdRequest->proposed_start_date->format('d M Y') }}
                                    @if($opdRequest->proposed_end_date)
                                        s/d {{ $opdRequest->proposed_end_date->format('d M Y') }}
                                    @endif
                                @else
                                    Fleksibel / Sesuai Jadwal Penayangan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: DOKUMEN & ANGGARAN --}}
            <div x-show="activeTab === 'attachments'" x-transition class="space-y-4" style="display: none;">
                <div class="kinlo-card p-6">
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span>💰</span> Pos Anggaran & Sumber Dana
                    </h2>
                    <div class="kinlo-well p-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sumber Anggaran</span>
                            <div class="text-base font-extrabold text-slate-900 mt-0.5">{{ $opdRequest->budget_source ?? 'DPA OPD Bersangkutan' }}</div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">APBD Kabupaten Berau</span>
                    </div>
                </div>

                <div class="kinlo-card p-6">
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span>📎</span> Berkas Lampiran Kerangka Acuan Kerja (KAK)
                    </h2>
                    @if($opdRequest->attachment_path)
                        <div class="kinlo-well p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-lg">
                                    📄
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-900">Dokumen Pendukung Usulan Kegiatan</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ basename($opdRequest->attachment_path) }}</div>
                                </div>
                            </div>
                            <a href="{{ asset($opdRequest->attachment_path) }}" target="_blank"
                               class="kinlo-btn kinlo-btn-primary text-xs">
                                <span>Unduh Berkas</span>
                            </a>
                        </div>
                    @else
                        <div class="kinlo-well p-6 text-center text-slate-400 text-sm">
                            <span>Tidak ada berkas fisik yang dilampirkan. Seluruh data diisi secara digital melalui formulir sistem.</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN (1 Col Span): KINLO SIGNATURE NIGHT CARD & OPD SUMMARY --}}
        <div class="space-y-6">

            {{-- SIGNATURE NIGHT CARD: DAMPAK JP & VALIDASI RESMI --}}
            <div class="kinlo-card-night p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-black uppercase tracking-wider text-cyan-300 bg-cyan-950/70 border border-cyan-800/80 px-2.5 py-0.5 rounded-full">
                        Analisis Jam Pelajaran
                    </span>
                    <span class="text-xs text-cyan-200/60 font-mono">LAN RI 20 JP</span>
                </div>

                <div class="my-3">
                    <div class="text-xs text-slate-300 font-semibold">Alokasi Jam Pelajaran (JP):</div>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-4xl sm:text-5xl font-black text-white tracking-tight">{{ $opdRequest->jp ?? 2 }}</span>
                        <span class="text-base font-extrabold text-cyan-300">JP / Pegawai</span>
                    </div>
                </div>

                {{-- Sub-metrics Tiles --}}
                <div class="grid grid-cols-2 gap-2.5 mt-5 pt-4 border-t border-white/10">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                        <span class="text-[10px] uppercase font-bold text-cyan-200/70 block">Target Peserta</span>
                        <span class="text-lg font-black text-white mt-0.5 block">{{ $opdRequest->estimated_participants ?? 0 }} <small class="text-xs font-normal text-slate-300">ASN</small></span>
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                        <span class="text-[10px] uppercase font-bold text-cyan-200/70 block">Total Jam OPD</span>
                        <span class="text-lg font-black text-cyan-300 mt-0.5 block">{{ ($opdRequest->jp ?? 2) * ($opdRequest->estimated_participants ?? 0) }} <small class="text-xs font-normal text-slate-300">JP</small></span>
                    </div>
                </div>

                {{-- Compliance progress notice --}}
                <div class="mt-4 pt-3 border-t border-white/10 text-[11.5px] text-slate-300 leading-relaxed flex items-start gap-2">
                    <span class="text-cyan-400">⚡</span>
                    <span>Setiap aparatur berhak atas minimal <strong>20 Jam Pelajaran</strong> pengembangan kompetensi per tahun. Pelatihan ini memenuhi <strong>{{ round((($opdRequest->jp ?? 2) / 20) * 100) }}%</strong> dari kewajiban tahunan aparatur.</span>
                </div>
            </div>

            {{-- INFO INSTANSI PENGUSUL CARD --}}
            <div class="kinlo-card p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold text-base">
                        🏛️
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 leading-tight">Unit Kerja Pengusul</h3>
                        <p class="text-[11px] text-slate-500">Pemerintah Kabupaten Berau</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-start pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500 font-semibold">Nama OPD</span>
                        <span class="font-extrabold text-slate-800 text-right max-w-[65%]">{{ $opdRequest->opd_name }}</span>
                    </div>

                    <div class="flex justify-between items-start pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500 font-semibold">Petugas Admin OPD</span>
                        <span class="font-bold text-slate-800 text-right">{{ $opdRequest->submitter?->name ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between items-start pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500 font-semibold">NIP Admin</span>
                        <span class="font-mono text-slate-700 text-right">{{ $opdRequest->submitter?->nip ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between items-start pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500 font-semibold">Email Dinas</span>
                        <span class="font-mono text-cyan-800 text-right truncate max-w-[65%]">{{ $opdRequest->submitter?->email ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-slate-500 font-semibold">ID Pengajuan</span>
                        <span class="font-mono text-slate-600">#OPD-{{ $opdRequest->id }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- KINLO REVISION MODAL --}}
<div id="revision-modal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="kinlo-card p-6 sm:p-7 max-w-lg w-full bg-white shadow-2xl relative">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center text-xl shrink-0">
                ✏️
            </div>
            <div>
                <h3 class="font-extrabold text-slate-900 text-lg leading-tight">Minta Revisi Proposal OPD</h3>
                <p class="text-xs text-slate-500">Berikan rujukan perbaikan yang jelas kepada unit kerja pengusul.</p>
            </div>
        </div>

        <form action="{{ route('admin.opd-requests.revision', $opdRequest) }}" method="POST" class="space-y-3 mt-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rincian Perbaikan yang Wajib Direvisi *</label>
                <textarea name="revision_request" rows="4" required placeholder="Contoh: Mohon lengkapi silabus kompetensi, format narasumber, dan sesuaikan jadwal dengan agenda BPSDM..."
                          class="w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none resize-none leading-relaxed"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea name="review_notes" rows="2" placeholder="Catatan internal pengingat..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-orange-500 outline-none resize-none"></textarea>
            </div>

            <div class="flex gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('revision-modal').classList.add('hidden')"
                        class="kinlo-btn kinlo-btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="kinlo-btn kinlo-btn-warn flex-1">
                    Kirim Permintaan Revisi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- KINLO REJECTION MODAL --}}
<div id="reject-modal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="kinlo-card p-6 sm:p-7 max-w-lg w-full bg-white shadow-2xl relative">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl shrink-0">
                ❌
            </div>
            <div>
                <h3 class="font-extrabold text-slate-900 text-lg leading-tight">Tolak Pengajuan Pelatihan OPD</h3>
                <p class="text-xs text-slate-500">Berikan pertimbangan penolakan secara resmi dan objektif.</p>
            </div>
        </div>

        <form action="{{ route('admin.opd-requests.reject', $opdRequest) }}" method="POST" class="space-y-3 mt-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alasan Resmi Penolakan (Min. 10 Karakter) *</label>
                <textarea name="review_notes" rows="4" required minlength="10" placeholder="Jelaskan alasan substansi atau anggaran penolakan usulan..."
                          class="w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none resize-none leading-relaxed"></textarea>
            </div>

            <div class="flex gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                        class="kinlo-btn kinlo-btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="kinlo-btn kinlo-btn-danger flex-1"
                        onclick="return confirm('Apakah Anda yakin ingin menolak usulan kegiatan pelatihan ini?')">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
