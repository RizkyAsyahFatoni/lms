---
name: lms-domain-expert
description: Domain expert for the Berau Government Learning Management System (LMS KAB Berau), SPBE standards, ASN competency, SIMPEG synchronization, and training workflows.
---

# LMS Domain Expert Skill

Gunakan skill ini ketika mengerjakan fitur backend, logika bisnis, integrasi API, atau workflow pelatihan pada project LMS Kabupaten Berau.

## Domain Core Knowledge & Workflow

1. **Aktor & 6 Hak Akses (Multi-Role):**
   - `super_admin`: Administrator BKPSDM Kabupaten Berau (akses penuh kelola master data, audit log, pengaturan SMTP/email, sinkronisasi SIMPEG).
   - `admin_opd`: Verifikator & perwakilan perangkat daerah (usulan diklat OPD, monitoring realisasi jam pelajaran pegawai OPD).
   - `asn_learner`: Pegawai ASN pembelajar (katalog kursus, pendaftaran, webinar, pre/post-test, unduh sertifikat, riwayat JP).
   - `narasumber`: Pemateri/instruktur (kelola materi diklat, evaluasi tugas peserta, validasi kehadiran webinar).
   - `pimpinan`: Kepala Dinas/Bupati (monitoring eksekutif, rekapitulasi IP ASN, laporan serapan anggaran kompetensi).
   - `reviewer`: Tim penilai kompetensi dan asesmen ASN.

2. **Perhitungan Jam Pelajaran (JP):**
   - Minimal pengembangan kompetensi ASN: 20 Jam Pelajaran (JP) per tahun sesuai UU ASN.
   - 1 JP setara dengan 45 menit pembelajaran terstruktur.
   - Sertifikat otomatis diterbitkan dengan nomor seri unik jika peserta mencapai persentase kelulusan minimal (80%) dan post-test memenuhi standar.

3. **Sinkronisasi SIMPEG API & Keamanan Data:**
   - Kolom sensitif ASN (NIK, nomor telepon, alamat) dienkripsi menggunakan AES-256-CBC.
   - Pencarian cepat NIK/NIP terenkripsi menggunakan Blind Index (HMAC-SHA256) dengan salt khusus di file `.env` (`ASN_ENCRYPTION_SALT`).
   - Sinkronisasi API SIMPEG menggunakan token Bearer terenkripsi dengan fallback data staging/mock jika koneksi offline.

4. **Sistem Notifikasi Resmi (7 Skenario KAK):**
   - 1. Pendaftaran Berhasil
   - 2. Pendaftaran Disetujui
   - 3. Jadwal Kegiatan
   - 4. Pengingat Webinar
   - 5. Pengingat Evaluasi
   - 6. Sertifikat Tersedia
   - 7. Persetujuan Kegiatan OPD
   - Engine notifikasi mendukung template email dinamis ber-tag `{nama_peserta}`, `{nama_kegiatan}`, `{link_kegiatan}`, `{nomor_tiket}`, dll, yang tersimpan di tabel `email_templates`.

