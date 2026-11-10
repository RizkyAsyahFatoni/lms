# BUKU PANDUAN LENGKAP & DOKUMENTASI SISTEM
## Learning Management System Pemerintah Kabupaten Berau (LMS KAB Berau)
### Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM)

---

## 1. Pemasangan dari Arsip ZIP & Syarat PHP Terbaru (Mitigasi PHP CVE)

Aplikasi LMS KAB Berau didistribusikan dalam bentuk paket arsip ZIP utuh (*standalone distribution*) yang dapat langsung diekstrak ke server lokal maupun cloud.

### A. Kewajiban Penggunaan PHP 8.3 atau PHP 8.4 Terbaru
Sesuai arahan Badan Siber dan Sandi Negara (BSSN) dan standar kepatuhan CSIRT instansi pemerintah, sistem **wajib** dijalankan pada **PHP 8.3.8+ atau PHP 8.4 terbaru** guna memitigasi kerentanan keamanan kritis yang telah dipublikasikan secara global:
- **CVE-2024-4577 (CGI Argument Injection RCE):** Kerentanan berbahaya pada modul parser PHP di lingkungan Windows/Apache yang memungkinkan penyerang mengeksekusi kode perintah jarak jauh melalui query HTTP. Versi PHP 8.3/8.4 patch terbaru telah menutup celah ini secara permanen.
- **CVE-2024-2756 & CVE-2024-3094:** Potensi kebocoran alokasi buffer memori pada pemrosesan string multibyte dan manipulasi sanitasi paket.

### B. Tahapan Pemasangan dari Berkas ZIP
1. **Ekstraksi Berkas ZIP:**  
   Pindahkan file ZIP aplikasi ke direktori web server (misalnya: `C:\laragon\www\lms` atau Linux `/var/www/lms`) dan lakukan ekstraksi.
2. **Pengaturan Berkas Konfigurasi (`.env`):**  
   Salin berkas template `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Pastikan parameter database dan keamanan terkonfigurasi:
   ```dotenv
   APP_NAME="LMS KAB Berau"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://lms.beraukab.go.id

   DB_CONNECTION=sqlite
   ASN_ENCRYPTION_SALT=berau_bkpsdm_secure_salt_2026_xyz
   ```
3. **Inisialisasi Kunci & Enkripsi Data:**  
   Jalankan perintah berikut di terminal:
   ```bash
   php artisan key:generate
   php artisan storage:link
   php artisan asn:encrypt-nip-nik
   ```
4. **Optimalisasi Cache:**  
   ```bash
   php artisan optimize:clear
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```

---

## 2. Arsitektur Tumpukan Teknologi & Standar Rekayasa SPBE

Sesuai pedoman permanen proyek pada project_guidelines.md, aplikasi dibangun dengan standar rekayasa perangkat lunak kedinasan yang bersih, modular, dan teruji:

- **Backend:** Laravel 11.x (Clean MVC, Service-Oriented Architecture).
- **CSS Framework:** Tailwind CSS v3.4 menggunakan palet resmi Teal Berau (`#0f766e`, `#0d9488`, `#14b8a6`) dan font utama **Plus Jakarta Sans**.
- **JavaScript:** Alpine.js v3 untuk reaktivitas ringan tanpa overhead framework SPA.
- **Keamanan Kriptografi:** Enkripsi dua arah AES-256-CBC untuk NIP dan NIK, didukung blind index `nip_hash` (HMAC-SHA256) untuk pencarian cepat terenkripsi.
- **Web Application Firewall (WAF) & Brute-Force Shield:** Pembatasan maksimal 5x login gagal dengan penguncian sementara akun dan penambahan delay progresif.
- **Kualitas Kode:** Bebas komentar klise dekoratif, tidak ada file debug yang tertinggal, struktur database ternormalisasi, dan 100% lulus 49 kasus uji (*Unit & Feature Tests*).

---

## 3. Skema Basis Data 19 Entitas Relasional

Sistem menggunakan SQLite 3 dengan mode *Write-Ahead Logging* (WAL) atau MySQL 8.0+:

