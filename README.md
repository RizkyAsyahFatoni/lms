# Learning Management System Pemerintah Kabupaten Berau (LMS KAB Berau)
### Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM)

Platform *Corporate University* digital terpadu untuk fasilitasi, pemantauan, dan pencatatan akumulasi Jam Pelajaran (JP) Aparatur Sipil Negara (ASN) di lingkungan Pemerintah Kabupaten Berau sesuai mandat UU No. 20 Tahun 2023, Perpres No. 95 Tahun 2018 (SPBE), dan Perpres No. 39 Tahun 2019 (Satu Data Indonesia).

---

## 🚀 Panduan Ringkas Memulai (Quick Start)

### Prasyarat Sistem
- **PHP:** 8.2 atau lebih tinggi (ekstensi: `pdo_sqlite`, `openssl`, `mbstring`, `curl`, `fileinfo`, `gd`/`imagick`)
- **Composer:** 2.5+
- **Node.js:** v18+ & NPM v9+
- **Basis Data:** SQLite 3.35+ / MySQL 8.0+

### Langkah Instalasi
```bash
# 1. Kloning repositori & masuk ke direktori
cd C:\laragon\www\lms

# 2. Siapkan file konfigurasi lingkungan
cp .env.example .env

# 3. Pasang dependensi PHP & generate APP_KEY
composer install
php artisan key:generate

# 4. Inisialisasi basis data dan data bawaan
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --class=LmsSeeder
php artisan db:seed --class=SsoAndApiSeeder

# 5. Tautkan penyimpanan publik
php artisan storage:link

# 6. Pasang dependensi frontend & kompilasi aset
npm install
npm run build

# 7. Jalankan server lokal
php artisan serve
```

Akses sistem melalui peramban web di: `http://127.0.0.1:8000`

---

## 🛠️ Tumpukan Teknologi (Tech Stack)
- **Framework Utama:** Laravel 11.x (Clean MVC, Service-Oriented Architecture)
- **Frontend & Styling:** Tailwind CSS v3, Alpine.js v3, Blade UI Components
- **Keamanan Data ASN:** Enkripsi AES-256-CBC dengan Blind Indexing `nip_hash` (HMAC-SHA256)
- **Proteksi Siber:** WAF, Brute Force Shielding, Rate Limiting & Audit Trail (`ActivityLog`)
- **Interoperabilitas:** RESTful API SIMPEG plug-and-play & Multi-provider SSO (SIASN BKN, Keycloak Pemkab Berau)

---

## 📖 Dokumentasi Lengkap & Ekspor PDF (A4)
Buku panduan lengkap sistem, arsitektur data, penjelasan fitur per peran pengguna (Admin, Admin OPD, Trainer, ASN Learner), integrasi REST API, serta tangkapan layar antarmuka dapat diakses melalui:

- 📄 [**Versi HTML Siap Cetak A4 / PDF Export**](http://127.0.0.1:8000/dokumentasi_lms_berau_a4.html) (`public/dokumentasi_lms_berau_a4.html`)
- 📥 [**File PDF Siap Pakai (Ukuran A4)**](docs/DOKUMENTASI_LMS_KAB_BERAU.pdf) (`docs/DOKUMENTASI_LMS_KAB_BERAU.pdf`)
- 📝 [**Dokumentasi Teknis Markdown**](docs/DOKUMENTASI_LMS_KAB_BERAU.md) (`docs/DOKUMENTASI_LMS_KAB_BERAU.md`)

---
*Dikembangkan secara resmi untuk Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM) Pemerintah Kabupaten Berau.*
