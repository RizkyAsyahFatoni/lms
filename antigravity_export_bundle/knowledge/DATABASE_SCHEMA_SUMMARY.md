# Ringkasan Skema Database LMS Kabupaten Berau
# Path: knowledge/DATABASE_SCHEMA_SUMMARY.md

Sistem database LMS Kabupaten Berau mengelola siklus lengkap pengembangan kompetensi ASN dari registrasi, usulan OPD, penugasan materi, evaluasi kuis, hingga penerbitan sertifikat digital.

---

## 1. Entitas Pengguna & Hak Akses
- **`users`**: Tabel master akun pengguna (NIP, nama, email terenkripsi, peran/role, status aktif, OPD id, jabatan).
- **`roles` / `permissions`**: Manajemen RBAC (Role-Based Access Control) multi-peran (Super Admin, Admin OPD, ASN/Learner, Narasumber, Pimpinan, Reviewer).
- **`departments` (OPD)**: Master Organisasi Perangkat Daerah di lingkungan Pemerintah Kabupaten Berau (Dinas Pendidikan, Dinkes, Bappeda, BKPSDM, dll).
- **`asn_profiles`**: Profil detail kepegawaian ASN terenkripsi (pangkat, golongan ruang, eselon, jabatan fungsional/struktural).

---

## 2. Kurikulum & Pembelajaran
- **`categories`**: Kategori diklat/kompetensi (Manajerial, Sosio-Kultural, Teknis, Fungsional).
- **`courses`**: Master program pembelajaran (judul, deskripsi, silabus, bobot JP, kuota, tanggal mulai/selesai, status approval, tipe: mandiri/terjadwal).
- **`modules`**: Bab/bagian dari kursus.
- **`lessons`**: Unit pembelajaran terkecil (video streaming, dokumen PDF, teks, interaktif).
- **`quizzes`**: Kuis evaluasi (Pre-test, Kuis Modul, Post-test akhir).
- **`questions` / `answers`**: Butir soal pilihan ganda atau esai beserta kunci jawaban dan pembobotan nilai.

---

## 3. Pendaftaran & Progress Pembelajar
- **`enrollments`**: Status pendaftaran peserta diklat (`pending`, `approved`, `rejected`, `active`, `completed`).
- **`lesson_completions`**: Rekam jejak progres per unit materi yang diselesaikan learner.
- **`quiz_attempts`**: Riwayat pengerjaan kuis peserta, skor nilai, durasi, dan status kelulusan.
- **`certificates`**: Rekor sertifikat resmi yang diterbitkan (nomor registrasi unik, kode verifikasi QR, tanggal terbit, total JP yang diperoleh).

---

## 4. Webinar & Event Virtual
- **`webinars`**: Master agenda webinar/seminar daring (topik, narasumber, tanggal/jam, tautan Zoom/Meet, batas kuota peserta).
- **`webinar_registrations`**: Pendaftaran peserta webinar dan presensi kehadiran online.

---

## 5. Usulan OPD & Audit Trail
- **`opd_training_requests`**: Pengajuan diklat mandiri atau pelatihan khusus dari masing-masing OPD ke BKPSDM beserta justifikasi anggaran dan sasaran kompetensi.
- **`activity_logs`**: Catatan riwayat aksi sistem (siapa, melakukan apa, kapan, dari IP mana, user agent).

---

## 6. Pengaturan Sistem & Notifikasi
- **`mail_settings`**: Konfigurasi koneksi SMTP email server (`mail_host`, `mail_port`, `mail_username`, `mail_password` terenkripsi, `mail_encryption`, `from_address`, `from_name`).
- **`email_templates`**: Template balasan pesan dinamis untuk 7 skenario notifikasi resmi KAK dengan dukungan parser placeholder variabel.