| Entitas Tabel | Kolom Utama | Fungsi Sistem & Hubungan Relasi |
| :--- | :--- | :--- |
| **`users`** | `id`, `name`, `email`, `nip` (AES-256), `nip_hash`, `role`, `opd_name`, `is_active` | Master data aparatur dengan hak akses RBAC multi-level dan blind index NIP. |
| **`categories`** | `id`, `name`, `slug`, `parent_id`, `color`, `order` | Taksonomi bidang kompetensi manajerial, teknis digital SPBE, dan SIPD. |
| **`courses`** | `id`, `category_id`, `instructor_id`, `title`, `duration_hours`, `cert_seal_text` | Master kurikulum pelatihan mandiri, bobot JP, dan konfigurasi sertifikat. |
| **`lessons`** | `id`, `course_id`, `title`, `type` (video/doc/article/quiz), `video_url`, `pdf_attachment` | Silabus bab materi: video, modul PDF interaktif, artikel, atau kuis evaluasi. |
| **`quizzes` & `quiz_questions`** | `passing_grade`, `time_limit_minutes`, `question_text`, `options`, `correct_option` | Engine evaluasi pre-test dan post-test dengan perhitungan passing grade otomatis. |
| **`certificates`** | `certificate_number`, `user_id`, `course_id`, `verification_hash`, `issued_at` | Rekam sertifikat kelulusan sah dengan hash unik SHA-256 dan kode QR publik. |
| **`webinars`** | `delivery_mode`, `event_date`, `time_start`, `location_type`, `webinar_url`, `quota`, `jp` | Manajemen agenda pelatihan tatap muka (Sasana Praja) dan lokakarya webinar Zoom. |
| **`attendance_sessions`** | `webinar_id`, `session_title`, `token`, `qr_code`, `is_active` | Pencatatan presensi peserta berbasis kode token dan pemindaian QR code dinamis. |
| **`popups` & `sliders`** | `title`, `badge_text`, `content`, `button_link`, `image_url`, `order`, `is_active` | Pengumuman modal dialog beranda dan banner hero slider untuk publikasi program prioritas. |

---

## 4. Panduan Lengkap Setiap Fitur (Fungsi, Step-by-Step, & Visual)

### Fitur 1: Manajemen Kursus & Pembuatan Materi Silabus
- **Fungsi & Manfaat:** Menyusun kurikulum pelatihan mandiri berbobot Jam Pelajaran (JP) sesuai SKJ ASN yang dapat diakses secara asinkronus oleh pegawai.
- **Langkah Penggunaan:**
  1. Buka menu **Manajemen Kursus &rarr; Tambah Kursus Baru**.
  2. Isi Judul, Kategori taksonomi, Bobot JP (misal: `4` JP), dan unggah thumbnail HD 1280x720 pixel.
  3. Pada tab **Silabus & Modul**, klik **Tambah Materi** untuk memasukkan materi video (YouTube/MP4), dokumen PDF interaktif, atau artikel teks panduan.
- **Tangkapan Layar:**  
  ![Manajemen Kursus](./screenshots/admin_courses.png)

---

### Fitur 2: Kuis Kelulusan & Bank Soal (Quizzes & Question Bank)
- **Fungsi & Manfaat:** Alat asesmen objektif ketuntasan kompetensi. Post-test memastikan aparatur benar-benar menyerap materi sebelum sertifikat kelulusan diterbitkan.
- **Langkah Penggunaan:**
  1. Pada silabus kursus, klik **Tambah Materi** bertipe **Kuis Evaluasi**.
  2. Tentukan **Passing Grade** (misal: 75%), timer durasi pengerjaan, dan batas pengulangan.
  3. Masukkan butir pertanyaan pilihan ganda, kunci jawaban, dan penjelasan pembahasan.
  4. Untuk impor massal dari file CSV, buka menu **Bank Soal** dan gunakan template yang disediakan.
- **Tangkapan Layar:**  
  ![Classroom Player & Kuis](./screenshots/learner_classroom.png)

---

