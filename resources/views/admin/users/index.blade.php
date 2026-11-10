@extends('layouts.admin')

@section('page_title', 'Manajemen User & Hak Akses')

@section('content')
<!-- Google Font: Urbanist (Kinlo signature font) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

<style>
/* ============================================================
   KINLO DESIGN SYSTEM TOKENS & ANIMATIONS
   Adapted for LMS Pemerintah Kabupaten Berau - User Management
   ============================================================ */
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
  --kinlo-r-md: 14px;
  --kinlo-r-lg: 18px;
  --kinlo-r-xl: 22px;
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

/* Kinlo Role Stat Tiles (H-Stat) */
.kinlo-hstat {
  background: rgba(255, 255, 255, 0.7);
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px var(--kinlo-line);
  border-radius: 16px;
  padding: 12px 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: all 0.2s var(--kinlo-ease);
  position: relative;
  overflow: hidden;
}
.kinlo-hstat:hover {
  background: #ffffff;
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px rgba(23, 37, 45, 0.15), 0 12px 28px -10px rgba(23, 37, 45, 0.18);
  transform: translateY(-2px);
}
.kinlo-hstat.is-selected {
  background: #ffffff;
  box-shadow: inset 0 0 0 2px var(--kinlo-accent), 0 8px 24px -8px rgba(14, 108, 132, 0.35);
}

