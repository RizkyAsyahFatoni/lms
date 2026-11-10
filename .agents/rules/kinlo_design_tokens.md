# Kinlo Design System Tokens & Standards
# Path: rules/kinlo_design_tokens.md
# Digunakan untuk: Standarisasi Desain UI, CSS Variables, dan Animasi LMS Berau

---

## 1. Filosofi Desain & Karakteristik Visual
Kinlo Design System mengedepankan estetika **Warm Glassmorphism** yang elegan, bernafas, profesional, dan nyaman untuk penggunaan intensif (dashboard operasional pemerintahan).

### Prinsip Utama:
1. **Background Hangat Berlapis (Wash Gradients):** Menggunakan warna dasar netral hangat `--bg: #eef1f3` dengan sentuhan aksen peach lembut `--wash-a: #f4bfa6` dan aqua tenang `--wash-b: #bfe2e6`.
2. **Glassmorphism Berkualitas Tinggi:** Background semi-transparan ber-blur (`backdrop-filter: blur(26px) saturate(180%)`), bingkai border kaca tipis (`1px solid rgba(255, 255, 255, 0.7)`), dan highlight rim atas (`border-top: 1px solid rgba(255, 255, 255, 0.9)`).
3. **Kurva Gerak Halus (Purposeful Physics):** Transisi menggunakan kurva kubik khas Kinlo:
   ```css
   --ease: cubic-bezier(0.22, 1, 0.36, 1);
   ```
4. **Tipografi Berkarakter:** Font utama **Urbanist** atau sans-serif modern berbobot proporsional (bukan generic sans).

---

## 2. CSS Design Tokens (:root)

```css
:root {
  /* Typography */
  --font: "Urbanist", system-ui, -apple-system, "Segoe UI", sans-serif;
  --fs-2xs: 11px;
  --fs-xs: 12px;
  --fs-sm: 12.5px;
  --fs-md: 13.5px;
  --fs-base: 14px;
  --fs-lg: 15px;
  --fs-xl: 17px;
  --fs-2xl: 20px;
  --fs-3xl: 22px;
  --fs-4xl: 26px;
  --fs-display: 34px;

  /* Border Radii */
  --r-sm: 10px;
  --r-md: 14px;
  --r-lg: 18px;
  --r-xl: 24px;
  --r-full: 9999px;

  /* Animation Easing */
  --ease: cubic-bezier(0.22, 1, 0.36, 1);

  /* Spacing & Dimensions */
  --side-w: 248px;
  --rail-w: 84px;
  --gap: 18px;

  /* Backgrounds & Washes */
  --bg: #eef1f3;
  --wash-a: #f4bfa6;
  --wash-wave: rgba(247, 214, 196, 0.85);
  --wash-b: #bfe2e6;
  --wash-c: #e1eaf3;

  /* Glassmorphism */
  --glass: rgba(255, 255, 255, 0.55);
  --glass-hi: rgba(255, 255, 255, 0.62);
  --glass-rim: rgba(255, 255, 255, 0.9);
  --glass-edge: rgba(23, 37, 45, 0.07);
  --blur: blur(26px) saturate(180%);
  --float: rgba(251, 252, 252, 0.9);

  /* Surfaces & Cards */
  --card: #fbfcfc;
  --card-2: rgba(23, 37, 45, 0.04);
  --card-3: rgba(23, 37, 45, 0.09);

  /* Inks (Text Colors) */
  --ink: #15212a;
  --ink-2: #354550;
  --muted: #55616a;

  /* Borders & Dividers */
  --line: rgba(23, 37, 45, 0.08);
  --line-2: rgba(23, 37, 45, 0.15);

  /* Shadows */
  --shadow: 0 1px 2px rgba(23, 37, 45, 0.04), 0 14px 34px -18px rgba(23, 37, 45, 0.2);
  --shadow-lg: 0 2px 6px rgba(23, 37, 45, 0.06), 0 28px 60px -24px rgba(23, 37, 45, 0.34);

  /* Primary Teal Brand */
  --accent: #0e6c84;
  --accent-ink: #ffffff;
  --accent-hi: #127d98;
  --accent-soft: rgba(14, 108, 132, 0.12);
  --accent-text: #0b5f74;

  /* Status Colors */
  --good: #23733f;
  --good-soft: rgba(35, 115, 63, 0.12);
  --warn: #9a5208;
  --warn-soft: rgba(196, 110, 18, 0.14);
  --bad: #b3263a;
  --bad-soft: rgba(179, 38, 58, 0.11);
  --info: #2c58a0;
  --info-soft: rgba(44, 88, 160, 0.11);
}
```

---

## 3. Komponen Standar Kinlo

### A. Tombol Utama (Primary Button)
```html
<button class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-[14px] bg-[#0e6c84] hover:bg-[#127d98] text-white font-semibold text-sm shadow-[0_4px_16px_rgba(14,108,132,0.25)] hover:shadow-[0_6px_20px_rgba(14,108,132,0.35)] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)]">
  <span>Label Tombol</span>
</button>
```

### B. Pill Filter / Role Switcher (Active & Inactive State)
```html
<!-- Active Pill -->
<button class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#0e6c84] text-white font-semibold text-xs shadow-[0_4px_12px_rgba(14,108,132,0.25)] transition-all">
  <span>Semua Peran</span>
  <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-bold">20</span>
</button>

<!-- Inactive Pill -->
<button class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 hover:bg-white text-[#354550] hover:text-[#0e6c84] border border-[#17252d14] font-medium text-xs shadow-sm hover:shadow transition-all">
  <span>ASN / Learner</span>
  <span class="px-2 py-0.5 rounded-full bg-[#17252d0b] text-[#55616a] text-[11px] font-semibold">11</span>
</button>
```

### C. Glassmorphism Card
```html
<div class="rounded-[20px] bg-[#fbfcfc]/90 backdrop-blur-[24px] border border-white/80 p-6 shadow-[0_1px_2px_rgba(23,37,45,0.04),0_14px_34px_-18px_rgba(23,37,45,0.15)] transition-all hover:shadow-[0_4px_24px_rgba(23,37,45,0.12)]">
  <!-- Konten Card -->
</div>
```

### D. Input Form & Filter Search Field
```html
<div class="relative w-full">
  <input type="text"
         class="w-full h-[46px] pl-10 pr-4 rounded-[14px] bg-white border border-[#17252d1f] text-sm text-[#15212a] placeholder-[#55616a]/60 focus:outline-none focus:border-[#0e6c84] focus:ring-4 focus:ring-[#0e6c84]/10 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)]"
         placeholder="Cari data...">
</div>
```

---

## 4. Animasi & Interaksi
- **Hover micro-interaction:** `-translate-y-0.5` dengan durasi `200ms` dan kurva `cubic-bezier(0.22, 1, 0.36, 1)`.
- **Active / Click:** `translate-y-0 scale-[0.98]` untuk memberikan respon sentuhan fisik yang reaktif.
- **Dropdown / Drawer:** Gunakan fade & scale lembut:
  ```html
  x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-200"
  x-transition:enter-start="opacity-0 translate-y-2 scale-95"
  x-transition:enter-end="opacity-100 translate-y-0 scale-100"
  ```