### Fitur 3: Agenda Webinar & Presensi Kode QR Dinamis
- **Fungsi & Manfaat:** Memusatkan lokakarya daring Zoom dan pelatihan tatap muka di Sasana Praja/Gedung Diklat dengan presensi real-time anti-kecurangan.
- **Langkah Penggunaan:**
  1. Buka menu **Manajemen Agenda &rarr; Buat Agenda Baru**.
  2. Pilih mode: *Online* (masukkan link Zoom), *Tatap Muka* (isi gedung fisik), atau *Hybrid*.
  3. Saat sesi dimulai, klik **Buka Sesi Presensi** untuk menampilkan **QR Code Dinamis** dan **Token 6 Digit**.
  4. Peserta memindai QR code via ponsel untuk mencatatkan kehadiran secara instan.
- **Tangkapan Layar:**  
  ![Agenda Webinar & Pelatihan](./screenshots/webinars.png)

---

### Fitur 4: Pengaturan Modal Pop-Up Pengumuman Beranda
- **Fungsi & Manfaat:** Memastikan informasi mendesak (pendaftaran beasiswa, surat edaran diklat wajib) langsung terbaca saat aparatur membuka beranda sistem.
- **Langkah Penggunaan:**
  1. Buka menu **Manajemen Pop-Up** di panel Admin.
  2. Masukkan Judul, Teks Badge (misal: `PENGUMUMAN PENTING`), narasi pesan, dan tombol CTA.
  3. Aktifkan saklar **Status Tayang**. Sistem menerapkan mekanisme auto-dismiss pintar via `localStorage` agar pop-up tidak mengganggu navigasi setelah ditutup peserta.

---

### Fitur 5: Pengaturan Hero Slider Banner Beranda
- **Fungsi & Manfaat:** Spanduk visual bergulir di bagian atas beranda untuk menampilkan program-program unggulan ASN Berau Corporate University.
- **Langkah Penggunaan:**
  1. Buka menu **Manajemen Slider**.
  2. Unggah gambar banner HD (1920x800 pixel), masukkan judul, subtitle, tombol CTA, dan nomor urutan (*order*).
- **Tangkapan Layar:**  
  ![Beranda & Hero Slider](./screenshots/homepage.png)

---

### Fitur 6: Interactive Certificate Builder & Segel Digital
- **Fungsi & Manfaat:** Menerbitkan sertifikat kelulusan sah berstempel digital `LMS BERAU VALID` dan kode QR verifikasi instan secara otomatis.
- **Langkah Penggunaan:**
  1. Buka **Edit Kursus &rarr; Certificate Builder**.
  2. Isi Lembaga Penerbit (*BKPSDM Kabupaten Berau*), Penandatangan (*Dra. Hj. Sri Eka Yuliana, M.Si*), NIP resmi, dan teks segel.
  3. Gunakan fitur **Pratinjau Langsung** untuk meninjau tata letak sebelum sertifikat diterbitkan.

---

### Fitur 7: Dashboard Peserta ASN & Cetak Transkrip 20 JP
- **Fungsi & Manfaat:** Lembar rapor kompetensi digital bagi aparatur untuk memantau pemenuhan target 20 JP tahunan serta mencetak transkrip sah untuk syarat kenaikan pangkat.
- **Langkah Penggunaan:**
  1. Buka menu **Dashboard** akun peserta ASN.
  2. Tinjau progres pemenuhan 20 JP dan klik tombol **Cetak Transkrip JP**.
  3. Gunakan menu **Klaim Diklat Eksternal** untuk mengajukan pengakuan sertifikat pelatihan luar (LAN / Kementerian).
- **Tangkapan Layar:**  
  ![Dashboard Peserta ASN](./screenshots/learner_dashboard.png)

---

### Fitur 8: Dashboard Monitoring Admin OPD & Usulan Diklat
- **Fungsi & Manfaat:** Membantu kepala dinas dan kasubbag kepegawaian mengawal persentase kepatuhan 20 JP seluruh staf unit kerja dan mengajukan usulan diklat internal dinas.
- **Langkah Penggunaan:**
  1. Buka **Dashboard OPD** untuk memantau grafik persentase kepatuhan staf.
  2. Buka menu **Usulan Kegiatan** untuk mengajukan proposal bimtek dinas ke BKPSDM beserta TOR lampiran.
