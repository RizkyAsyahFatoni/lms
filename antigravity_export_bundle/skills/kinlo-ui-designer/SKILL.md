---
name: kinlo-ui-designer
description: Expert UI/UX designer and frontend engineer for implementing the Kinlo Warm Glassmorphism design system in Laravel Blade, Tailwind CSS, and Alpine.js.
---

# Kinlo UI Designer Skill

Gunakan skill ini ketika diminta membuat, meredesain, atau menyempurnakan halaman antarmuka (UI/UX) pada aplikasi LMS dan sistem internal dengan standar desain Kinlo.

## Standar Desain Wajib
1. **Palet Warna:**
   - Background utama: Warm slate/grey (`#eef1f3`) dengan aksen wash peach (`#f4bfa6`) dan aqua (`#bfe2e6`).
   - Warna Brand Utama: Deep Teal (`#0e6c84`), hover (`#127d98`), soft background (`rgba(14, 108, 132, 0.12)`).
   - Teks Utama (Ink): `#15212a`, teks sekunder `#354550`, teks muted `#55616a`.
2. **Glassmorphism:**
   - Card container ber-radius besar (`rounded-[20px]`).
   - Background semi-transparan `bg-[#fbfcfc]/90 backdrop-blur-[24px]`.
   - Border halus putih transparan `border border-white/80` dengan shadow lembut.
3. **Pill Navigation & Role Filter:**
   - Filter peran/kategori menggunakan pill berbentuk kapsul bulat penuh (`rounded-full`).
   - Tab aktif menggunakan solid teal (`bg-[#0e6c84] text-white`) dengan badge counter transparan.
   - Tab tidak aktif menggunakan putih kaca (`bg-white/80 text-[#354550] border border-[#17252d14]`).
   - Pergantian filter wajib instan (0ms latency) menggunakan Alpine.js client-side filtering.
4. **Form Input & Search:**
   - Input field proporsional dengan tinggi `h-[46px]`, radius `rounded-[14px]`.
   - Focus ring berwarna teal halus (`focus:ring-4 focus:ring-[#0e6c84]/10`).
5. **Animasi & Physics:**
   - Transisi wajib memakai kurva: `cubic-bezier(0.22, 1, 0.36, 1)`.
   - Hover efek: `-translate-y-0.5` halus 200ms.

