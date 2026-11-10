@extends('layouts.admin')

@section('page_title', 'Pusat Laporan & Analytics')

@section('content')
<div class="space-y-8">
    
    <!-- Top Greeting & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pusat Laporan & Analytics</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    Standar Laporan Resmi
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Modul pelaporan terpusat Corporate University. Menyediakan rekapitulasi indikator utama pembelajaran dan Report Builder fleksibel.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.jp.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
                <span>⏱️ Rekapitulasi 20 JP</span>
            </a>

            <a href="{{ route('admin.reports.builder') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-600 transition shadow-sm">
                <span>🎨 Buka Report Builder</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>

    <!-- Quick Stats Metric Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Total ASN Pembelajar</p>
            <p class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($stats['total_asns']) }}</p>
            <span class="text-[10px] text-teal-600 font-bold">Pegawai ASN</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Kegiatan Diklat</p>
            <p class="text-2xl font-black text-brand-700 tracking-tight mt-1">{{ number_format($stats['total_activities']) }}</p>
            <span class="text-[10px] text-slate-400">Kursus & Webinar</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Log Presensi Digital</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tight mt-1">{{ number_format($stats['total_attendances']) }}</p>
            <span class="text-[10px] text-emerald-600 font-bold">Check-In Terdata</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">E-Sertifikat Terbit</p>
            <p class="text-2xl font-black text-indigo-600 tracking-tight mt-1">{{ number_format($stats['total_certs']) }}</p>
            <span class="text-[10px] text-indigo-500 font-bold">Tervalidasi Digital</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Diklat Eksternal</p>
            <p class="text-2xl font-black text-amber-600 tracking-tight mt-1">{{ number_format($stats['total_externals']) }}</p>
            <span class="text-[10px] text-amber-600 font-bold">Klaim JP Luar</span>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Unit OPD Aktif</p>
            <p class="text-2xl font-black text-rose-600 tracking-tight mt-1">{{ number_format($stats['total_opds']) }}</p>
            <span class="text-[10px] text-slate-400">Organisasi Perangkat</span>
        </div>
    </div>

    <!-- Feature Banner: Report Builder Sederhana (Highlight KAK) -->
    <div class="relative bg-gradient-to-r from-slate-900 via-brand-950 to-teal-950 text-white rounded-3xl p-6 sm:p-8 shadow-md border border-slate-800 overflow-hidden">
        <div class="relative z-10 max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md border border-white/20 text-teal-300">
                <span>⚡</span> Pembuat Laporan Kustom
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                Report Builder Kustom
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Admin BKPSDM dapat menentukan sendiri parameter kolom laporan yang diinginkan secara mandiri. Pilih entitas (Peserta, Kegiatan, Presensi, Sertifikat, IDP, atau OPD), centang kolom yang dibutuhkan, lalu langsung unduh format Excel atau cetak PDF resmi.
            </p>
            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.reports.builder') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-400 hover:bg-teal-300 text-slate-950 font-black text-xs transition shadow-sm">
                    <span>Mulai Bangun Laporan Baru &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Catalog of 10 Mandatory Reports -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 tracking-tight">Katalog Laporan Pembelajaran Terpadu</h3>
                <p class="text-xs text-slate-500">Pilih template laporan di bawah ini untuk mengunduh Excel secara instan atau sesuaikan kolomnya.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            
            <!-- 1. Laporan Kegiatan -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center text-lg">
                        📚
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">1. Laporan Kegiatan Pembelajaran</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Rekapitulasi seluruh kursus e-Learning, agenda webinar live, durasi, bobot JP, kuota, instruktur, dan jumlah pendaftar.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.builder', ['entity' => 'kegiatan']) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Kustomisasi &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.reports.export-excel', ['entity' => 'kegiatan']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.reports.print', ['entity' => 'kegiatan']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Laporan Peserta -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200 text-teal-700 flex items-center justify-center text-lg">
                        👥
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">2. Laporan Peserta (Siswa ASN)</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Data lengkap pegawai ASN terdaftar, NIP, OPD penempatan, jabatan, progres pelatihan, dan total perolehan jam pelajaran.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.builder', ['entity' => 'peserta']) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Kustomisasi &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.reports.export-excel', ['entity' => 'peserta']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.reports.print', ['entity' => 'peserta']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Laporan Kehadiran -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center text-lg">
                        📱
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">3. Laporan Kehadiran Digital</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Log absensi digital per sesi kegiatan (QR Code / Tombol), waktu check-in, status hadir/terlambat, serta durasi kehadiran.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.builder', ['entity' => 'kehadiran']) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Kustomisasi &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.reports.export-excel', ['entity' => 'kehadiran']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.reports.print', ['entity' => 'kehadiran']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Laporan Jam Pelajaran (JP) -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center text-lg">
                        ⏱️
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">4. Laporan Jam Pelajaran (JP) ASN</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Akumulasi perolehan Jam Pelajaran internal dan eksternal terhadap target kepatuhan 20 JP per tahun sesuai regulasi merit.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.jp.asn') }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Buka Modul JP &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.jp.export.asn') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.jp.print', ['type' => 'asn']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- 5. Laporan Sertifikat Digital -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center text-lg">
                        🏅
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">5. Laporan Sertifikat Digital</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Data seluruh sertifikat resmi yang diterbitkan otomatis, nomor seri sertifikat, penerima NIP, tanggal terbit, dan tautan QR.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.builder', ['entity' => 'sertifikat']) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Kustomisasi &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.reports.export-excel', ['entity' => 'sertifikat']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.reports.print', ['entity' => 'sertifikat']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- 6. Laporan Learning Record ASN -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 flex items-center justify-center text-lg">
                        🗂️
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">6. Learning Record ASN (Transkrip)</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Buku catatan portofolio pengembangan kompetensi terintegrasi per pegawai (Internal, Eksternal, Webinar, Skor evaluasi).
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.jp.asn') }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Pilih Pegawai &rarr;
                    </a>
                    <span class="text-[11px] font-mono text-purple-600 bg-purple-50 px-2 py-0.5 rounded font-bold">
                        A4 Transkrip
                    </span>
                </div>
            </div>

            <!-- 7. Laporan per OPD -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-center text-lg">
                        🏛️
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">7. Laporan Capaian per OPD</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Rekapitulasi kepatuhan 20 JP per Perangkat Daerah di Kabupaten Berau, persentase kepatuhan, dan jumlah ASN yang tuntas.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.jp.opd') }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Detail OPD &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.jp.export.opd') }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.jp.print', ['type' => 'opd']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- 8. Laporan per Periode -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-700 flex items-center justify-center text-lg">
                        🗓️
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">8. Laporan per Periode / Tahun</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Filter agregat capaian dan histori diklat berdasarkan rentang semester, tahun anggaran, atau tanggal kalender tertentu.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.builder', ['entity' => 'kegiatan']) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Filter Rentang &rarr;
                    </a>
                    <span class="text-[11px] font-mono text-cyan-600 bg-cyan-50 px-2 py-0.5 rounded font-bold">
                        Multi-Tahun
                    </span>
                </div>
            </div>

            <!-- 9. Laporan Pembelajaran Eksternal -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-orange-700 flex items-center justify-center text-lg">
                        🌐
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">9. Laporan Diklat Eksternal</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Data seluruh pelatihan luar daerah yang diajukan ASN, penyelenggara, jumlah JP yang diakui, dan riwayat verifikasi BKPSDM.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.builder', ['entity' => 'eksternal']) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Kustomisasi &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.reports.export-excel', ['entity' => 'eksternal']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.reports.print', ['entity' => 'eksternal']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- 10. Laporan IDP -->
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-300 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 border border-pink-200 text-pink-700 flex items-center justify-center text-lg">
                        🧭
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">10. Laporan Individual Development Plan</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Rencana pengembangan tahunan (Tahun 1-5) per pegawai ASN, judul materi kompetensi, batch penetapan, dan progres pemenuhannya.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.builder', ['entity' => 'idp']) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Kustomisasi &rarr;
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.reports.export-excel', ['entity' => 'idp']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            Excel
                        </a>
                        <a href="{{ route('admin.reports.print', ['entity' => 'idp']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                            PDF
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