- **Tangkapan Layar:**  
  ![Dashboard Admin OPD](./screenshots/opd_dashboard.png)

---

### Fitur 9: Ruang Kerja Trainer / Narasumber (Workspace)
- **Fungsi & Manfaat:** Panel mandiri bagi instruktur pengampu untuk memantau kemajuan kelas dan memeriksa berkas penugasan studi kasus peserta.
- **Langkah Penggunaan:**
  1. Masuk ke **Trainer Workspace**, pilih kelas ampu.
  2. Pada tab **Penilaian Tugas**, periksa file karya tulis peserta, beri nilai angka (0 - 100), dan tuliskan catatan feedback.
- **Tangkapan Layar:**  
  ![Trainer Workspace](./screenshots/trainer_workspace.png)

---

### Fitur 10: Integrasi Single Sign-On (SSO) & REST API SIMPEG
- **Fungsi & Manfaat:** Gerbang interoperabilitas satu pintu (OAuth2/OIDC) dengan Keycloak Pemkab Berau / SIASN BKN dan penarikan data kepegawaian SIMPEG.
- **Spesifikasi Teknis:**
  - Authorized Callback URI: `https://lms.beraukab.go.id/auth/sso/callback`
  - Header API: `Authorization: Bearer berau_lms_live_xxxxxxxxxxxxxxxxxxxxxxxx`
  - Endpoint Utama:
    - `GET /api/v1/asn/participants` (Data seluruh pegawai & akumulasi JP)
    - `GET /api/v1/asn/participants/{nip}` (Data 1 pegawai via blind index `nip_hash`)
    - `GET /api/v1/asn/certificates/verify/{hash}` (Verifikasi publik keabsahan sertifikat)
    - `GET /api/v1/asn/stats` (Statistik makro kepatuhan daerah)
- **Tangkapan Layar:**  
  ![Integrasi SSO](./screenshots/admin_sso.png)

---

### Fitur 11: Pengaturan Identitas & Branding Lembaga
- **Fungsi & Manfaat:** Personalisasi nama aplikasi, tagline, logo resmi daerah, favicon, dan kontak dinas tanpa perlu koding ulang.
- **Langkah Penggunaan:**
  1. Buka menu **Pengaturan &rarr; Identitas & Logo LMS**.
  2. Unggah logo resmi Pemerintah Kabupaten Berau, isi email `bkpsdm@beraukab.go.id`, telepon dinas `(0554) 21064`, dan alamat kantor di Tanjung Redeb.
- **Tangkapan Layar:**  
  ![Pengaturan Branding](./screenshots/admin_branding.png)

### Panduan Spesifikasi & Rekomendasi Dimensi Unggah Gambar

Untuk menjaga kualitas visual portal tetap tajam (*retina-ready*), responsif pada berbagai layar perangkat (desktop, tablet, maupun smartphone), dan cepat diakses, berikut adalah panduan ukuran rekomendasi untuk seluruh fitur unggahan gambar pada sistem:

