# Panduan Migrasi & Pemulihan Antigravity LMS Berau
# File: README_MIGRATION.md
# Tujuan: Menyiapkan kembali seluruh Rule, Skill, Knowledge, dan Konfigurasi di Laptop Baru

Dokumen ini berisi panduan terstruktur untuk memindahkan lingkungan kerja Google Antigravity dan project **LMS Kabupaten Berau** (`lms`) ke laptop baru tanpa kehilangan preferensi, pedoman kualitas, desain sistem, maupun konteks keahlian sistem.

---

## 1. Struktur Isi Paket Ekspor

```text
antigravity_export_bundle/
├── rules/
│   ├── project_guidelines.md     <- Panduan wajib project LMS (arsitektur, model, standardisasi controller)
│   ├── anti_slop_guidelines.md    <- Pedoman anti-slop UI, copywriting manusia autentik, & kualitas kode
│   ├── kinlo_design_tokens.md     <- Token CSS, kurva cubic-bezier, palet warna, & standar komponen Kinlo
│   ├── user_profile.md            <- Profil peran Tony (Divisi PDV Consulting & ODH, preferensi komunikasi)
│   └── hosting_mapping.md         <- Pemetaan hosting cPanel pdvconsulting.id, people-org, & database
├── skills/
│   ├── kinlo-ui-designer/         <- Custom skill Antigravity untuk implementasi antarmuka Kinlo Glassmorphism
│   │   └── SKILL.md
│   └── lms-domain-expert/         <- Custom skill Antigravity untuk aturan bisnis ASN, JP, SPBE, & notifikasi
│       └── SKILL.md
├── knowledge/
│   ├── DOKUMENTASI_LMS_KAB_BERAU.md <- Dokumentasi teknis lengkap sistem (50+ halaman)
│   ├── DATABASE_SCHEMA_SUMMARY.md   <- Ringkasan skema 19 tabel relasional utama dan migrasi
│   ├── SECURITY_ENCRYPTION_SPEC.md  <- Arsitektur enkripsi AES-256-CBC, Blind Index, & sanitasi data
│   └── EMAIL_NOTIFICATION_SYSTEM.md <- Spesifikasi SMTP dinamis & 7 skenario notifikasi KAK
├── design-system/
│   ├── kinlo.css                  <- CSS referensi murni Kinlo
│   └── kinlo.js                   <- Script interaksi & micro-animations Kinlo
└── README_MIGRATION.md            <- Dokumen panduan ini
```

---

## 2. Langkah Pemulihan di Laptop Baru

### Langkah 1: Pasang Antigravity & Laragon di Laptop Baru
1. Pasang aplikasi **Google Antigravity** di laptop baru.
2. Pasang **Laragon** (atau stack PHP 8.2+ dan Composer) pada laptop baru:
   - PHP versi `>= 8.2` (disarankan PHP 8.3).
   - Pastikan extension `pdo_sqlite`, `fileinfo`, `gd`, `zip`, `openssl`, `mbstring` aktif.
   - Node.js versi `>= 18.x` dan NPM.

---

### Langkah 2: Salin File Aturan Global (User-Level Rules)
Antigravity membaca preferensi dan aturan pengguna secara global dari direktori profil pengguna.

1. Buka File Explorer di laptop baru, tuju path:
   ```text
   C:\Users\<Username-Baru>\.gemini\config\rules\
   ```
   *(Jika folder `config` atau `rules` belum ada, buat foldernya terlebih dahulu)*
2. Salin 3 file dari folder `rules/` paket ini ke direktori di atas:
   - `anti_slop_guidelines.md`
   - `user_profile.md`
   - `hosting_mapping.md`

> **Manfaat:** Antigravity pada laptop baru akan langsung mengenali identitas Tony, menghindari respon berbasis template AI generik (anti-slop), dan mengingat seluruh pemetaan server/domain tanpa perlu dijelaskan ulang di setiap sesi baru.

---

### Langkah 3: Tempatkan Workspace Rules pada Project LMS
1. Salin folder codebase project `lms` ke folder web server lokal di laptop baru (misal: `C:\laragon\www\lms`).
2. Pastikan di dalam folder project terdapat direktori:
   ```text
   C:\laragon\www\lms\.agents\rules\
   ```
3. Salin 2 file berikut ke dalam `C:\laragon\www\lms\.agents\rules\`:
   - `project_guidelines.md`
   - `kinlo_design_tokens.md`

---

### Langkah 4: Registrasi Custom Skills ke Antigravity
Untuk membuat skill `kinlo-ui-designer` dan `lms-domain-expert` dapat dipanggil secara otomatis oleh Antigravity:

1. Buka direktori skills Antigravity di laptop baru:
   ```text
   C:\Users\<Username-Baru>\.gemini\antigravity\skills\
   ```
   *(atau simpan di folder `.gemini/skills/` pada root project jika ingin berbasis workspace)*
2. Salin seluruh sub-folder dari `skills/`:
   - Salin folder `kinlo-ui-designer` ke `C:\Users\<Username-Baru>\.gemini\antigravity\skills\kinlo-ui-designer\`
   - Salin folder `lms-domain-expert` ke `C:\Users\<Username-Baru>\.gemini\antigravity\skills\lms-domain-expert\`

---

### Langkah 5: Menyiapkan File `.env` & Kunci Keamanan
Saat project Laravel dipindahkan, buat/perbarui file `.env`:

```bash
cd C:\laragon\www\lms
cp .env.example .env
```

Pastikan variabel penting berikut diisi (jangan gunakan nilai acak untuk salt enkripsi jika sudah ada database yang terisi):
```dotenv
APP_NAME="LMS Kabupaten Berau"
APP_ENV=local
APP_KEY=base64:... # Generate via 'php artisan key:generate'
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# DB_DATABASE otomatis mengarah ke database/database.sqlite

# Salt untuk Blind Index NIK/NIP ASN (Wajib konsisten)
ASN_ENCRYPTION_SALT=berau_lms_secure_blind_index_salt_2026
```

---

### Langkah 6: Jalankan Dependency & Uji Coba Sistem
Buka terminal PowerShell pada direktori project:

```powershell
# 1. Install PHP dependencies
composer install

# 2. Install Javascript dependencies & build asset
npm install
npm run build

# 3. Jalankan migrasi dan seeder
php artisan migrate --force

# 4. Verifikasi seluruh unit test
php artisan test
```

Semua 57 test suite wajib lulus (Green / Passed).

---

## 3. Checklist Verifikasi Setelah Migrasi

- [ ] Folder `%USERPROFILE%\.gemini\config\rules\` berisi `anti_slop_guidelines.md`, `user_profile.md`, dan `hosting_mapping.md`.
- [ ] Folder `C:\laragon\www\lms\.agents\rules\` berisi `project_guidelines.md` dan `kinlo_design_tokens.md`.
- [ ] Folder skills Antigravity memuat `kinlo-ui-designer` dan `lms-domain-expert`.
- [ ] Buka Antigravity dan uji prompt singkat (misal: *"Halo, periksa status modul LMS Berau"*). Antigravity harus langsung menyapa dengan sapaan "Tony" dan memahami konteks LMS BKPSDM Berau secara presisi.
- [ ] Jalankan dev server (`php artisan serve`) dan akses halaman `/admin/mail-settings` serta `/admin/users` untuk memverifikasi UI Kinlo berjalan sempurna.

