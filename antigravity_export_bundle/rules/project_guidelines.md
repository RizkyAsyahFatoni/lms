# Project Guidelines: LMS KAB Berau
# Path: C:\laragon\www\lms
# Last Updated: 2026-10-27

Dokumen ini adalah panduan permanen untuk pengembangan project **LMS KAB Berau**.
Wajib dibaca dan dipatuhi dalam setiap sesi kerja, baik itu penambahan fitur, perbaikan bug, refactor, maupun pembuatan halaman baru.

---

## 1. Stack Teknologi

| Layer | Teknologi | Versi |
|---|---|---|
| **Backend** | Laravel | 11.x |
| **PHP** | PHP | ^8.2 |
| **Database (lokal)** | SQLite | `database/database.sqlite` |
| **CSS Framework** | Tailwind CSS | ^3.4 |
| **CSS Plugin** | @tailwindcss/forms, @tailwindcss/typography | latest |
| **JS Framework** | Alpine.js | ^3.17 |
| **Bundler** | Vite + laravel-vite-plugin | ^5.0 / ^1.0 |
| **Template Engine** | Blade (.blade.php) | - |
| **Export** | Maatwebsite Excel | ^3.1 |
| **HTML Sanitizer** | ezyang/htmlpurifier | ^4.18 |

---

## 2. Arsitektur & Struktur Direktori

```
C:\laragon\www\lms\
├── app/
│   ├── Http/
│   │   ├── Controllers/          <- Controller pengguna biasa & publik
│   │   │   ├── Admin/            <- Controller khusus admin (AdminController, UserController, ReportCenter, dll)
│   │   │   └── Api/              <- Controller endpoint API
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/                   <- 38+ Eloquent model (User, Course, Webinar, Enrollment, dll)
│   └── Services/                 <- Business logic class (SecurityService, JpService, SimpegApiService, dll)
├── database/
│   ├── migrations/               <- 30 file migrasi (2026-09-16 s.d. 2026-10-13)
│   ├── seeders/
│   └── database.sqlite           <- Database lokal SQLite
├── resources/
│   ├── css/app.css               <- Entry CSS (Tailwind @import)
│   ├── js/app.js                 <- Entry JS (import Alpine)
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php     <- Layout utama (user-facing, header sticky, nav, footer)
│       │   └── admin.blade.php   <- Layout admin panel
│       ├── admin/                <- View halaman admin
│       ├── courses/              <- View modul pelatihan
│       ├── dashboard/            <- View dashboard user
│       ├── webinars/             <- View webinar
│       ├── attendance/           <- View presensi digital
│       ├── certificates/         <- View sertifikat
│       ├── articles/             <- View artikel
│       ├── trainer/              <- View panel trainer
│       ├── home.blade.php        <- Landing page utama (file besar ~42KB)
│       └── welcome.blade.php     <- Halaman welcome (~35KB)
├── public/
│   └── uploads/                  <- Upload file pengguna (dilindungi .htaccess)
└── routes/web.php                <- Semua route aplikasi
```

---

## 3. Design System (Wajib Diikuti)

### A. Palet Warna Brand
```js
// tailwind.config.js - Brand: Teal/Berau
brand-50:  '#f0fdfa'
brand-500: '#14b8a6'  // Primary accent utama
brand-600: '#0d9488'  // Hover state
brand-700: '#0f766e'  // Active / pressed
brand-800: '#115e59'  // Header/dark section
brand-900: '#134e4a'  // Deep dark
// Surface
surface-light:  '#f8fafc'  // Background halaman
surface-card:   '#ffffff'  // Background card
surface-border: '#e2e8f0'  // Garis border
```

### B. Tipografi
- **Font Utama:** `Plus Jakarta Sans` (weight: 300, 400, 500, 600, 700, 800) — sudah di-load via Google Fonts di layout utama.
- **Fallback:** Inter, system-ui.
- **Dilarang:** Memakai font lain tanpa justifikasi desain yang jelas.

### C. Shadow & Elevasi
```js
shadow-v0:       '0 1px 2px 0 rgba(0,0,0,0.05)'            // Shadow minimal
shadow-v0-card:  '0 1px 3px rgba(0,0,0,0.04), ...'          // Card
shadow-v0-hover: '0 10px 25px -5px rgba(13,148,136,0.1)...' // Hover teal glow
```

### D. Pola Visual Wajib (No AI Slop)
- ❌ **NO nested cards** (card dalam card — gunakan whitespace atau divider `border-slate-100`).
- ❌ **NO pure black** — gunakan `slate-900` atau `slate-800` bukan `#000000`.
- ❌ **NO gray text on colored backgrounds** — pastikan kontras WCAG AA terpenuhi.
- ❌ **NO gradient abstrak purple-biru generik** — tetap pada palet teal brand.
- ✅ **Gunakan state yang jelas:** loading, empty state, error, disabled.
- ✅ **Gunakan `antialiased` + `font-sans`** di semua container baru.
- ✅ Tombol CTA utama: `bg-brand-600 hover:bg-brand-700 text-white`.
- ✅ Tombol secondary: `border border-slate-200 bg-white hover:bg-slate-50 text-slate-700`.

---

## 4. Pola Laravel yang Dipakai di Proyek Ini

### A. Controller
- Controller user biasa: `app/Http/Controllers/*.php`
- Controller admin: `app/Http/Controllers/Admin/*.php`
- Pakai **Form Request** untuk validasi (folder `app/Http/Requests/`).
- Business logic kompleks pisahkan ke **Service** (`app/Services/`).

