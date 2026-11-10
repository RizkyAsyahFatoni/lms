<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Digital QR Code - {{ $session->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .qr-glow {
            box-shadow: 0 0 50px rgba(20, 184, 166, 0.25);
        }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col justify-between p-6 sm:p-10 select-none overflow-x-hidden"
      x-data="{
          currentTime: '',
          currentDate: '',
          updateClock() {
              const now = new Date();
              this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
              this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
          }
      }"
      x-init="updateClock(); setInterval(() => updateClock(), 1000);">

    <!-- Top Bar -->
    <header class="flex items-center justify-between pb-6 border-b border-slate-850 border-slate-800/80">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg">
                QR
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        PRESENSI DIGITAL RESMI
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-800 text-slate-300">
                        SESI: {{ $session->session_code }}
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-black text-white tracking-tight mt-1 line-clamp-1">
                    {{ $session->title }}
                </h1>
            </div>
        </div>

        <!-- Live Clock & Attendees Count -->
        <div class="flex items-center gap-6 text-right">
            <div class="hidden sm:block">
                <div class="text-xl sm:text-2xl font-black font-mono text-teal-400" x-text="currentTime"></div>
                <div class="text-xs text-slate-400 font-medium" x-text="currentDate"></div>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl px-5 py-2.5 shadow-sm text-center">
                <div class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono">{{ $attendedCount }}</div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Peserta Hadir</div>
            </div>
        </div>
    </header>

    <!-- Main Presentation Area: QR Code + Steps -->
    <main class="my-auto py-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center max-w-7xl mx-auto w-full">
        
        <!-- Left: Huge QR Code Box with Scanner Animation -->
        <div class="lg:col-span-6 flex flex-col items-center justify-center text-center space-y-5">
            <div class="relative p-6 bg-white rounded-3xl shadow-2xl qr-glow border-4 border-teal-500/40">
                <!-- QR Code Image -->
                <img src="{{ $session->qr_image_url }}" 
                     alt="QR Code Presensi" 
                     class="w-64 h-64 sm:w-80 sm:h-80 object-contain rounded-2xl">

                <!-- Center Badge -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-14 h-14 rounded-2xl bg-slate-950 text-teal-400 border-2 border-white shadow-xl flex items-center justify-center text-2xl font-black">
                        🏛️
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-slate-300">
                    Arahkan kamera smartphone Anda ke QR Code di atas
                </p>
                <p class="text-[11px] font-mono text-slate-500">
                    URL Presensi: <span class="text-teal-400">{{ $session->qr_scan_url }}</span>
                </p>
            </div>
        </div>

        <!-- Right: Instructions & Real-time Live Log -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- Step-by-Step Instructions -->
            <div class="bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-800 p-6 space-y-4">
                <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center gap-2">
                    <span>📱</span>
                    <span>Petunjuk Presensi Peserta</span>
                </h3>
                
                <div class="space-y-3 text-xs text-slate-300">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-teal-500/20 text-teal-300 font-bold flex items-center justify-center shrink-0 border border-teal-500/30">1</div>
                        <div>
                            <p class="font-bold text-white">Pindai QR Code</p>
                            <p class="text-slate-400 mt-0.5">Buka kamera ponsel pintar atau aplikasi QR Scanner bawaan Anda.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-teal-500/20 text-teal-300 font-bold flex items-center justify-center shrink-0 border border-teal-500/30">2</div>
                        <div>
                            <p class="font-bold text-white">Buka Tautan & Login Akun ASN</p>
                            <p class="text-slate-400 mt-0.5">Pastikan Anda telah masuk (login) ke sistem LMS dengan NIP/Email terdaftar.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-teal-500/20 text-teal-300 font-bold flex items-center justify-center shrink-0 border border-teal-500/30">3</div>
                        <div>
                            <p class="font-bold text-white">Konfirmasi Kehadiran</p>
                            <p class="text-slate-400 mt-0.5">Sistem otomatis mencatat nama, NIP, OPD, tanggal, jam hadir, dan status ketepatan waktu.</p>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-teal-950/40 border border-teal-800/50 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Toleransi Keterlambatan:</span>
                        <span class="font-bold text-white">{{ $session->tolerance_late_minutes }} Menit dari Jam Mulai</span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Durasi Minimal:</span>
                        <span class="font-bold text-teal-300">{{ $session->min_duration_minutes }} Menit</span>
                    </div>
                </div>
            </div>

            <!-- Recent Check-ins Widget -->
            <div class="bg-slate-900/60 backdrop-blur-md rounded-3xl border border-slate-800 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Peserta Baru Saja Check-in</span>
                    </h4>
                    <span class="text-[10px] text-slate-500 font-mono">Real-time update</span>
                </div>

                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    @forelse($attendances->take(5) as $att)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-800/60 border border-slate-750 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 font-bold flex items-center justify-center text-[11px] shrink-0">
                                    {{ substr($att->participant_name, 0, 2) }}
                                </div>
                                <div class="truncate">
                                    <p class="font-bold text-white truncate">{{ $att->participant_name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $att->participant_opd ?: ($att->participant_nip ? 'NIP. ' . $att->participant_nip : '-') }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold {{ $att->status === 'present' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                    {{ $att->check_in_at ? $att->check_in_at->format('H:i') : '' }} WIB
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-xs text-slate-500 py-4">Belum ada peserta yang memindai QR Code.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

    <!-- Footer Controls -->
    <footer class="pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
        <div class="flex items-center gap-3">
            <span>Platform Presensi Digital Terpadu LMS Pemerintah</span>
            <span>&bull;</span>
            <span class="text-slate-400">Tekan <kbd class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px]">F11</kbd> untuk Mode Layar Penuh</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.location.reload();" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 transition text-[11px] font-bold">
                🔄 Refresh Tampilan
            </button>
            <a href="{{ route('admin.attendances.sessions') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white transition text-[11px] font-bold">
                ✕ Tutup Proyektor
            </a>
        </div>
    </footer>

</body>
</html>
