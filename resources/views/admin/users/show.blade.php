@extends('layouts.admin')

@section('page_title', 'Detail Profil User: ' . $user->name)

@section('content')
<div class="space-y-6" x-data="{ editModal: false, resetPasswordModal: false }">

    <!-- Top Action Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" 
               class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                    @if($user->nip)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">ASN</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">Umum</span>
                    @endif
                    {!! $user->role_badge !!}
                    @if($user->is_active)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">🟢 Aktif</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">🔴 Nonaktif</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $user->email }}
                    @if($user->nip) • NIP: <span class="font-mono font-semibold">{{ $user->nip }}</span> @endif
                    @if($user->opd_name) • 🏛️ {{ $user->opd_name }} @endif
                    @if($user->jabatan) • {{ $user->jabatan }} @endif
                    @if($user->phone) • 📞 {{ $user->phone }} @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Reset Password Button -->
            <button @click="resetPasswordModal = true" 
                    class="px-3 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition shadow-2xs flex items-center gap-1.5">
                <span>🔑</span>
                <span>Reset Password</span>
            </button>

            <!-- Toggle Active/Inactive -->
            @if(auth()->id() !== $user->id)
                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline"
                      onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun ini?')">
                    @csrf
                    <button type="submit" 
                            class="px-3 py-2 rounded-xl text-xs font-bold {{ $user->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }} transition shadow-2xs flex items-center gap-1.5">
                        <span>{{ $user->is_active ? '🚫' : '✅' }}</span>
                        <span>{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan Akun' }}</span>
                    </button>
                </form>
            @endif

            <!-- Edit Modal Trigger -->
            <button @click="editModal = true" 
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit Profil & Role</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kursus Diikuti</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $enrollments->count() }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sedang Berjalan</p>
            <p class="text-2xl font-black text-amber-500 mt-1">{{ $inProgressCount }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sertifikat Terbit</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $certificates->count() }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Jam Belajar</p>
            <p class="text-2xl font-black text-teal-600 mt-1">{{ number_format($totalLearningHours, 1) }} <span class="text-xs font-bold text-slate-400">Jam</span></p>
        </div>
    </div>

    <!-- SECTION 1: JP TRACKER (CIRCULAR GAUGE & YEARLY STATS) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Circular Gauge -->
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 flex flex-col items-center justify-center">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-4">Target JP Tahun {{ $jpYearLabel }}</p>
            
            @php
                $percent = $jpData['percent'];
                $radius = 70;
                $circumference = 2 * pi() * $radius;
                $offset = $circumference - ($percent / 100) * $circumference;
                $gaugeColor = $jpData['achieved'] ? '#059669' : ($percent >= 50 ? '#0d9488' : '#f59e0b');
            @endphp
            <div class="relative w-44 h-44">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 160 160">
                    <circle cx="80" cy="80" r="{{ $radius }}" stroke="#f1f5f9" stroke-width="12" fill="none"/>
                    <circle cx="80" cy="80" r="{{ $radius }}" stroke="{{ $gaugeColor }}" stroke-width="12" fill="none"
                            stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                            stroke-linecap="round" class="transition-all duration-1000 ease-out"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $jpData['total'] }}</span>
                    <span class="text-xs text-slate-400 font-semibold">dari {{ $jpData['target'] }} JP</span>
                </div>
            </div>

            <!-- Breakdown -->
            <div class="w-full space-y-2 mt-4">
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-1.5 text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-brand-600"></span> JP Kursus Internal
                    </span>
                    <span class="font-bold text-slate-900">{{ $jpData['internal'] }} JP</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-1.5 text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span> JP Diklat Eksternal
                    </span>
                    <span class="font-bold text-slate-900">{{ $jpData['external'] }} JP</span>
                </div>
                <div class="border-t border-slate-100 pt-2 mt-2">
                    @if($jpData['achieved'])
                        <div class="flex items-center gap-1.5 justify-center px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Target JP Tercapai! 🎉
                        </div>
                    @else
                        <div class="flex items-center gap-1.5 justify-center px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Kurang {{ $jpData['target'] - $jpData['total'] }} JP lagi
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Yearly JP Stats & Overview -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span>📊</span>
                        <span>Akumulasi Jam Pelajaran (JP) Per Tahun</span>
                    </h2>
                    <span class="text-xs text-slate-400 font-mono">Target: {{ $user->jp_target_per_year ?? $jpData['target'] }} JP/Tahun</span>
                </div>

                <div class="space-y-3">
                    @foreach($yearlyStats as $stat)
                        @php
                            $barPercent = $stat['target'] > 0 ? min(100, ($stat['total'] / $stat['target']) * 100) : 0;
                            $barColor = $stat['achieved'] ? 'from-emerald-500 to-teal-400' : ($barPercent >= 50 ? 'from-brand-600 to-teal-500' : 'from-amber-400 to-amber-500');
                        @endphp
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold text-slate-500 w-10 shrink-0">{{ $stat['year'] }}</span>
                            <div class="flex-1">
                                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r {{ $barColor }} h-3 rounded-full transition-all duration-700" style="width: {{ $barPercent }}%"></div>
                                </div>
                            </div>
                            <div class="text-xs font-bold text-slate-700 w-24 text-right shrink-0">
                                {{ $stat['total'] }}/{{ $stat['target'] }} JP
                                @if($stat['achieved']) <span class="text-emerald-500">✓</span> @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Profile Meta Detail Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Informasi Akun & Kepegawaian</h3>
                    @if($user->is_active)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">🟢 Akun Aktif (Dapat Login)</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">🔴 Akun Nonaktif (Diblokir)</span>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-600">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase">Perangkat Daerah / OPD:</span>
                        <span class="font-bold text-slate-900">{{ $user->opd_name ?: ($user->headline ?: 'Belum diisi') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase">Jabatan:</span>
                        <span class="font-semibold text-slate-800">{{ $user->jabatan ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase">Pangkat / Golongan:</span>
                        <span class="font-semibold text-slate-800">{{ $user->pangkat_golongan ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase">Nomor Induk Pegawai (NIP):</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $user->nip ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase">Nomor Induk Kependudukan (NIK):</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $user->nik ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase">Terdaftar Pada:</span>
                        <span class="font-mono">{{ $user->created_at->format('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVIGATION TABS: INTERNAL vs EKSTERNAL vs HISTORI vs LOGS -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden" x-data="{ activeTab: '{{ $pendingExternalCount > 0 ? 'external' : 'internal' }}' }">
        
        <!-- Tab Bar Header -->
        <div class="flex items-center gap-2 p-2.5 border-b border-slate-100 bg-slate-50/50 overflow-x-auto">
            <!-- Tab 1: Internal Courses -->
            <button @click="activeTab = 'internal'" 
                    :class="activeTab === 'internal' ? 'bg-white text-slate-900 shadow-xs border-slate-200' : 'text-slate-600 hover:text-slate-900 border-transparent hover:bg-white/60'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition border shrink-0">
                <span>📚</span>
                <span>Pelatihan Internal LMS</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold"
                      :class="activeTab === 'internal' ? 'bg-brand-50 text-brand-700' : 'bg-slate-200/70 text-slate-600'">
                    {{ $enrollments->count() }}
                </span>
            </button>

            <!-- Tab 2: External Training & Review -->
            <button @click="activeTab = 'external'" 
                    :class="activeTab === 'external' ? 'bg-white text-slate-900 shadow-xs border-slate-200' : 'text-slate-600 hover:text-slate-900 border-transparent hover:bg-white/60'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition border shrink-0">
                <span>🏛️</span>
                <span>Pelatihan Eksternal (Klaim JP)</span>
                @if($pendingExternalCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white animate-pulse">
                        {{ $pendingExternalCount }} Perlu Review
                    </span>
                @else
                    <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold"
                          :class="activeTab === 'external' ? 'bg-teal-50 text-teal-700' : 'bg-slate-200/70 text-slate-600'">
                        {{ $externalTrainings->count() }}
                    </span>
                @endif
            </button>

            <!-- Tab 3: Unified History -->
            <button @click="activeTab = 'history'" 
                    :class="activeTab === 'history' ? 'bg-white text-slate-900 shadow-xs border-slate-200' : 'text-slate-600 hover:text-slate-900 border-transparent hover:bg-white/60'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition border shrink-0">
                <span>🗓️</span>
                <span>Histori Perjalanan Pelatihan</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold"
                      :class="activeTab === 'history' ? 'bg-slate-900 text-white' : 'bg-slate-200/70 text-slate-600'">
                    {{ $trainingHistory->count() }}
                </span>
            </button>

            <!-- Tab 4: Certificates -->
            <button @click="activeTab = 'certificates'" 
                    :class="activeTab === 'certificates' ? 'bg-white text-slate-900 shadow-xs border-slate-200' : 'text-slate-600 hover:text-slate-900 border-transparent hover:bg-white/60'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition border shrink-0">
                <span>🏅</span>
                <span>Sertifikat Resmi</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold"
                      :class="activeTab === 'certificates' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200/70 text-slate-600'">
                    {{ $certificates->count() }}
                </span>
            </button>

            <!-- Tab 5: Activity Logs (KAK) -->
            <button @click="activeTab = 'logs'" 
                    :class="activeTab === 'logs' ? 'bg-white text-slate-900 shadow-xs border-slate-200' : 'text-slate-600 hover:text-slate-900 border-transparent hover:bg-white/60'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition border shrink-0">
                <span>📜</span>
                <span>Log Aktivitas</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold"
                      :class="activeTab === 'logs' ? 'bg-purple-50 text-purple-700' : 'bg-slate-200/70 text-slate-600'">
                    {{ $userActivityLogs->count() }}
                </span>
            </button>
        </div>

        <!-- TAB 1 CONTENT: PELATIHAN INTERNAL LMS -->
        <div x-show="activeTab === 'internal'" class="p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span>📚</span>
                        <span>Kursus Pembelajaran Internal LMS ({{ $enrollments->count() }})</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar kursus resmi di platform LMS KAB Berau yang sedang atau telah diselesaikan user.</p>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/60 shrink-0 self-start sm:self-auto">
                    {{ $completedCount }} Selesai • {{ $inProgressCount }} Sedang Berjalan
                </span>
            </div>

            @if($enrollments->isEmpty())
                <div class="p-12 text-center text-xs text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    User ini belum mendaftar di kursus internal manapun.
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($enrollments as $enrollment)
                        <div class="p-5 rounded-2xl border border-slate-200/90 bg-white hover:border-brand-200 hover:shadow-xs transition space-y-4">
                            <!-- Top Section: Thumbnail + Roomy Title + Tags -->
                            <div class="flex items-start gap-4">
                                <!-- Crisp Thumbnail with Error Fallback -->
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200 relative shadow-2xs">
                                    @php
                                        $thumb = $enrollment->course->thumbnail;
                                        $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') || str_starts_with($thumb, '/') ? $thumb : asset('storage/' . $thumb)) : null;
                                    @endphp
                                    @if($thumbUrl)
                                        <img src="{{ $thumbUrl }}" class="w-full h-full object-cover" alt="{{ $enrollment->course->title }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="hidden w-full h-full bg-gradient-to-tr from-brand-600 to-teal-400 items-center justify-center text-white font-bold text-2xl">
                                            📖
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-gradient-to-tr from-brand-600 to-teal-400 flex items-center justify-center text-white font-bold text-2xl">
                                            📖
                                        </div>
                                    @endif
                                </div>

                                <!-- Wide, Spacious Title Area -->
                                <div class="flex-1 min-w-0 space-y-1.5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200/60">
                                            {{ $enrollment->course->category->name ?? 'Kompetensi' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            {{ $enrollment->course->jp ?? 0 }} JP
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                            Level: {{ $enrollment->course->level ?? 'Semua' }}
                                        </span>
                                    </div>

                                    <h4 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                        <a href="{{ route('courses.show', $enrollment->course->slug) }}" target="_blank" class="hover:text-brand-700 transition">
                                            {{ $enrollment->course->title }}
                                        </a>
                                    </h4>

                                    <p class="text-xs text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1 pt-0.5">
                                        <span>👨‍🏫 Instruktur: <strong class="text-slate-700 font-semibold">{{ $enrollment->course->instructor->name ?? 'Tim Fasilitator Berau' }}</strong></span>
                                        <span>⏱️ Estimasi: <strong class="text-slate-700 font-semibold">{{ $enrollment->course->duration_hours ?? 2 }} Jam</strong></span>
                                        <span>📚 {{ $enrollment->course->lessons->count() }} Modul & Materi</span>
                                        @if($enrollment->completed_at)
                                            <span class="text-emerald-600 font-semibold">✓ Lulus: {{ \Carbon\Carbon::parse($enrollment->completed_at)->format('d M Y') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Bottom Section: Proportional Progress Bar Underneath -->
                            <div class="bg-slate-50/90 p-3.5 rounded-xl border border-slate-200/70 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-700">Status Belajar:</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $enrollment->progress_percent == 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $enrollment->progress_percent == 100 ? '✓ Tuntas & Lulus' : '⏳ Sedang Berjalan' }}
                                        </span>
                                    </div>
                                    <span class="font-mono font-black text-slate-900 text-sm">
                                        {{ $enrollment->progress_percent }}% Selesai
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200/80 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 rounded-full transition-all duration-700 {{ $enrollment->progress_percent == 100 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : 'bg-gradient-to-r from-brand-600 to-teal-500' }}" 
                                         style="width: {{ $enrollment->progress_percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 2 CONTENT: PELATIHAN EKSTERNAL & REVIEW JP -->
        <div x-show="activeTab === 'external'" class="p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span>🏛️</span>
                        <span>Daftar Pelatihan Eksternal & Klaim JP ({{ $externalTrainings->count() }})</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Diklat, bimbingan teknis, workshop, atau seminar dari luar LMS yang diajukan user untuk dihitung ke target JP tahunan.</p>
                </div>
                <a href="{{ route('admin.external-trainings.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-brand-700 bg-brand-50 border border-brand-200 hover:bg-brand-100 transition shrink-0 self-start sm:self-auto">
                    <span>↗️ Buka Semua Antrean Verifikasi</span>
                </a>
            </div>

            @if($pendingExternalCount > 0)
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between gap-3 text-xs text-amber-800">
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚠️</span>
                        <span><strong>Perhatian Admin:</strong> Ada <strong>{{ $pendingExternalCount }} pengajuan pelatihan eksternal</strong> yang menunggu review dan persetujuan Anda di bawah ini.</span>
                    </div>
                </div>
            @endif

            @if($externalTrainings->isEmpty())
                <div class="p-12 text-center text-xs text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    User ini belum pernah mengajukan pelatihan eksternal.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($externalTrainings as $ext)
                        <div class="p-5 rounded-2xl border transition {{ $ext->status === 'pending' ? 'bg-amber-50/30 border-amber-200 ring-2 ring-amber-100' : 'bg-white border-slate-200/90' }} space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                            {{ $ext->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                            {{ $ext->status === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-300' : '' }}
                                            {{ $ext->status === 'rejected' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}">
                                            {{ $ext->status === 'approved' ? '✓ Disetujui' : ($ext->status === 'pending' ? '⏳ Menunggu Review' : '✕ Ditolak') }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-brand-50 text-brand-700 border border-brand-200/60">
                                            +{{ $ext->jp_count }} JP
                                        </span>
                                    </div>

                                    <h4 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                        {{ $ext->training_name }}
                                    </h4>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 pt-0.5">
                                        <span>🏛️ Lembaga Penyelenggara: <strong class="text-slate-700 font-semibold">{{ $ext->institution_name }}</strong></span>
                                        <span>🗓️ Tanggal: <strong class="text-slate-700 font-semibold">{{ \Carbon\Carbon::parse($ext->training_date)->format('d F Y') }}</strong></span>
                                        <span>📅 Diajukan pada: <strong class="font-mono text-slate-600">{{ $ext->created_at->format('d M Y, H:i') }} WIB</strong></span>
                                    </div>

                                    @if($ext->description)
                                        <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200/60 mt-1">
                                            <strong>Catatan Peserta:</strong> {{ $ext->description }}
                                        </p>
                                    @endif

                                    <!-- Bukti Dokumen Sertifikat -->
                                    <div class="flex items-center gap-2 pt-1">
                                        @if($ext->certificate_file)
                                            <a href="{{ route('external-trainings.file', [$ext->id, 'certificate']) }}" target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-brand-700 bg-white border border-brand-200 hover:bg-brand-50 transition shadow-2xs">
                                                <span>📄 Buka Bukti Sertifikat</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                        @if($ext->supporting_file)
                                            <a href="{{ route('external-trainings.file', [$ext->id, 'supporting']) }}" target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-2xs">
                                                <span>📎 Dokumen Pendukung</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                        @if(!$ext->certificate_file && !$ext->supporting_file)
                                            <span class="text-xs text-slate-400 italic">Tidak ada berkas sertifikat yang dilampirkan.</span>
                                        @endif
                                    </div>

                                    @if($ext->status === 'rejected' && $ext->rejection_reason)
                                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 mt-2">
                                            <strong>Alasan Penolakan:</strong> {{ $ext->rejection_reason }}
                                        </div>
                                    @endif

                                    @if($ext->reviewer)
                                        <p class="text-[11px] text-slate-400 pt-1">
                                            Diverifikasi oleh <strong class="text-slate-600">{{ $ext->reviewer->name }}</strong> pada {{ $ext->reviewed_at ? $ext->reviewed_at->format('d M Y H:i') : '-' }} WIB
                                        </p>
                                    @endif
                                </div>

                                <!-- Review Actions directly inside User Detail -->
                                <div class="shrink-0 pt-1">
                                    @if($ext->status === 'pending')
                                        <div class="flex flex-col gap-2 min-w-[140px]" x-data="{ showRejectForm: false }">
                                            <form action="{{ route('admin.external-trainings.approve', $ext) }}" method="POST" onsubmit="return confirm('Setujui pengajuan diklat eksternal ini dan tambahkan {{ $ext->jp_count }} JP ke user?')">
                                                @csrf
                                                <button type="submit" class="w-full px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm flex items-center justify-center gap-1.5">
                                                    <span>✓</span>
                                                    <span>Setujui ({{ $ext->jp_count }} JP)</span>
                                                </button>
                                            </form>

                                            <button @click="showRejectForm = !showRejectForm" type="button" 
                                                    class="w-full px-3.5 py-1.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                                <span>✕ Tolak Pengajuan</span>
                                            </button>

                                            <div x-show="showRejectForm" x-cloak class="mt-2 space-y-2 p-3 bg-white rounded-xl border border-rose-200 shadow-sm">
                                                <form action="{{ route('admin.external-trainings.reject', $ext) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-[11px] font-bold text-rose-800 mb-1">Alasan Penolakan:</label>
                                                    <textarea name="rejection_reason" rows="2" required placeholder="Tulis alasan penolakan..."
                                                              class="w-full px-2.5 py-1.5 text-xs border border-rose-200 rounded-lg focus:ring-2 focus:ring-rose-400 focus:outline-none resize-none"></textarea>
                                                    <button type="submit" class="w-full mt-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition">
                                                        Kirim Penolakan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600">
                                            Status: {{ ucfirst($ext->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 3 CONTENT: HISTORI TERPADU TIMELINE -->
        <div x-show="activeTab === 'history'" class="p-6 space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <span>🗓️</span>
                    <span>Histori Perjalanan Pelatihan & Akumulasi JP ({{ $trainingHistory->count() }})</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kronologi gabungan seluruh kursus internal platform dan pelatihan eksternal yang diakui.</p>
            </div>

            @if($trainingHistory->isEmpty())
                <div class="p-12 text-center text-xs text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    Belum ada riwayat pelatihan yang tercatat untuk user ini.
                </div>
            @else
                <div class="relative pl-6 border-l-2 border-slate-200 space-y-6 my-2">
                    @foreach($trainingHistory as $item)
                        <div class="relative group">
                            <!-- Bullet indicator -->
                            <div class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full border-2 border-white shadow-xs {{ $item['type'] === 'internal' ? 'bg-brand-600' : ($item['status'] === 'approved' ? 'bg-emerald-500' : ($item['status'] === 'pending' ? 'bg-amber-400' : 'bg-slate-300')) }}"></div>
                            
                            <div class="p-4 rounded-xl border border-slate-200/80 bg-white hover:border-brand-200 hover:shadow-2xs transition">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm">{{ $item['title'] }}</h4>
                                            <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase {{ $item['type'] === 'internal' ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                                                {{ $item['type'] === 'internal' ? 'LMS Internal' : 'Diklat Luar' }}
                                            </span>
                                            @if($item['type'] === 'external')
                                                <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase {{ $item['status'] === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($item['status'] === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500">
                                            Penyelenggara: <span class="font-semibold text-slate-700">{{ $item['institution'] }}</span> • 
                                            Tanggal: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-sm font-black text-brand-700 bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-200/60">+{{ $item['jp'] }} JP</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 4 CONTENT: SERTIFIKAT TERBIT -->
        <div x-show="activeTab === 'certificates'" class="p-6 space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <span>🏅</span>
                    <span>Sertifikat Resmi Terbit ({{ $certificates->count() }})</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar sertifikat kelulusan kompetensi resmi ber-QR Code yang berhasil diraih user.</p>
            </div>

            @if($certificates->isEmpty())
                <div class="p-12 text-center text-xs text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    User ini belum memiliki sertifikat resmi yang terbit.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($certificates as $cert)
                        <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-brand-200 hover:shadow-xs transition space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-mono text-slate-400">NO: {{ $cert->certificate_number }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Terverifikasi</span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-1">{{ $cert->course->title ?? 'Kursus Kompetensi' }}</h4>
                            <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                                <span>{{ $cert->issued_at ? \Carbon\Carbon::parse($cert->issued_at)->format('d M Y') : $cert->created_at->format('d M Y') }}</span>
                                <a href="{{ route('certificates.show', $cert->certificate_number) }}" target="_blank" 
                                   class="font-bold text-brand-700 hover:underline inline-flex items-center gap-1">
                                    <span>Lihat</span>
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 5 CONTENT: ACTIVITY LOGS (KAK PENCATATAN AKTIVITAS PENGGUNA) -->
        <div x-show="activeTab === 'logs'" class="p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span>📜</span>
                        <span>Rekam Jejak & Log Aktivitas Pengguna ({{ $userActivityLogs->count() }})</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan aktivitas terkini dari akun pengguna ini termasuk login, penyelesaian modul, dan perubahan profil.</p>
                </div>
                <a href="{{ route('admin.activity-logs.index', ['user_id' => $user->id]) }}" 
                   class="text-xs font-bold text-brand-600 hover:text-brand-800 transition flex items-center gap-1">
                    <span>Lihat Semua di Audit Trail</span>
                    <span>&rarr;</span>
                </a>
            </div>

            @if($userActivityLogs->isEmpty())
                <div class="p-12 text-center text-xs text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    Belum ada riwayat aktivitas tercatat untuk akun pengguna ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/60 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-2.5 px-3">Waktu</th>
                                <th class="py-2.5 px-3 text-center">Jenis Aksi</th>
                                <th class="py-2.5 px-3">Deskripsi Aktivitas</th>
                                <th class="py-2.5 px-3">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($userActivityLogs as $log)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-3 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        {!! $log->action_badge !!}
                                    </td>
                                    <td class="py-3 px-3 text-slate-700 font-medium">
                                        {{ $log->description }}
                                    </td>
                                    <td class="py-3 px-3 font-mono text-slate-400 text-[11px]">
                                        {{ $log->ip_address ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL: EDIT USER DETAILS & ROLE -->
    <div x-show="editModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="editModal = false" 
             class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Edit Profil & Peran Pengguna</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $user->name }} ({{ $user->email }})</p>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-5 space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $user->name }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Email Resmi <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ $user->email }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Peran Akses (Role) <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold focus:bg-white transition">
                            @foreach($rolesDefinition as $rKey => $rDef)
                                <option value="{{ $rKey }}" {{ ($user->role === $rKey || ($rKey === 'super_admin' && $user->role === 'admin') || ($rKey === 'asn_learner' && $user->role === 'student') || ($rKey === 'narasumber' && $user->role === 'instructor')) ? 'selected' : '' }}>
                                    {{ $rDef['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Status Akun</label>
                        <select name="is_active" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold focus:bg-white transition">
                            <option value="1" {{ $user->is_active ? 'selected' : '' }}>🟢 Aktif (Dapat Login)</option>
                            <option value="0" {{ !$user->is_active ? 'selected' : '' }}>🔴 Nonaktif (Login Diblokir)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor Induk Pegawai (NIP)</label>
                        <input type="text" name="nip" value="{{ $user->nip }}" placeholder="19870101..."
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">NIK (KTP)</label>
                        <input type="text" name="nik" value="{{ $user->nik }}" placeholder="3201..."
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Perangkat Daerah / OPD</label>
                        <input type="text" name="opd_name" value="{{ $user->opd_name }}" placeholder="Contoh: Dinas Kesehatan"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ $user->jabatan }}" placeholder="Contoh: Pranata Komputer Ahli Muda"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_golongan" value="{{ $user->pangkat_golongan }}" placeholder="Contoh: Penata / III-c"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" value="{{ $user->phone }}" placeholder="Contoh: 081234567890"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan Profil / Bio</label>
                    <textarea name="bio" rows="2" placeholder="Informasi tambahan peserta..."
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">{{ $user->bio }}</textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="editModal = false" type="button" class="px-3.5 py-1.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        Simpan Perubahan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: RESET PASSWORD (KAK RESET PASSWORD) -->
    <div x-show="resetPasswordModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="resetPasswordModal = false" 
             class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Reset Password Akun</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $user->name }} ({{ $user->email }})</p>
                </div>
                <button @click="resetPasswordModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="p-5 space-y-4 text-xs">
                @csrf

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                    <p class="font-bold">⚠️ Perhatian Pengelola:</p>
                    <p class="mt-0.5">Password baru akan segera aktif untuk akun ini. Jika dikosongkan, sistem akan meng-generate password acak 8 karakter.</p>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="new_password" minlength="6" placeholder="Kosongkan untuk generate acak"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="resetPasswordModal = false" type="button" class="px-3.5 py-1.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                        Reset Password Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
