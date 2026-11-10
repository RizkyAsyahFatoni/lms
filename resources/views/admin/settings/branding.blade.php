@extends('layouts.admin')

@section('page_title', 'Pengaturan Logo & Favicon')

@section('content')
<div class="space-y-6"
     x-data="{
         appName: '{{ addslashes($setting->app_name) }}',
         tagline: '{{ addslashes($setting->tagline) }}',
         badgeText: '{{ addslashes($setting->badge_text ?? 'PRO') }}',
         logoPreview: '{{ $setting->logo_url ? $setting->logo_url : asset('images/logo.svg') }}',
         faviconPreview: '{{ $setting->favicon_url ? $setting->favicon_url : asset('favicon.svg') }}',
         previewLogo(event) {
             const file = event.target.files[0];
             if (file) {
                 this.logoPreview = URL.createObjectURL(file);
             }
         },
         previewFavicon(event) {
             const file = event.target.files[0];
             if (file) {
                 this.faviconPreview = URL.createObjectURL(file);
             }
         }
     }">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white flex items-center justify-center font-black shadow-sm">
                    🎨
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pengaturan Logo, Favicon & Identitas Brand</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola nama aplikasi resmi, unggah logo kustom instansi, dan atur favicon tab browser.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" 
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition flex items-center gap-2">
                <span>Lihat Beranda Publik</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <button type="submit" form="brandingForm" 
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <!-- Overview Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[11px] font-medium text-slate-500">Nama Aplikasi Aktif</span>
            <p class="text-lg font-black text-slate-900 mt-0.5" x-text="appName || 'LMS KAB Berau'"></p>
            <span class="text-[10px] font-mono text-emerald-600 font-semibold">● Terhubung ke Seluruh Halaman</span>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[11px] font-medium text-slate-500">Status Logo Utama</span>
            <p class="text-lg font-black text-slate-900 mt-0.5">
                {{ $setting->logo_path ? 'Logo Kustom Terpasang' : 'Logo Vektor LMS KAB Berau' }}
            </p>
            <span class="text-[10px] font-mono {{ $setting->logo_path ? 'text-indigo-600' : 'text-teal-600' }} font-semibold">
                {{ $setting->logo_path ? 'File: ' . basename($setting->logo_path) : '✓ Format SVG Resmi' }}
            </span>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[11px] font-medium text-slate-500">Favicon Browser</span>
            <p class="text-lg font-black text-slate-900 mt-0.5">
                {{ $setting->favicon_path ? 'Favicon Kustom' : 'Favicon Vektor Bawaan' }}
            </p>
            <span class="text-[10px] font-mono text-slate-500">
                Format: .svg, .ico, .png didukung
            </span>
        </div>
    </div>

    <!-- Main Grid: Settings Form & Live Previews -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left 7 Cols: Form Settings -->
        <div class="lg:col-span-7 space-y-6">
            
            <form id="brandingForm" action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" 
                  class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
                @csrf

                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Identitas Dasar Aplikasi</h3>
                    <p class="text-xs text-slate-500">Nama dan identitas yang akan ditampilkan pada header, email, dan meta tag.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Aplikasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="app_name" x-model="appName" required
                               placeholder="LMS KAB Berau"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-bold transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Badge</label>
                        <input type="text" name="badge_text" x-model="badgeText"
                               placeholder="PRO"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono font-bold transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tagline / Slogan Subjudul</label>
                    <input type="text" name="tagline" x-model="tagline"
                           placeholder="Ekosistem Pembelajaran Digital Terintegrasi"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                </div>

                <!-- Section: Logo Upload -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Unggah File Logo Utama</label>
                                <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                    📐 Rekomendasi: 400 × 100 px (Horizontal) / 512 × 512 px (Emblem)
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format disarankan: <strong>PNG transparan</strong>, <strong>SVG</strong>, atau <strong>WebP</strong> (Maks. 2 MB). Tinggi tampilan standar navbar 40–48 px.</p>
                        </div>

                        @if($setting->logo_path)
                            <button type="submit" formaction="{{ route('admin.branding.reset-logo') }}" 
                                    class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 hover:underline">
                                ↺ Reset ke Logo Vektor Bawaan
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 p-3.5 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 p-2 flex items-center justify-center shrink-0 shadow-2xs">
                            <img :src="logoPreview" alt="Logo Preview" class="max-h-full max-w-full object-contain">
                        </div>

                        <div class="flex-1 min-w-0">
                            <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                   @change="previewLogo($event)"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-brand-700 file:cursor-pointer cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Jika tidak diunggah, logo vektor resmi LMS KAB Berau akan otomatis digunakan.</p>
                        </div>
                    </div>
                </div>

                <!-- Section: Favicon Upload -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Unggah File Favicon Browser</label>
                                <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                    📐 Rekomendasi: 32 × 32 px atau 64 × 64 px (Rasio 1:1)
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format disarankan: <strong>.png</strong>, <strong>.ico</strong>, atau <strong>.svg</strong> (Maks. 1 MB). Ikon kecil untuk tab browser dan bookmark.</p>
                        </div>

                        @if($setting->favicon_path)
                            <button type="submit" formaction="{{ route('admin.branding.reset-favicon') }}" 
                                    class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 hover:underline">
                                ↺ Reset ke Favicon Bawaan
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 p-3.5 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 p-2 flex items-center justify-center shrink-0 shadow-2xs">
                            <img :src="faviconPreview" alt="Favicon Preview" class="w-7 h-7 object-contain">
                        </div>

                        <div class="flex-1 min-w-0">
                            <input type="file" name="favicon" accept=".ico,image/png,image/svg+xml,image/x-icon"
                                   @change="previewFavicon($event)"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-brand-700 file:cursor-pointer cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Ukuran optimal 32x32 atau 64x64 piksel persegi.</p>
                        </div>
                    </div>
                </div>

                <!-- Section: JP Target & Fiscal Year Cycle Config -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Target & Periode Perhitungan JP ASN</h3>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">ASN Standard</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi target kewajiban jam pelajaran (JP) tahunan bagi ASN dan siklus tahun perhitungannya.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target JP Default Tahunan</label>
                            <select name="jp_default_target" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                                <option value="20" {{ ($setting->jp_default_target ?? 20) == 20 ? 'selected' : '' }}>20 JP / Tahun (Standar Default LAN / BKN)</option>
                                <option value="24" {{ ($setting->jp_default_target ?? 20) == 24 ? 'selected' : '' }}>24 JP / Tahun (PermenpanRB / Daerah Khusus)</option>
                                <option value="30" {{ ($setting->jp_default_target ?? 20) == 30 ? 'selected' : '' }}>30 JP / Tahun (Pengembangan Lanjutan)</option>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">Dapat disesuaikan secara individu per profil peserta jika dibutuhkan.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Siklus Periode Perhitungan JP</label>
                            <select name="jp_year_start_month" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                                <option value="1" {{ ($setting->jp_year_start_month ?? 1) == 1 ? 'selected' : '' }}>Tahun Kalender (1 Januari - 31 Desember)</option>
                                <option value="4" {{ ($setting->jp_year_start_month ?? 1) == 4 ? 'selected' : '' }}>Tahun Anggaran (1 April - 31 Maret)</option>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">Menentukan rentang waktu akumulasi JP tahunan peserta.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all hover:shadow">
                        Simpan Pengaturan Branding
                    </button>
                </div>
            </form>

        </div>

        <!-- Right 5 Cols: Live Previews -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card 1: Browser Tab Simulation -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">1. Pratinjau Tab Browser:</span>
                    <span class="text-[10px] font-mono text-teal-600 font-bold">Real-time</span>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-100 p-2 space-y-2">
                    <!-- Browser Tab Bar -->
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5 px-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-rose-400"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                        </div>

                        <!-- Active Tab -->
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-2xs border border-slate-200 max-w-xs text-xs truncate">
                            <img :src="faviconPreview" class="w-3.5 h-3.5 rounded object-contain shrink-0" alt="Favicon">
                            <span class="truncate font-medium text-slate-800 text-[11px]" 
                                  x-text="(appName || 'LMS KAB Berau') + ' | ' + (tagline || 'Ekosistem Pembelajaran')"></span>
                            <span class="text-slate-400 hover:text-slate-600 text-[10px] ml-auto">✕</span>
                        </div>
                    </div>

                    <!-- URL Bar Mockup -->
                    <div class="bg-white rounded-md px-3 py-1 text-[10px] font-mono text-slate-400 border border-slate-200 truncate">
                        🔒 https://lms.beraukab.go.id/
                    </div>
                </div>
            </div>

            <!-- Card 2: Navbar Header Simulation -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">2. Pratinjau Header Navbar Pengunjung:</span>
                    <span class="text-[10px] font-mono text-teal-600 font-bold">Real-time</span>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <img :src="logoPreview" class="h-9 w-auto max-w-[140px] object-contain" alt="Logo Preview">
                        </div>

                        <div class="hidden sm:flex items-center gap-2 text-xs font-semibold">
                            <span class="text-slate-500 text-[11px]">Beranda</span>
                            <span class="text-slate-500 text-[11px]">Katalog</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[10px] font-bold">Masuk</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Admin Sidebar Simulation -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">3. Pratinjau Sidebar Admin:</span>
                    <span class="text-[10px] font-mono text-teal-600 font-bold">Real-time</span>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5 space-y-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-700 via-brand-600 to-teal-500 text-white font-extrabold text-xs flex items-center justify-center shadow-md">
                            1
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-slate-900 tracking-tight text-sm leading-none" x-text="appName || 'LMS KAB Berau'"></span>
                                <span class="px-1 py-0.2 rounded text-[8px] font-mono font-black bg-brand-50 text-brand-700 border border-brand-200/80" x-text="badgeText || 'PRO'"></span>
                            </div>
                            <p class="text-[9px] font-mono font-medium text-slate-400 mt-0.5 uppercase tracking-wider">Management Console</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
