# Spesifikasi Keamanan & Enkripsi Data ASN
# Path: knowledge/SECURITY_ENCRYPTION_SPEC.md

Standar keamanan data pribadi ASN (Aparatur Sipil Negara) yang diimplementasikan pada LMS Kabupaten Berau untuk mematuhi regulasi UU Perlindungan Data Pribadi (UU PDP) dan standar SPBE.

---

## 1. Skema Enkripsi Simetris (AES-256-CBC)
Data sensitif identitas pegawai (NIK, Nomor Kontak Pribadi, Alamat Rumah) tidak disimpan dalam bentuk *plaintext* pada basis data.

- **Algoritma:** `AES-256-CBC`
- **Kunci Enkripsi:** Menggunakan `APP_KEY` Laravel dipadukan dengan konfigurasi keamanan `ASN_ENCRYPTION_SALT`.
- **Implementasi:** Dilakukan melalui Eloquent Cast kustom atau service class `SecurityService.php`.

---

## 2. Pencarian Cepat dengan Blind Index (HMAC-SHA256)
Tantangan dari data terenkripsi adalah query pencarian (`WHERE nip = ?` atau `WHERE nik = ?`). Untuk menjaga kecepatan query database tingkat tinggi tanpa mengekspos plaintext:
- Sistem membuat kolom pendamping: `nik_blind_index` dan `nip_blind_index`.
- Nilai blind index dihitung menggunakan:
  ```php
  $blindIndex = hash_hmac('sha256', $value, env('ASN_ENCRYPTION_SALT'));
  ```
- Ketika operator melakukan pencarian NIK pada form filter/search, inputan dicari melalui hashing HMAC yang sama lalu dicocokkan ke kolom blind index yang telah terindeks B-Tree di database.

---

## 3. Mitigasi Vulnerability (CVE-2024-4577 & Security Hardening)
- **CGI Argument Injection:** Pembaruan interpreter PHP ke versi stabil 8.2+ / 8.3 dengan konfigurasi php.ini aman (`cgi.force_redirect = 1`).
- **HTML Sanitization:** Seluruh input materi pembelajaran bertipe Rich Text (WYSIWYG) disaring menggunakan `HTMLPurifier` (`ezyang/htmlpurifier`) untuk mencegah injeksi script berbahaya (Stored XSS).
- **CSRF & Rate Limiting:** Seluruh form dilindungi token CSRF otomatis dan pembatasan frekuensi percobaan login (*rate limiting*) pada endpoint otentikasi.
- **Kredensial SMTP:** Password akun SMTP pada tabel `mail_settings` disimpan dalam bentuk terenkripsi menggunakan helper `Crypt::encryptString()` dan hanya didekripsi saat sesi pengiriman email berlangsung.

