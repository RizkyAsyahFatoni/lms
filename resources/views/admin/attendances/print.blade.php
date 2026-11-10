<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Resmi - {{ $session->title }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; line-height: 1.3; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .header { border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 14pt; }
        .header h3 { margin: 2px 0; font-size: 12pt; }
        .header p { margin: 0; font-size: 9pt; font-style: italic; }
        table.meta { width: 100%; margin-bottom: 15px; font-size: 10pt; }
        table.meta td { padding: 2px 4px; vertical-align: top; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9.5pt; }
        table.data th, table.data td { border: 1px solid #000; padding: 6px 8px; }
        table.data th { background-color: #f2f2f2; text-align: center; }
        .signature-section { width: 100%; margin-top: 30px; font-size: 10.5pt; }
        .signature-box { float: right; width: 250px; text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #f1f5f9; padding: 10px; margin-bottom: 15px; border-radius: 8px; text-align: right;">
        <button onclick="window.print();" style="padding: 8px 16px; font-weight: bold; background: #0f172a; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
        <button onclick="window.close();" style="padding: 8px 16px; margin-left: 5px; border: 1px solid #ccc; background: #fff; border-radius: 6px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <!-- Kop Dokumen Resmi -->
    <div class="header text-center">
        <h2>PEMERINTAH DAERAH PROVINSI / KABUPATEN / KOTA</h2>
        <h3>BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</h3>
        <p>Pusat Pengembangan Kompetensi Digital Terpadu Aparatur Sipil Negara (LMS Governance)</p>
    </div>

    <div class="text-center" style="margin-bottom: 15px;">
        <h3 class="uppercase" style="margin: 0; text-decoration: underline;">DAFTAR HADIR PESERTA (PRESENSI DIGITAL)</h3>
        <p style="margin: 2px 0 0 0; font-size: 9.5pt; font-family: monospace;">KODE SESI: {{ $session->session_code }}</p>
    </div>

    <!-- Metadata Kegiatan -->
    <table class="meta">
        <tr>
            <td style="width: 18%;" class="font-bold">Nama Kegiatan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 80%;" class="font-bold">{{ $session->title }}</td>
        </tr>
        <tr>
            <td class="font-bold">Hari, Tanggal</td>
            <td>:</td>
            <td>{{ $session->date ? $session->date->translatedFormat('l, d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="font-bold">Waktu Kegiatan</td>
            <td>:</td>
            <td>{{ $session->time_start ?: '08:00' }} - {{ $session->time_end ?: '17:00' }} WIB</td>
        </tr>
        <tr>
            <td class="font-bold">Durasi Minimal</td>
            <td>:</td>
            <td>{{ $session->min_duration_minutes }} Menit</td>
        </tr>
    </table>

    <!-- Tabel Daftar Hadir -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Peserta</th>
                <th style="width: 18%;">NIP</th>
                <th style="width: 22%;">Perangkat Daerah / OPD</th>
                <th style="width: 10%;">Jam Hadir</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Paraf / Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $idx => $att)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $att->participant_name }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ $att->participant_nip ?: '-' }}</td>
                    <td>{{ $att->participant_opd ?: '-' }}</td>
                    <td class="text-center">{{ $att->check_in_at ? $att->check_in_at->format('H:i:s') : $att->time }}</td>
                    <td class="text-center">{{ $att->status_label }}</td>
                    <td class="text-center" style="font-size: 7.5pt; color: #047857; font-weight: bold;">
                        ✓ DIGITAL<br><span style="font-size: 6pt; color: #666;">({{ strtoupper($att->method) }})</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; font-style: italic;">
                        Belum ada data kehadiran peserta tercatat pada sesi ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pengesahan Panitia / Penanggung Jawab -->
    <div class="signature-section">
        <div class="signature-box">
            <p style="margin: 0;">{{ now()->translatedFormat('d F Y') }}</p>
            <p style="margin: 2px 0 60px 0;">Mengetahui,<br>Penanggung Jawab Kegiatan</p>
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                {{ auth()->user()->name ?? 'Administrator BKPSDM' }}
            </p>
            <p style="margin: 0; font-size: 9.5pt;">NIP. {{ auth()->user()->nip ?? '198507152010011008' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