### B. Model Utama & Relasi Penting
| Model | Keterangan |
|---|---|
| `User` | User utama (peserta, trainer, admin) dengan NIK encrypted |
| `Course` | Kursus/pelatihan, memiliki Lesson, Enrollment, Evaluation |
| `Webinar` | Webinar (sync/async), memiliki Registration, LearningRecord |
| `Enrollment` | Relasi User ↔ Course, dengan progress tracking |
| `Attendance` / `AttendanceSession` | Sistem presensi digital |
| `Certificate` | Sertifikat per course |
| `OpdActivityRequest` | Permintaan kegiatan dari OPD |
| `RolePermission` | Sistem permission berbasis role |
| `AppSetting` | Konfigurasi tampilan aplikasi (nama app, logo, favicon, tagline) |
| `ActivityLog` | Log aktivitas pengguna (audit trail) |

### C. View & Blade
- Selalu `@extends('layouts.app')` untuk halaman user-facing.
- Selalu `@extends('layouts.admin')` untuk halaman admin.
- Gunakan `@section('content')` ... `@endsection`.
- Untuk head tambahan (chart library, custom CSS): `@yield('head_extra')` / `@section('head_extra')`.
- Gunakan `$appSetting` yang di-share ke semua view via ViewServiceProvider (sudah tersedia).

### D. Database & Migrasi
- **Lokal:** SQLite (`database/database.sqlite`).
- Setiap kolom baru wajib dibuatkan migration baru — **jangan edit migrasi lama** yang sudah dijalankan.
- Nama tabel mengikuti konvensi Laravel (snake_case plural).

### E. Services yang Sudah Ada (Jangan Duplikasi)
- `SecurityService` — sanitasi input, enkripsi.
- `HtmlPurifierService` — sanitasi HTML dari user.
- `JpService` — kalkulasi JP (Jam Pelajaran).
- `SimpegApiService` — integrasi API SIMPEG external.
- `NotificationService` — kirim notifikasi in-app.
- `EncryptionService` — enkripsi data sensitif (NIK).

---

## 5. Pedoman Penulisan Kode (Anti AI-Shaped Code)

### A. Komentar
- ✅ **Tulis komentar untuk:** alasan desain arsitektur, logika kompleks, edge case non-obvious, catatan integrasi API.
- ❌ **Jangan tulis:** komentar yang hanya mengulang nama fungsi/baris kode.
- ❌ **Jangan buat:** banner ASCII dekoratif atau section divider panjang (`// =====================`).

### B. Robustness
- Selalu tangani: input kosong (empty state), file tidak ditemukan, response API gagal, user tanpa data.
- Gunakan `try-catch` pada operasi I/O, integrasi API, dan enkripsi.
- Validasi di Form Request, bukan langsung di Controller.

### C. Konsistensi
- Ikuti pola controller yang sudah ada (lihat `CourseController` atau `WebinarController` sebagai referensi).
- Jangan mencampurkan logika bisnis ke dalam Blade view.
- Gunakan Eloquent relationship — hindari raw query manual kecuali sangat dibutuhkan untuk performa.

---

## 6. Pedoman Copywriting & Teks UI (No AI Writing)

Teks UI, label, notifikasi, judul halaman, dan pesan error di aplikasi ini harus:
- **Lugas & langsung:** hindari kalimat yang panjang dan berputar-putar.
- **Bahasa Indonesia profesional** — bukan bahasa korporat klise (*"Revolusikan cara Anda belajar!"*).
- **Bebas dari AI tells:** tidak ada "Delve into...", "Tapestry of...", "Seamlessly integrated".
- **Berbasis fungsi nyata:** label tombol dan link harus mendeskripsikan aksi yang terjadi.
  - ❌ `Mulai Perjalanan Anda` → ✅ `Daftar Pelatihan`
  - ❌ `Explore Our Solutions` → ✅ `Lihat Semua Kursus`

---

## 7. Konteks Bisnis & Pengguna

- **Nama Aplikasi:** LMS KAB Berau
- **Pengelola:** Tony (M Imam Fatoni) — Staff IT & Digital Specialist, Divisi ODH
- **Target Pengguna:** Peserta pelatihan (ASN / karyawan), Trainer, Admin
- **Domain Lokal:** `http://lms.test`
- **Fitur Utama:**
  - Manajemen kursus, lesson, materi (video/PDF/dokumen)
  - Sistem webinar (sinkronus & asinkronus)
  - Presensi digital via QR/link
  - Sertifikat otomatis
  - Artikel & pengetahuan
  - IDP (Individual Development Plan)
  - Laporan JP (Jam Pelajaran)
  - Integrasi SIMPEG & SSO
  - Bank Soal & Kuis
  - OPD Activity Request (permintaan kegiatan)
  - Role-based Permission
  - Audit log aktivitas

---

## 8. Checklist Sebelum Commit / Selesai Mengerjakan Fitur

- [ ] Migrasi baru dibuat jika ada perubahan skema database.
- [ ] Validasi input menggunakan Form Request atau `$request->validate()`.
- [ ] Tidak ada data sensitif di-log atau dicetak ke view tanpa enkripsi.
- [ ] View baru sudah menggunakan layout yang tepat (`app` atau `admin`).
- [ ] Tidak ada kode debug (`dd()`, `dump()`, `var_dump()`) yang tertinggal.
- [ ] Teks dan label UI menggunakan Bahasa Indonesia yang tepat dan lugas.
- [ ] Komponen Blade konsisten dengan design system (warna brand, font, shadow).
- [ ] Empty state, error state, dan loading state sudah ditangani di view.