| No | Komponen / Fitur Unggahan | Rekomendasi Resolusi | Rasio Aspek | Format Ideal | Batas Ukuran File | Penempatan & Kegunaan |
| :---: | :--- | :---: | :---: | :---: | :---: | :--- |
| **1** | **Logo Utama Instansi** | **400 × 100 px** *(atau 512 × 512 px)* | 4:1 *(atau 1:1)* | PNG Transparan, SVG, WebP | Maks. 2 MB | Header navigasi atas (navbar), footer portal, halaman login, kop surat, dan email notifikasi dinas. |
| **2** | **Favicon Peramban (Browser)** | **32 × 32 px** atau **64 × 64 px** | 1:1 (Persegi) | ICO, PNG, SVG | Maks. 1 MB | Ikon tab peramban web, bookmark, shortcut desktop, dan riwayat peramban. |
| **3** | **Hero Slider Banner Beranda** | **1920 × 800 px** *(Min. 1600 × 600 px)* | 2.4:1 (Landscape) | JPG, PNG, WebP | Maks. 3 MB | Banner spanduk bergulir utama pada beranda portal publik untuk sosialisasi agenda diklat prioritas. |
| **4** | **Thumbnail / Cover Kursus Diklat** | **1280 × 720 px** *(Min. 800 × 450 px)* | 16:9 (Widescreen) | JPG, PNG, WebP | Maks. 2 MB | Sampul kartu materi pada katalog diklat publik, kelas aktif, dan dashboard peserta ASN. |
| **5** | **Poster / Banner Webinar & Bimtek** | **1280 × 720 px** | 16:9 (Landscape) | JPG, PNG, WebP | Maks. 2 MB | Poster promosi agenda webinar daring, lokakarya, dan bimtek tatap muka di Sasana Praja. |
| **6** | **Background Piagam Sertifikat** | **2970 × 2100 px** *(A4 300 DPI)* | 1.414:1 (A4 Landscape) | PNG, JPG Berkualitas Tinggi | Maks. 8 MB | Desain blangko sertifikat resmi siap cetak definisi tinggi untuk aparatur yang telah lulus uji kompetensi. |
| **7** | **Logo Lembaga pada Sertifikat** | **300 × 300 px** | 1:1 (Persegi) | PNG Transparan, SVG | Maks. 2 MB | Kop lambang Garuda atau Pemkab Berau di sisi atas piagam sertifikat kelulusan diklat. |
| **8** | **Tanda Tangan Basah Pejabat** | **400 × 200 px** | 2:1 (Landscape) | PNG Transparan (Wajib) | Maks. 1 MB | Tanda tangan basah digital Kepala Badan BKPSDM yang disematkan secara legal di atas segel kriptografi. |
| **9** | **Gambar Dialog Pop-Up Beranda** | **800 × 450 px** *(atau 600 × 400 px)* | 16:9 atau 3:2 | JPG, PNG, WebP | Maks. 2 MB | Spanduk pop-up modal pengumuman darurat atau pendaftaran beasiswa yang muncul seketika di beranda. |
| **10** | **Cover Berita & Knowledge Base** | **1200 × 630 px** | 16:9 / 1.91:1 | JPG, PNG, WebP | Maks. 2 MB | Sampul artikel wawasan kepegawaian, surat edaran, regulasi, dan modul bacaan mandiri. |
| **11** | **Foto Profil ASN / Avatar Pengguna** | **500 × 500 px** | 1:1 (Persegi) | JPG, PNG, WebP | Maks. 2 MB | Foto resmi aparatur berbusana dinas / batik KORPRI untuk identitas profil dan transkrip resmi 20 JP. |

> **Catatan Rekayasa Desain:** Seluruh formulir unggahan pada panel Administrator dan Instruktur kini telah dilengkapi dengan lencana penunjuk resolusi (*guide badge*) `📐 Rekomendasi: ...` secara otomatis untuk memudahkan operator dinas mengunggah gambar dengan dimensi yang tepat tanpa perlu membuka buku manual secara berulang.


---

## 5. Rujukan Dokumen Cetak Siap Pakai (PDF Ukuran A4)

Dokumen lengkap dengan tata letak cetak A4 yang presisi dapat diakses melalui:
- **Tampilan HTML Siap Cetak Browser:**  
  ðŸ‘‰ [**http://127.0.0.1:8000/dokumentasi_lms_berau_a4.html**](http://127.0.0.1:8000/dokumentasi_lms_berau_a4.html)
- **Unduh File PDF Resmi (Ukuran A4):**  
  ðŸ‘‰ [**http://127.0.0.1:8000/DOKUMENTASI_LMS_KAB_BERAU.pdf**](http://127.0.0.1:8000/DOKUMENTASI_LMS_KAB_BERAU.pdf)  
  *(Berkas lokal: `C:\laragon\www\lms\docs\DOKUMENTASI_LMS_KAB_BERAU.pdf`)*