/* Kinlo Interactive Buttons */
.kinlo-btn {
  height: 38px;
  font-size: 13px;
  font-weight: 700;
  border-radius: 12px;
  padding: 0 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
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
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px rgba(23, 37, 45, 0.2), 0 6px 14px -6px rgba(23, 37, 45, 0.12);
}
.kinlo-btn-export {
  background: linear-gradient(135deg, #0e6c84 0%, #153c48 100%);
  color: #ffffff;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25), 0 8px 18px -6px rgba(14, 108, 132, 0.4);
}
.kinlo-btn-export:hover {
  background: linear-gradient(135deg, #127d98 0%, #1c4d5c 100%);
  transform: translateY(-1.5px);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35), 0 12px 22px -6px rgba(14, 108, 132, 0.55);
}

/* Status & Role Pills */
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
.kinlo-pill-dot::before {
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

/* Kinlo Role Tabs (List per Role) */
.kinlo-tabs-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(23, 37, 45, 0.05);
  padding: 4px;
  border-radius: 14px;
  overflow-x: auto;
  box-shadow: inset 0 1px 2px rgba(23, 37, 45, 0.06);
}
.kinlo-tab-btn {
  height: 36px;
  padding: 0 14px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 700;
  color: var(--kinlo-ink-muted);
  display: inline-flex;
  align-items: center;
  gap: 7px;
  transition: all 0.2s var(--kinlo-ease);
  cursor: pointer;
  white-space: nowrap;
  text-decoration: none;
}
.kinlo-tab-btn:hover {
  color: var(--kinlo-ink);
  background: rgba(255, 255, 255, 0.6);
}
.kinlo-tab-btn.is-active {
  background: var(--kinlo-accent);
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(14, 108, 132, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.25);
}
.kinlo-tab-btn.is-active .kinlo-tab-cnt {
  background: rgba(255, 255, 255, 0.22);
  color: #ffffff;
}
.kinlo-tab-cnt {
  font-size: 10.5px;
  padding: 1.5px 6px;
  border-radius: 99px;
  background: rgba(23, 37, 45, 0.08);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
}
</style>

<div class="kinlo-root space-y-6 pb-12" x-data="{ 
    createModal: false, 
    editModal: false, 
    importModal: false,
    resetPasswordModal: false,
    exportDropdown: false,
    activeRole: '{{ request('role') && in_array(request('role'), ['super_admin','admin_opd','asn_learner','narasumber','pimpinan','reviewer_talent']) ? request('role') : 'all' }}',
    visibleCount: {{ $stats['total'] ?? $users->total() }},
    selectedUser: { id: null, name: '', email: '', role: 'asn_learner', opd_name: '', bagian: '', jabatan: '', nip: '', nik: '', phone: '', is_active: 1 },
    resetUser: { id: null, name: '', email: '' },
    exportBaseUrl: '{{ route('admin.users.export') }}',

    roleCounts: {
        all: {{ $stats['total'] ?? $users->total() }},
        super_admin: {{ $stats['super_admin'] ?? 0 }},
        admin_opd: {{ $stats['admin_opd'] ?? 0 }},
        asn_learner: {{ $stats['asn_learner'] ?? 0 }},
        narasumber: {{ $stats['narasumber'] ?? 0 }},
        pimpinan: {{ $stats['pimpinan'] ?? 0 }},
        reviewer_talent: {{ $stats['reviewer_talent'] ?? 0 }}
    },

    roleLabels: {
        super_admin: 'Super Admin / BKPSDM',
        admin_opd: 'Admin OPD',
        asn_learner: 'ASN / Learner',
        narasumber: 'Narasumber',
        pimpinan: 'Pimpinan',
        reviewer_talent: 'Reviewer Talent'
    },

    switchRole(role) {
        this.activeRole = role || 'all';
        this.updateVisibleCount();
        
        // Update URL bar seamlessly without full-page reload
        try {
            const url = new URL(window.location.href);
            if (this.activeRole === 'all') {
                url.searchParams.delete('role');
            } else {
                url.searchParams.set('role', this.activeRole);
            }
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}
    },

    isRowVisible(roleKey) {
        if (this.activeRole === 'all') return true;
        return this.activeRole === roleKey;
    },

    updateVisibleCount() {
        if (this.activeRole === 'all') {
            this.visibleCount = this.roleCounts.all;
        } else {
            this.visibleCount = this.roleCounts[this.activeRole] ?? 0;
        }
    },

    init() {
        this.updateVisibleCount();
    }
}">

        {{-- KINLO HERO BANNER CARD --}}
    <div class="kinlo-card p-6 lg:p-7">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            
            {{-- Title & Breadcrumbs --}}
            <div class="space-y-1.5 flex-1 min-w-0">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                    <span>Admin</span>
                    <span>&bull;</span>
                    <span class="text-cyan-800">Manajemen Pengguna & Hak Akses SPBE</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight whitespace-nowrap shrink-0">
                        👥 Manajemen User & Hak Akses
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-cyan-100 text-cyan-900 border border-cyan-200 whitespace-nowrap">
                        <span x-text="visibleCount">{{ $users->total() }}</span> Pengguna
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Kelola akun aparatur sipil negara, penugasan peran hak akses, sinkronisasi SIMPEG, aktivasi akun, dan kredensial resmi.
                </p>
            </div>

            {{-- Action Buttons Toolbar --}}
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                
                {{-- Satu Tombol Export Utama (Sesuai Filter / Role Aktif) --}}
                <div class="relative" @click.away="exportDropdown = false">
                    <button @click="exportDropdown = !exportDropdown" type="button" class="kinlo-btn kinlo-btn-export" title="Unduh data pengguna dalam format CSV/Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Export CSV</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="exportDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    {{-- Kinlo Glass Dropdown Menu --}}
                    <div x-show="exportDropdown" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute right-0 mt-2 w-64 rounded-2xl bg-white/95 backdrop-blur-xl border border-slate-200 shadow-2xl p-2 z-50 text-xs font-semibold text-slate-800">
                        <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            Pilihan Export Data:
                        </div>

                        <div class="py-1 space-y-0.5">
                            <a :href="exportBaseUrl + (activeRole !== 'all' ? '?role=' + activeRole : '?role=all')" 
                               class="flex items-center justify-between px-3 py-2 rounded-xl bg-cyan-50 text-cyan-900 font-bold hover:bg-cyan-100 transition">
                                <span>📥 Export Peran Aktif (<span x-text="activeRole === 'all' ? 'Semua' : activeRole"></span>)</span>
                                <span class="text-[10px] font-mono" x-text="visibleCount"></span>
                            </a>
                            <a :href="exportBaseUrl + '?role=all'" 
                               class="flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition">
                                <span>🌐 Export Semua Role</span>
                                <span class="text-[10px] font-mono">{{ $stats['total'] ?? $users->total() }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Import CSV --}}
                <button @click="importModal = true" type="button" class="kinlo-btn kinlo-btn-ghost bg-emerald-50/80 text-emerald-900 border border-emerald-200 hover:bg-emerald-100">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import CSV</span>
                </button>

                {{-- Tarik SIMPEG --}}
                <a href="{{ route('admin.simpeg.index') }}" 
                   class="kinlo-btn kinlo-btn-ghost text-teal-900 hover:bg-teal-50"
                   title="Integrasi REST API SIMPEG Kabupaten">
                    <span>🏛️</span>
                    <span>Tarik SIMPEG</span>
                </a>

                {{-- Tambah Pengguna --}}
                <button @click="createModal = true" type="button" class="kinlo-btn kinlo-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Pengguna</span>
                </button>
            </div>
        </div>

        @php
        $roleConfigs = [
            'super_admin' => [
                'label' => 'Super Admin / BKPSDM',
                'short' => 'Super Admin',
                'icon' => '🛡️',
                'sub' => 'Total Pengelola',
                'count' => $roleCounts['super_admin'] ?? ($stats['super_admin'] ?? 0),
            ],
            'admin_opd' => [
                'label' => 'Admin OPD',
                'short' => 'Admin OPD',
                'icon' => '🏛️',
                'sub' => 'Perangkat Daerah',
                'count' => $roleCounts['admin_opd'] ?? ($stats['admin_opd'] ?? 0),
            ],
            'asn_learner' => [
                'label' => 'ASN / Learner',
                'short' => 'ASN / Learner',
                'icon' => '🎓',
                'sub' => 'Peserta Pembelajaran',
                'count' => $roleCounts['asn_learner'] ?? ($stats['asn_learner'] ?? 0),
            ],
            'narasumber' => [
                'label' => 'Narasumber',
                'short' => 'Narasumber',
                'icon' => '👨‍🏫',
                'sub' => 'Pemateri & Pengampu',
                'count' => $roleCounts['narasumber'] ?? ($stats['narasumber'] ?? 0),
            ],
            'pimpinan' => [
                'label' => 'Pimpinan',
                'short' => 'Pimpinan',
                'icon' => '👔',
                'sub' => 'Monitoring & Eksekutif',
                'count' => $roleCounts['pimpinan'] ?? ($stats['pimpinan'] ?? 0),
            ],
            'reviewer_talent' => [
                'label' => 'Reviewer Talent',
                'short' => 'Reviewer',
                'icon' => '⭐',
                'sub' => 'Penilai Kompetensi',
                'count' => $roleCounts['reviewer_talent'] ?? ($stats['reviewer_talent'] ?? 0),
            ],
        ];
        @endphp

        {{-- MENU BILAH TAB PER ROLE UNTUK MENAMPILKAN LIST (INSTANT FILTER 0MS) --}}
        <div class="mt-6 pt-5 border-t border-slate-200/70">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="kinlo-tabs-wrap">
                    {{-- Tab: Semua Role --}}
                    <button type="button" 
                            @click="switchRole('all')" 
                            class="kinlo-tab-btn cursor-pointer" 
                            :class="activeRole === 'all' ? 'is-active' : ''">
                        <span>🌐 Semua Peran</span>
                        <span class="kinlo-tab-cnt">{{ $stats['total'] ?? $users->total() }}</span>
                    </button>

                    {{-- Tabs: Masing-masing Role --}}
                    @foreach($roleConfigs as $rKey => $cfg)
                    <button type="button" 
                            @click="switchRole('{{ $rKey }}')" 
                            class="kinlo-tab-btn cursor-pointer" 
                            :class="activeRole === '{{ $rKey }}' ? 'is-active' : ''">
                        <span>{{ $cfg['icon'] }}</span>
                        <span>{{ $cfg['short'] }}</span>
                        <span class="kinlo-tab-cnt">{{ $cfg['count'] }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- Status Filter Aktif --}}
                <div class="flex items-center gap-2" x-show="activeRole !== 'all'" x-cloak>
                    <span class="text-xs text-slate-500">
                        Menampilkan: <strong class="text-cyan-900" x-text="roleLabels[activeRole] || activeRole"></strong>
                    </span>
                    <button type="button" @click="switchRole('all')" class="text-xs font-bold text-rose-600 hover:underline cursor-pointer">
                        ✕ Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- KINLO ROLE STAT TILES (6 ROLES - BERSIH TANPA ICON DOWNLOAD GANDA) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mt-4">
            @foreach($roleConfigs as $rKey => $cfg)
            <button type="button"
                    @click="switchRole(activeRole === '{{ $rKey }}' ? 'all' : '{{ $rKey }}')" 
                    class="kinlo-hstat group block text-left cursor-pointer transition-all duration-200"
                    :class="activeRole === '{{ $rKey }}' ? 'is-selected ring-2 ring-cyan-700 bg-white shadow-md' : ''"
                    title="Klik untuk memfilter daftar pengguna peran {{ $cfg['label'] }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-cyan-800 transition truncate">{{ $cfg['label'] }}</span>
                    <span class="text-sm shrink-0">{{ $cfg['icon'] }}</span>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-1.5">{{ $cfg['count'] }}</div>
                <div class="flex items-center justify-between mt-1 text-[10px]">
                    <p class="text-slate-400 truncate">{{ $cfg['sub'] }}</p>
                    <span class="font-bold text-cyan-800 shrink-0" x-show="activeRole === '{{ $rKey }}'" x-cloak>Aktif ✓</span>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    {{-- KINLO SEARCH & FILTER BAR (LAYOUT SEJAJAR DENGAN FIELD LEBIH TINGGI & BESAR) --}}
    <div class="kinlo-card p-5 lg:p-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="w-full flex flex-col lg:flex-row gap-2.5 items-end">
            {{-- Cari Pengguna (Flex-1 melebar penuh mengisi ruang sisa) --}}
            <div class="flex-1 w-full min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider whitespace-nowrap">Cari Pengguna</label>
                <div class="relative flex items-center">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, NIP, OPD, jabatan, nomor WhatsApp..."
                           style="padding-left: 44px; padding-right: 14px;"
                           class="w-full h-[46px] text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 transition shadow-sm font-medium text-slate-800 placeholder:text-slate-400">
                </div>
            </div>

            {{-- Filter Peran (Role) --}}
            <div class="w-full sm:w-auto lg:w-44 shrink-0">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider whitespace-nowrap">Peran (Role)</label>
                <select name="role" x-model="activeRole" @change="switchRole($event.target.value)" 
                        class="w-full px-3.5 h-[46px] text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-700 shadow-sm cursor-pointer">
                    <option value="all">Semua Peran (Role)</option>
                    @foreach($rolesDefinition as $rKey => $rDef)
                        <option value="{{ $rKey }}">
                            {{ $rDef['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Akun --}}
            <div class="w-full sm:w-auto lg:w-36 shrink-0">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider whitespace-nowrap">Status Akun</label>
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3.5 h-[46px] text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-700 shadow-sm cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 Akun Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>🔴 Akun Nonaktif</option>
                </select>
            </div>

            {{-- Filter OPD --}}
            @if(!empty($opdList))
            <div class="w-full sm:w-auto lg:w-44 shrink-0">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider whitespace-nowrap">Filter OPD</label>
                <select name="opd" onchange="this.form.submit()" 
                        class="w-full px-3.5 h-[46px] text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-700 shadow-sm truncate cursor-pointer">
                    <option value="">Semua OPD / Instansi</option>
                    @foreach($opdList as $opdItem)
                        <option value="{{ $opdItem }}" {{ request('opd') === $opdItem ? 'selected' : '' }}>
                            {{ $opdItem }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Tombol Filter & Reset --}}
            <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
                <button type="submit" class="kinlo-btn kinlo-btn-primary px-5 h-[46px] text-sm font-bold justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['q', 'role', 'status', 'opd']))
                <a href="{{ route('admin.users.index') }}" class="kinlo-btn kinlo-btn-ghost px-4 h-[46px] text-sm font-bold justify-center" title="Hapus semua filter">
                    <span>Reset</span>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- KINLO USERS TABLE --}}
    <div class="kinlo-card overflow-hidden">
        <div class="p-5 border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base">Daftar Pengguna Sistem LMS</h3>
                <p class="text-xs text-slate-500 mt-0.5">Menampilkan aparatur sipil negara dan pengelola yang terdaftar dalam basis data.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 font-mono">
                <span>Ditemukan <span x-text="visibleCount">{{ $users->total() }}</span> akun</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Pengguna & Kontak</th>
                        <th class="py-3.5 px-4">OPD & Jabatan</th>
                        {{-- PERAN ROLE (REQUEST #1 FIXED) --}}
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4 text-center">Status Akun</th>
                        <th class="py-3.5 px-4 text-center">Partisipasi</th>
                        <th class="py-3.5 px-4">Terdaftar</th>
                        <th class="py-3.5 px-5 text-right">Aksi & Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    @php
                        $roleKey = match($user->role) {
                            'admin', 'bkpsdm' => 'super_admin',
                            'student'         => 'asn_learner',
                            'instructor'      => 'narasumber',
                            default           => $user->role,
                        };
                        $roleDef = $rolesDefinition[$roleKey] ?? [
                            'name'        => ucfirst($user->role),
                            'badge_color' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'icon'        => '👤'
                        ];
                    @endphp
                    <tr x-show="isRowVisible('{{ $roleKey }}')" 
                        x-transition:enter="transition ease-out duration-150" 
                        x-transition:enter-start="opacity-0 translate-y-1" 
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="hover:bg-slate-50/80 transition group {{ !$user->is_active ? 'bg-slate-50/40 opacity-70' : '' }}">
                        
                        {{-- User Info & Contact --}}
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-800 to-teal-500 text-white font-extrabold flex items-center justify-center text-xs shadow-sm shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-extrabold text-slate-900 group-hover:text-cyan-800 transition truncate max-w-xs">
                                            {{ $user->name }}
                                        </a>
                                        @if($user->nip)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">ASN</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $user->email }}</div>
                                    @if($user->phone)
                                        <div class="text-[10px] text-slate-400 mt-0.5">📞 {{ $user->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- OPD & Jabatan --}}
                        <td class="py-3.5 px-4 text-slate-600">
                            <div class="font-bold text-slate-800 max-w-xs truncate">{{ $user->opd_name ?: ($user->headline ?: '-') }}</div>
                            @if($user->jabatan)
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $user->jabatan }}</div>
                            @endif
                            @if($user->nip)
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">NIP: {{ $user->nip }}</div>
                            @endif
                        </td>

                        {{-- PERAN (ROLE) - TULISAN TAMPIL JELAS (FIX REQUEST #1) --}}
                        <td class="py-3.5 px-4">
                            <span class="kinlo-pill {{ $roleDef['badge_color'] }}">
                                <span>{{ $roleDef['icon'] }}</span>
                                <span class="font-bold tracking-tight">{{ $roleDef['name'] }}</span>
                            </span>
                        </td>

                        {{-- Status Akun --}}
                        <td class="py-3.5 px-4 text-center">
                            @if($user->is_active)
                                <span class="kinlo-pill kinlo-pill-dot kinlo-pill-pulse bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="kinlo-pill kinlo-pill-dot bg-rose-50 text-rose-700 border border-rose-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- Partisipasi --}}
                        <td class="py-3.5 px-4 text-center">
                            <div class="inline-flex items-center gap-1.5 p-1 rounded-xl bg-slate-100/80 border border-slate-200/60">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-slate-700" title="Kursus Diikuti">
                                    📚 {{ $user->enrollments_count }}
                                </span>
                                <span class="text-slate-300">|</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-emerald-700" title="Sertifikat">
                                    🎓 {{ $user->certificates_count }}
                                </span>
                            </div>
                        </td>

                        {{-- Terdaftar --}}
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                            {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                        </td>

                        {{-- Aksi --}}
                        <td class="py-3.5 px-5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Detail --}}
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-900 border border-cyan-200 text-xs font-bold hover:bg-cyan-100 transition">
                                    Detail
                                </a>

                                {{-- Edit Modal Trigger --}}
                                <button type="button"
                                        @click="
                                            selectedUser = {
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}',
                                                role: '{{ $user->role }}',
                                                opd_name: '{{ addslashes($user->opd_name ?? '') }}',
                                                bagian: '{{ addslashes($user->bagian ?? '') }}',
                                                jabatan: '{{ addslashes($user->jabatan ?? '') }}',
                                                nip: '{{ addslashes($user->nip ?? '') }}',
                                                nik: '{{ addslashes($user->nik ?? '') }}',
                                                phone: '{{ addslashes($user->phone ?? '') }}',
                                                is_active: {{ $user->is_active ? 1 : 0 }}
                                            };
                                            editModal = true;
                                        "
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-amber-700 hover:bg-amber-50 transition"
                                        title="Edit Data & Role">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                {{-- Toggle Status Form --}}
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $user->name }}?')">
                                    @csrf
                                    <button type="submit" 
                                            class="p-1.5 rounded-lg {{ $user->is_active ? 'text-slate-400 hover:text-rose-600 hover:bg-rose-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' }} transition"
                                            title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                        @if($user->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                @endif

                                {{-- Reset Password Trigger --}}
                                <button type="button"
                                        @click="
                                            resetUser = {
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}'
                                            };
                                            resetPasswordModal = true;
                                        "
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-brand-600 hover:bg-brand-50 transition"
                                        title="Reset Password Akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-slate-400">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-3xl mx-auto mb-3">
                                📭
                            </div>
                            <div class="text-sm font-extrabold text-slate-700">Tidak ada data pengguna ditemukan</div>
                            <p class="text-xs text-slate-400 mt-1">Sesuaikan kata kunci pencarian atau reset filter untuk menampilkan data.</p>
                        </td>
                    </tr>
                    <tr x-show="visibleCount === 0" x-cloak>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="text-3xl mb-2">🔍</div>
                            <div class="text-sm font-extrabold text-slate-700">Tidak ada data pengguna untuk peran ini</div>
                            <p class="text-xs text-slate-400 mt-1">Klik tombol <button type="button" @click="switchRole('all')" class="text-cyan-800 font-bold hover:underline cursor-pointer">Semua Peran</button> untuk melihat seluruh pengguna.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- KINLO MODAL: CREATE USER --}}
    <div x-show="createModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4">
        <div @click.away="createModal = false" 
             x-show="createModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="kinlo-card bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Tambah Pengguna Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun pengguna baru beserta peran akses (Role).</p>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-5 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso, S.Kom"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Resmi <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="budi@instansi.go.id"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip" placeholder="198701012015031001"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">NIK (KTP)</label>
                        <input type="text" name="nik" placeholder="3201..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Peran Akses (Role) <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-900">
                            @foreach($rolesDefinition as $rKey => $rDef)
                                <option value="{{ $rKey }}" {{ $rKey === 'asn_learner' ? 'selected' : '' }}>
                                    {{ $rDef['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" placeholder="08123456789"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Instansi / OPD</label>
                        <input type="text" name="opd_name" placeholder="Contoh: Diskominfo"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Bagian / Bidang</label>
                        <input type="text" name="bagian" placeholder="Contoh: Bidang Aptika"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" placeholder="Contoh: Pranata Komputer Ahli Muda"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Password Awal <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Ketik ulang password"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked class="w-4 h-4 rounded text-cyan-800 focus:ring-cyan-600 border-slate-300">
                    <label for="create_is_active" class="text-xs font-semibold text-slate-700">Langsung aktifkan akun ini (dapat login)</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="createModal = false" type="button" class="kinlo-btn kinlo-btn-ghost">
                        Batal
                    </button>
                    <button type="submit" class="kinlo-btn kinlo-btn-primary">
                        Simpan Pengguna Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KINLO MODAL: EDIT USER --}}
    <div x-show="editModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4">
        <div @click.away="editModal = false" 
             x-show="editModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="kinlo-card bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Edit Profil & Peran Pengguna</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="selectedUser.name + ' (' + selectedUser.email + ')'"></p>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/admin/users/' + selectedUser.id" method="POST" class="p-5 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="selectedUser.name" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Resmi <span class="text-red-500">*</span></label>
                        <input type="email" name="email" x-model="selectedUser.email" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Peran Akses (Role) <span class="text-red-500">*</span></label>
                        <select name="role" x-model="selectedUser.role" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-900">
                            @foreach($rolesDefinition as $rKey => $rDef)
                                <option value="{{ $rKey }}">{{ $rDef['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Status Akun</label>
                        <select name="is_active" x-model="selectedUser.is_active" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-900">
                            <option :value="1">🟢 Aktif (Dapat Login)</option>
                            <option :value="0">🔴 Nonaktif (Login Diblokir)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">NIP</label>
                        <input type="text" name="nip" x-model="selectedUser.nip"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">NIK</label>
                        <input type="text" name="nik" x-model="selectedUser.nik"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Instansi / OPD</label>
                        <input type="text" name="opd_name" x-model="selectedUser.opd_name"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Bagian / Bidang</label>
                        <input type="text" name="bagian" x-model="selectedUser.bagian"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" x-model="selectedUser.jabatan"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" x-model="selectedUser.phone"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="editModal = false" type="button" class="kinlo-btn kinlo-btn-ghost">
                        Batal
                    </button>
                    <button type="submit" class="kinlo-btn kinlo-btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KINLO MODAL: RESET PASSWORD --}}
    <div x-show="resetPasswordModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4">
        <div @click.away="resetPasswordModal = false" 
             x-show="resetPasswordModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="kinlo-card bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Reset Password Akun</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="resetUser.name + ' (' + resetUser.email + ')'"></p>
                </div>
                <button @click="resetPasswordModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/admin/users/' + resetUser.id + '/reset-password'" method="POST" class="p-5 space-y-4">
                @csrf

                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 leading-relaxed">
                    <p class="font-bold">⚠️ Perhatian Pengelola:</p>
                    <p class="mt-0.5">Password baru akan segera aktif seketika. Jika dikosongkan, sistem otomatis membuat sandi acak 8 karakter.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="new_password" minlength="6" placeholder="Kosongkan jika ingin generate acak"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="resetPasswordModal = false" type="button" class="kinlo-btn kinlo-btn-ghost">
                        Batal
                    </button>
                    <button type="submit" class="kinlo-btn kinlo-btn-primary bg-rose-600 hover:bg-rose-700">
                        Reset Password Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KINLO MODAL: IMPORT CSV --}}
    <div x-show="importModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4">
        <div @click.away="importModal = false" 
             x-show="importModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="kinlo-card bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg">
                        📥
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Import & Migrasi Data ASN (CSV)</h3>
                        <p class="text-xs text-slate-500">Penyedia migrasi data awal kepegawaian standar BKPSDM</p>
                    </div>
                </div>
                <button @click="importModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5 text-xs">
                <!-- Step 1: Download Template -->
                <div class="p-4 rounded-2xl bg-cyan-50/70 border border-cyan-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <div class="font-extrabold text-cyan-950 text-xs flex items-center gap-1.5">
                            <span>📄</span>
                            <span>Langkah 1: Unduh Format Template CSV</span>
                        </div>
                        <p class="text-[11px] text-cyan-800/80 leading-snug">
                            Header Bahasa Indonesia standar BKPSDM (Tanpa NIK) yang aman dan rapi dibuka Excel.
                        </p>
                    </div>
                    <a href="{{ route('admin.users.template.download') }}" 
                       class="kinlo-btn kinlo-btn-primary text-xs shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Template</span>
                    </a>
                </div>

                <!-- Info Box: Petunjuk Sederhana & Fitur Mutasi -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 space-y-1.5 text-[11px] leading-relaxed">
                    <div class="font-bold text-slate-800 flex items-center gap-1">
                        <span>💡</span>
                        <span>Fitur Sinkronisasi & Mutasi Otomatis (Upsert):</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                        <li>Jika <strong>NIP sudah terdaftar</strong>, data jabatan/OPD akan otomatis dimutasi tanpa membuat akun ganda.</li>
                        <li>Format kolom: <code>nip, nama_lengkap, email, nama_opd, bagian, jabatan, pangkat_golongan, nomor_hp, peran, password</code></li>
                        <li>Kolom <strong>password</strong> opsional (default: <code>Asn12345!</code>).</li>
                    </ul>
                </div>

                <!-- Step 2: Form Upload CSV -->
                <form action="{{ route('admin.users.import.csv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-extrabold text-slate-800 mb-1.5">Langkah 2: Pilih File CSV Hasil Pengisian</label>
                        <input type="file" name="csv_file" accept=".csv,text/csv" required
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <p class="text-[10px] text-slate-400 mt-1">Ukuran berkas maksimal 10 MB. Berekstensi .csv</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button @click="importModal = false" type="button" class="kinlo-btn kinlo-btn-ghost">
                            Batal
                        </button>
                        <button type="submit" class="kinlo-btn kinlo-btn-primary bg-emerald-600 hover:bg-emerald-700">
                            <span>🚀 Mulai Proses Migrasi Data ASN</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
