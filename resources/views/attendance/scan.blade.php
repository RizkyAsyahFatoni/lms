@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">

    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden">
        
        <!-- Header Visual Banner -->
        <div class="p-6 sm:p-8 bg-gradient-to-br from-slate-900 via-teal-950 to-emerald-900 text-white relative">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    PRESENSI DIGITAL (QR CODE)
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-white/10 text-white">
                    {{ $session->session_code }}
                </span>
            </div>
            
            <h1 class="text-xl sm:text-2xl font-black text-white leading-tight">
                {{ $session->title }}
            </h1>

            <p class="text-xs text-teal-200 mt-2 flex items-center gap-2">
                <span>📅 {{ $session->date ? $session->date->translatedFormat('l, d F Y') : '-' }}</span>
                <span>•</span>
                <span>⏰ {{ $session->time_start ?: '08:00' }} - {{ $session->time_end ?: '17:00' }} WIB</span>
            </p>
        </div>

        <div class="p-6 sm:p-8 space-y-6">

            <!-- Peserta Info Card -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-teal-400 text-white font-black text-lg flex items-center justify-center shrink-0 shadow-sm">
                    {{ substr($user->name, 0, 2) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Identitas Peserta ASN:</p>
                    <p class="font-extrabold text-slate-900 text-sm truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 font-mono">
                        {{ $user->nip ? 'NIP: ' . $user->nip : $user->email }}
                    </p>
                    @if($user->opd_name)
                        <p class="text-[11px] text-slate-600 mt-0.5 truncate">🏛️ {{ $user->opd_name }}</p>
                    @endif
                </div>
            </div>

            <!-- Kondisi: Sudah Presensi -->
            @if($existing)
                <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-500 text-white flex items-center justify-center text-2xl shadow-sm">
                        ✓
                    </div>
                    <div>
                        <h3 class="font-black text-emerald-950 text-base">Presensi Telah Tercatat!</h3>
                        <p class="text-xs text-emerald-800 mt-0.5">
                            Anda telah melakukan presensi pada <strong>{{ $existing->check_in_at->format('d F Y, H:i') }} WIB</strong>.
                        </p>
                        <div class="mt-2.5">
                            {!! $existing->status_badge !!}
                        </div>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('attendance.history') }}" 
                           class="inline-block px-4 py-2 rounded-xl text-xs font-bold text-emerald-900 bg-emerald-100 hover:bg-emerald-200 transition">
                            Lihat Riwayat Presensi Saya &rarr;
                        </a>
                    </div>
                </div>

            <!-- Kondisi: Sesi Ditutup -->
            @elseif(!$session->isOpen())
                <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-3">
                    <div class="text-3xl">🔒</div>
                    <div>
                        <h3 class="font-bold text-amber-950 text-sm">Presensi Digital Ditutup</h3>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Waktu presensi untuk kegiatan ini belum dibuka atau telah melewati batas akhir penutupan.
                        </p>
                    </div>
                </div>

            <!-- Kondisi: Siap Presensi -->
            @else
                <form action="{{ route('attendance.qr.submit', $session) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ request('token', $session->qr_secret_token) }}">

                    <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200 text-xs text-teal-900 space-y-1">
                        <div class="flex items-center justify-between font-bold">
                            <span>Status Ketepatan Waktu:</span>
                            @php
                                $statusPreview = $session->evaluateStatus(now());
                            @endphp
                            @if($statusPreview === 'present')
                                <span class="text-emerald-700">🟢 Tepat Waktu</span>
                            @else
                                <span class="text-amber-700">🟡 Terlambat (Batas toleransi: {{ $session->tolerance_late_minutes }} menit)</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-teal-700">Waktu presensi Anda akan otomatis terekam secara sah sesuai zona waktu server (WIB).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="notes" placeholder="Contoh: Hadir via Zoom / Hadir langsung di aula..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-2xl font-black text-sm text-white bg-slate-900 hover:bg-brand-700 transition shadow-lg flex items-center justify-center gap-2 group">
                        <span>📱</span>
                        <span>Konfirmasi & Simpan Presensi Kehadiran</span>
                        <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </button>
                </form>
            @endif

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-slate-600 transition">&larr; Beranda LMS</a>
                <span>Platform Presensi Digital Terpadu</span>
            </div>

        </div>

    </div>

</div>
@endsection
