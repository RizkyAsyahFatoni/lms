# Arsitektur Sistem Pengaturan SMTP & Template Email Notifikasi
# Path: knowledge/EMAIL_NOTIFICATION_SYSTEM.md

Sistem notifikasi resmi LMS Kabupaten Berau dirancang sesuai Kerangka Acuan Kerja (KAK) SPBE untuk menyediakan notifikasi multi-kanal (Web Inbox & Email SMTP).

---

## 1. Konfigurasi SMTP Dinamis (Tanpa Ubah .env Server)
Sistem memiliki antarmuka administrasi di `/admin/mail-settings` yang memungkinkan Super Admin mengatur parameter SMTP secara dinamis:
- **Host:** misal `smtp.gmail.com` atau SMTP server lokal Pemkab Berau.
- **Port:** 465 (SSL) atau 587 (TLS).
- **Username & Password:** Terenkripsi aman di basis data.
- **Enkripsi:** `tls` / `ssl`.
- **Pengirim:** Nama instansi (misal: "BKPSDM Kab. Berau - LMS SPBE") dan alamat email pengirim resmi.
- **Diagnostic Tool (Uji Kirim Email):** Fitur pengujian langsung ke alamat email tujuan dengan log status koneksi SMTP real-time.

---

## 2. 7 Skenario Notifikasi Wajib KAK & Variabel Dinamis

| Kode Skenario | Judul Notifikasi | Trigger / Pemicu | Variabel Placeholder Tersedia |
|---|---|---|---|
| `registration_success` | Pendaftaran Berhasil | Peserta mendaftar diklat/kursus baru | `{nama_peserta}`, `{nama_kegiatan}`, `{nomor_tiket}`, `{tanggal_daftar}`, `{link_kegiatan}` |
| `registration_approved`| Pendaftaran Disetujui | Admin OPD / BKPSDM menyetujui kepesertaan | `{nama_peserta}`, `{nama_kegiatan}`, `{nomor_tiket}`, `{link_kegiatan}`, `{catatan_admin}` |
| `activity_schedule`   | Jadwal Kegiatan      | Jadwal diklat diterbitkan / diperbarui | `{nama_peserta}`, `{nama_kegiatan}`, `{tanggal_mulai}`, `{tanggal_selesai}`, `{lokasi_kegiatan}`, `{link_kegiatan}` |
| `webinar_reminder`    | Pengingat Webinar    | H-1 atau 1 jam sebelum sesi webinar dimulai | `{nama_peserta}`, `{judul_webinar}`, `{narasumber}`, `{jadwal_mulai}`, `{link_webinar}`, `{password_room}` |
| `evaluation_reminder` | Pengingat Evaluasi   | Pasca materi selesai, peserta diingatkan isi kuis/evaluasi | `{nama_peserta}`, `{nama_kegiatan}`, `{batas_waktu_evaluasi}`, `{link_evaluasi}` |
| `certificate_ready`   | Sertifikat Tersedia  | Peserta dinyatakan lulus dan sertifikat terbit | `{nama_peserta}`, `{nama_kegiatan}`, `{nomor_sertifikat}`, `{total_jp}`, `{link_unduh_sertifikat}` |
| `opd_approval`        | Persetujuan Kegiatan OPD | Usulan program diklat dari OPD disetujui BKPSDM | `{nama_opd}`, `{nama_kegiatan}`, `{anggaran_disetujui}`, `{status_persetujuan}`, `{link_detail}` |

---

## 3. Komponen Teknis
1. **Controller:** `App\Http\Controllers\Admin\MailSettingController`
   - `index()`: Tampilan tab pengaturan SMTP dan list template.
   - `updateSmtp()`: Validasi dan penyimpanan konfigurasi koneksi.
   - `sendTestEmail()`: Mengkonfigurasi `Config::set('mail.mailers.smtp', ...)` secara runtime dan mengirim email percobaan.
   - `updateTemplate()`: Menyimpan kustomisasi subjek dan isi template pesan.
   - `resetTemplate()`: Mengembalikan template ke draf resmi KAK default.
2. **Mail Class:** `App\Mail\DynamicNotificationMail`
   - Mengambil template dari database, mengganti placeholder tag menggunakan regex, dan merender HTML responsive dengan styling Kinlo Warm Glassmorphism.
3. **Dispatcher Service:** `App\Services\NotificationService`
   - Metode statis seperti `NotificationService::sendRegistrationSuccess($user, $course)` yang dapat dipanggil dari controller manapun.

