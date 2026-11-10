<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transkrip Learning Record ASN - {{ $targetUser->name }} (NIP {{ $targetUser->nip }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff;
                color: #000000;
                font-size: 10pt;
                padding: 0;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    {{-- ── FLOATING TOOLBAR (NO PRINT) ─────────────────────────── --}}
    <div class="no-print max-w-4xl mx-auto mb-6 bg-slate-900 text-white rounded-2xl p-4 shadow-xl flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🎓</span>
            <div>
                <h4 class="font-bold text-sm">Transkrip Resmi Learning Record ASN</h4>
                <p class="text-xs text-slate-300">Format standar portofolio kompetensi tahunan ASN (A4 Portrait).</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-teal-400 hover:bg-teal-300 text-slate-950 font-black text-xs transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Transkrip PDF</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition">
                Tutup
            </button>
        </div>
    </div>

    {{-- ── A4 PORTRAIT SHEET ─────────────────────────────────────── --}}
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200/80 shadow-sm print:border-none print:shadow-none print:p-0">
        
        <!-- Kop Surat Resmi -->
        <div class="border-b-4 border-double border-slate-900 pb-4 mb-6 flex items-center gap-5">
            <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                @if($appSetting && $appSetting->logo_path)
                    <img src="{{ asset('storage/' . $appSetting->logo_path) }}" class="h-20 w-auto object-contain" alt="Logo Berau">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white font-black text-2xl flex items-center justify-center">
                        🏛️
                    </div>
                @endif
            </div>

            <div class="flex-1 text-center">
                <h3 class="text-sm font-extrabold uppercase tracking-widest text-slate-800 leading-tight">
                    Pemerintah Kabupaten Berau
                </h3>
                <h1 class="text-lg font-black uppercase tracking-wider text-slate-900 leading-tight">
                    Badan Kepegawaian, Pendidikan dan Pelatihan
                </h1>
                <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">
                    Jl. Murjani II No. 1, Tanjung Redeb, Kabupaten Berau, Kalimantan Timur 77312
                </p>
                <p class="text-[10px] text-slate-500 font-mono">
                    LMS Berau Corporate University: lms.beraukab.go.id
                </p>
            </div>

            <div class="w-20 text-right font-mono text-[9px] text-slate-400">
                <span>PORTFOLIO ASN</span>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="text-center mb-6">
            <h2 class="text-base font-black uppercase tracking-tight text-slate-900">
                BUKU CATATAN PEMBELAJARAN (LEARNING RECORD) ASN
            </h2>
            <p class="text-xs text-slate-600 mt-0.5 font-medium">
                Tahun Anggaran {{ $currentYear }}
            </p>
            <p class="text-[10px] font-mono text-slate-400 mt-1">
                Nomor Registrasi Portofolio: BCP-{{ date('Y') }}-{{ $targetUser->id }}-{{ strtoupper(substr(md5($targetUser->nip . $currentYear), 0, 6)) }}
            </p>
        </div>

        <!-- Biodata Pegawai ASN -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-xs">
            <table class="w-full">
                <tr class="leading-relaxed">
                    <td class="font-bold text-slate-600 w-36">Nama Lengkap</td>
                    <td class="w-4">:</td>
                    <td class="font-extrabold text-slate-900">{{ $targetUser->name }}</td>
                    <td class="font-bold text-slate-600 w-36">Pangkat / Golongan</td>
                    <td class="w-4">:</td>
                    <td class="text-slate-800 font-semibold">{{ $targetUser->golongan ?: 'Penata Muda (III/a)' }}</td>
                </tr>
                <tr class="leading-relaxed">
                    <td class="font-bold text-slate-600">NIP Pegawai</td>
                    <td>:</td>
                    <td class="font-mono font-bold text-slate-900">{{ $targetUser->nip ?: '-' }}</td>
                    <td class="font-bold text-slate-600">Jabatan</td>
                    <td>:</td>
                    <td class="text-slate-800 font-semibold">{{ $targetUser->jabatan ?: 'Fungsional Umum' }}</td>
                </tr>
                <tr class="leading-relaxed">
                    <td class="font-bold text-slate-600">Perangkat Daerah</td>
                    <td>:</td>
                    <td colspan="4" class="font-bold text-slate-900">{{ $targetUser->opd_name ?: 'Pemerintah Kabupaten Berau' }}</td>
                </tr>
            </table>
        </div>

        <!-- Akumulasi Jam Pelajaran & Status 20 JP -->
        <div class="grid grid-cols-4 gap-3 mb-6 text-center text-xs">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                <p class="text-[10px] text-slate-500 font-medium">Target Wajib Tahunan</p>
                <p class="text-lg font-black text-slate-900 mt-0.5">20 JP</p>
            </div>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                <p class="text-[10px] text-slate-500 font-medium">Capaian Internal (LMS)</p>
                <p class="text-lg font-black text-brand-700 mt-0.5">{{ $jpStats['internal_jp'] ?? 0 }} JP</p>
            </div>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                <p class="text-[10px] text-slate-500 font-medium">Capaian Diklat Luar</p>
                <p class="text-lg font-black text-amber-600 mt-0.5">{{ $jpStats['external_jp'] ?? 0 }} JP</p>
            </div>
            <div class="p-3 {{ ($jpStats['total_jp'] ?? 0) >= 20 ? 'bg-emerald-50 border-emerald-300' : 'bg-slate-50 border-slate-200' }} border rounded-xl">
                <p class="text-[10px] {{ ($jpStats['total_jp'] ?? 0) >= 20 ? 'text-emerald-700 font-bold' : 'text-slate-500' }}">Total Akumulasi</p>
                <p class="text-lg font-black {{ ($jpStats['total_jp'] ?? 0) >= 20 ? 'text-emerald-800' : 'text-slate-900' }} mt-0.5">
                    {{ $jpStats['total_jp'] ?? 0 }} JP
                </p>
            </div>
        </div>

        <!-- Tabel 1: Kursus Internal & Pelatihan LMS -->
        <div class="mb-6">
            <h3 class="text-xs font-black uppercase text-slate-800 mb-2">
                I. Program Pembelajaran Internal (e-Learning & Webinar)
            </h3>
            <table class="w-full text-left text-xs border border-slate-300 border-collapse">
                <thead class="bg-slate-100 text-[10px] uppercase font-bold text-slate-700">
                    <tr>
                        <th class="p-2 border border-slate-300 text-center w-8">No</th>
                        <th class="p-2 border border-slate-300">Nama Program Pelatihan</th>
                        <th class="p-2 border border-slate-300">Kategori</th>
                        <th class="p-2 border border-slate-300 text-center w-16">Bobot JP</th>
                        <th class="p-2 border border-slate-300">No. Sertifikat</th>
                        <th class="p-2 border border-slate-300 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="text-[11px]">
                    @forelse($internalEnrollments as $idx => $enr)
                        @php
                            $cert = $certificates[$enr->course_id] ?? null;
                        @endphp
                        <tr>
                            <td class="p-2 border border-slate-300 text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="p-2 border border-slate-300 font-bold text-slate-800">{{ $enr->course->title }}</td>
                            <td class="p-2 border border-slate-300 text-slate-600">{{ $enr->course->category?->name ?: 'Umum' }}</td>
                            <td class="p-2 border border-slate-300 text-center font-mono font-bold">{{ $enr->course->jp ?? 2 }} JP</td>
                            <td class="p-2 border border-slate-300 font-mono text-[10px]">{{ $cert ? $cert->certificate_number : 'Tuntas (100%)' }}</td>
                            <td class="p-2 border border-slate-300 text-center font-bold text-emerald-700">LULUS</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-3 text-center text-slate-400 italic">Belum ada riwayat kursus internal tahun ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tabel 2: Pelatihan Eksternal Terverifikasi -->
        <div class="mb-8">
            <h3 class="text-xs font-black uppercase text-slate-800 mb-2">
                II. Pembelajaran Eksternal Terverifikasi (Diklat Luar / Mandiri)
            </h3>
            <table class="w-full text-left text-xs border border-slate-300 border-collapse">
                <thead class="bg-slate-100 text-[10px] uppercase font-bold text-slate-700">
                    <tr>
                        <th class="p-2 border border-slate-300 text-center w-8">No</th>
                        <th class="p-2 border border-slate-300">Nama Pelatihan Eksternal</th>
                        <th class="p-2 border border-slate-300">Lembaga Penyelenggara</th>
                        <th class="p-2 border border-slate-300 text-center w-24">Tanggal</th>
                        <th class="p-2 border border-slate-300 text-center w-16">JP</th>
                        <th class="p-2 border border-slate-300 text-center w-24">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="text-[11px]">
                    @forelse($externalTrainings as $idx => $ext)
                        <tr>
                            <td class="p-2 border border-slate-300 text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="p-2 border border-slate-300 font-bold text-slate-800">{{ $ext->training_name }}</td>
                            <td class="p-2 border border-slate-300 text-slate-600">{{ $ext->organizer }}</td>
                            <td class="p-2 border border-slate-300 text-center font-mono">{{ $ext->training_date ? $ext->training_date->format('d/m/Y') : '-' }}</td>
                            <td class="p-2 border border-slate-300 text-center font-mono font-bold">{{ $ext->jp }} JP</td>
                            <td class="p-2 border border-slate-300 text-center font-bold text-emerald-700">DISETUJUI</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-3 text-center text-slate-400 italic">Tidak ada klaim pelatihan eksternal pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Lembar Pengesahan Pejabat -->
        <div class="flex items-end justify-between pt-4 border-t border-slate-300 text-xs">
            <div class="space-y-1 text-slate-500 text-[10px] font-mono">
                <p>Dokumen resmi ini diakui sebagai bukti pemenuhan Jam Pelajaran (JP) ASN.</p>
                <p>Terintegrasi dengan SIASN BKN & SIMPEG Kabupaten Berau.</p>
                <p>Verifikasi Digital: {{ strtoupper(substr(md5($targetUser->nip . $currentYear), 0, 16)) }}</p>
            </div>

            <div class="text-center w-64 space-y-1">
                <p class="text-xs text-slate-700">Tanjung Redeb, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="text-xs font-bold text-slate-900 leading-tight">Kepala BKPSDM Kabupaten Berau</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="text-[10px] font-mono text-slate-400 border border-dashed border-slate-300 px-3 py-1 rounded">
                        [Tanda Tangan Elektronik]
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-900 underline">DRA. SRI EKAWATI</p>
                <p class="text-[10px] font-mono text-slate-600">NIP. 19680512 199303 2 004</p>
            </div>
        </div>

    </div>

</body>
</html>
