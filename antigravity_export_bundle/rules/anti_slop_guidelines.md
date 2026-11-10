# Anti-Slop & Quality Guidelines (UI, Copywriting, & Code)

Pedoman permanen ini diadopsi dari 7 repositori standar industri:
1. `Leonxlnx/taste-skill` (Art direction, visual differentiation, page vs app screen)
2. `miqdadbadjuber/anti-slop` (Filter anti-generik untuk UI, teks, dan kode)
3. `pbakaus/impeccable` (Design system, 24 perintah desain, a11y, no nested cards)
4. `blader/humanizer` (Menghilangkan pola tulisan AI berdasarkan standar Wikipedia)
5. `hardikpandya/stop-slop` (Eliminasi AI crutches & buzzwords)
6. `petergyang/no-ai-slop` (Menjaga suara autentik manusia & no binary contrasts)
7. `conorbronsdon/avoid-ai-writing` (Katalog tier kata terlarang, deteksi struktural, fakta tanpa inflasi)

Pedoman ini menjadi standar wajib dalam pembuatan project baru, penambahan fitur, perancangan UI/UX, penulisan konten/copywriting, serta penulisan kode.

---

## 1. Frontend & UI/UX Design (Anti-Slop UI)

Hindari tampilan "AI template default" (tampilan rata-rata yang seragam dan membosankan).

### A. Larangan Keras (Hard Gates)
- **NO Generic AI Elements:** Dilarang menggunakan logo sparkle/bintang AI default, pill badge "NEXT-GEN AI 2.0", atau fake terminal dengan latency fiktif (0.0001ms).
- **NO Nested Cards:** Hindari membungkus semua elemen dalam kartu di dalam kartu (card soup). Gunakan whitespace, divider halus, atau grid terbuka.
- **NO Pure Black/White/Grey:** Selalu berikan sedikit tint warna brand/hangat pada neutral colors (misal slate, zinc, atau warm grey), jangan gunakan `#000000` atau `#888888` mentah.
- **NO Default Font Fallback:** Jangan otomatis memakai Inter/Arial untuk semua hal tanpa pertimbangan. Pilih tipografi yang memiliki karakter kuat dan sesuai konteks aplikasi.
- **NO Gray Text on Colored Background:** Pastikan rasio kontras warna selalu memenuhi standar aksesibilitas (WCAG AA).
- **NO Dated Animations:** Hindari efek bounce/elastic berlebihan. Gunakan transisi halus, terarah, dan memiliki tujuan fungsional (*purposeful motion*).

### B. Pemisahan Dua Jenis Antarmuka
1. **Public/Landing Page (Scrolled & Read):**
   - Fokus pada hierarki visual, narasi yang mengalir, kontras ukuran teks yang tegas (*type scale*), dan visual nyata (bukan ilustrasi abstrak ungu-biru generik).
   - Susunan seksi harus mengikuti argumen nyata produk, bukan template kaku (*hero -> 3 icon cards -> fake testimonials -> pricing*).
2. **App Screen / Dashboard (Worked in):**
   - Fokus pada densitas data, kemudahan scanning, ketepatan fungsional, dan kestabilan layout.
   - Hindari grafik pelangi (*rainbow charts*) atau angka metrik palsu (*vanity metrics*).
   - Border halus, tabel yang mudah disortir/filter, status badge yang jelas, dan state interaksi (loading, empty, error, disabled) yang matang.

---

## 2. Copywriting & Teks (No AI Writing Patterns)

Bahasa yang dihasilkan harus terdengar seperti ditulis oleh manusia ahli, lugas, profesional, dan to-the-point.

### A. Pola Struktur yang Dilarang (AI Tells)
1. **Binary Contrast ("Not X, but Y"):**
   - *Dilarang:* "Ini bukan sekadar aplikasi, ini adalah revolusi cara Anda bekerja."
   - *Seharusnya:* Langsung nyatakan fungsinya secara konkret.
2. **One-Line Dramatic Closers:**
   - *Dilarang:* "Let that sink in.", "Renungkan itu.", "Dan itu baru permulaan.", "The message was clear."
3. **Staged Run-ups / Throat-clearing:**
   - *Dilarang:* "Here's the thing...", "At its core...", "Honestly?", "Certainly!", "In today's fast-paced digital world..."
4. **Pernyataan Klise & Sok Filosofis:**
   - *Dilarang:* Membuka paragraf dengan definisi puitis atau analogi berlebihan yang tidak diminta pembaca.
5. **Inflasi Makna (Unearned Hype):**
   - Jangan menambah klaim berlebihan tanpa bukti nyata (*groundbreaking, game-changing, revolutionize, seamless integration* jika faktanya hanya fitur standar).

### B. Kata-Kata Terlarang / Hindari (AI Vocabulary Blacklist)
- **Hindari kata:** *delve, tapestry, leverage, foster, paramount, beacon, watershed, nestled, vibrant, holistic, synergy, realm, testament to, poised to*.
- **Ganti dengan kata lugas:**
  - *Leverage* -> Gunakan / Pakai
  - *Delve into* -> Pelajari / Bahas / Teliti
  - *Foster* -> Bangun / Kembangkan / Dukung
  - *Utilize* -> Pakai / Gunakan
  - *In order to* -> Untuk

### C. Integritas Fakta
- Jangan mengarang nama, angka, tanggal, persentase, atau kutipan.
- Jika data tidak tersedia, tanyakan langsung atau gunakan bahasa netral tanpa klaim palsu.

---

## 3. Clean Code & Architecture (Anti AI-shaped Code)

Kode harus fungsional, bersih, mudah dirawat, dan tidak memuat artefak AI yang berisik.

### A. Kebersihan Komentar
- **Hapus Komentar Basa-Basi:** Jangan menulis komentar yang hanya mengulang baris kode (misal: `// increment i by 1`, `// return the response`).
- **NO ASCII Art / Decorative Banners:** Dilarang membuat border dekoratif panjang (misal: `// ========================================== //`).
- Komentar hanya boleh menjelaskan alasan desain (*why*), arsitektur kompleks, atau peringatan edge case.

### B. Robustness & Hardening (Prinsip Impeccable)
- **Edge Cases:** Tangani input kosong (*empty state*), error handling, validasi input, dan batas ukuran file/karakter.
- **Konsistensi Arsitektur:** Ikuti standar framework yang dipakai (misal Laravel / Vue / React) tanpa mencampuradukkan pola aneh.
- **Siap Produksi:** Hindari meninggalkan `TODO` atau fungsi mock tanpa penjelasan transparan kepada pengguna.

---

## 4. Alur Kerja Implementasi (Workflow)

Dalam setiap pengerjaan fitur atau proyek:
1. **Pahami Subjek & Fakta:** Ekstrak apa yang benar-benar nyata (kebutuhan bisnis Tony, database yang ada, batasan hosting cPanel).
2. **Eliminasi Slop:** Sebelum mengirimkan kode atau teks, lakukan self-check terhadap pola AI slop di atas.
3. **Hasil Berkualitas Tinggi:** Pastikan output terasa premium, dibuat dengan sengaja (*intentional*), dan efisien untuk operasional konsultan SDM & LMS Tony.
