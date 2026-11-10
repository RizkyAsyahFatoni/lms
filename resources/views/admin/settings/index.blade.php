@extends('layouts.admin')

@section('page_title', 'Pengaturan LMS & Identitas')

@section('content')
<div class="space-y-6"
     x-data="{
         activeSettingTab: '{{ request('tab', 'identity') }}',

         // Identity state
         appName: '{{ addslashes($setting->app_name) }}',
         tagline: '{{ addslashes($setting->tagline) }}',
         badgeText: '{{ addslashes($setting->badge_text ?? 'PRO') }}',
         logoPreview: '{{ $setting->logo_url ? $setting->logo_url : asset('images/logo.svg') }}',
         faviconPreview: '{{ $setting->favicon_url ? $setting->favicon_url : asset('favicon.svg') }}',

         // Footer state
         footerDesc: '{{ addslashes($setting->footer_description ?? '') }}',
         footerAddress: '{{ addslashes($setting->footer_address ?? '') }}',
         contactEmail: '{{ addslashes($setting->contact_email ?? 'bkpsdm@beraukab.go.id') }}',
         contactPhone: '{{ addslashes($setting->contact_phone ?? '+62 812-3456-7890') }}',
         footerCopyright: '{{ addslashes($setting->footer_copyright ?? '') }}',

         // JP ASN state
         jpTarget: {{ $setting->jp_default_target ?? 20 }},
         jpMonth: {{ $setting->jp_year_start_month ?? 1 }},

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
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white flex items-center justify-center font-black shadow-sm text-lg">
                    ⚙️
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pengaturan LMS & Identitas Sistem</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola identitas brand, logo, favicon, informasi footer, dan konfigurasi kewajiban JP ASN.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" 
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition flex items-center gap-2">
                <span>Lihat Beranda Publik</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>

            <!-- Dynamic Save Button -->
            <template x-if="activeSettingTab === 'identity'">
                <button type="submit" form="identityForm" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                    <span>Simpan Identitas & Logo</span>
                </button>
            </template>
            <template x-if="activeSettingTab === 'footer'">
                <button type="submit" form="footerForm" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                    <span>Simpan Pengaturan Footer</span>
                </button>
            </template>
            <template x-if="activeSettingTab === 'jp'">
                <button type="submit" form="jpForm" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                    <span>Simpan Pengaturan JP ASN</span>
                </button>
            </template>
            <template x-if="activeSettingTab === 'layout'">
                <button type="submit" form="layoutForm" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                    <span>Simpan Tata Letak Grid</span>
                </button>
            </template>
        </div>
    </div>

    <!-- Top Tab Navigation Bar (v0 Precision Style) -->
    <div class="bg-white p-1.5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-1 overflow-x-auto">
        <!-- Tab 1: Identitas & Logo LMS -->
        <button @click="activeSettingTab = 'identity'" 
                :class="activeSettingTab === 'identity' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
            <span class="text-sm">🎨</span>
            <span>Identitas & Logo LMS</span>
            <span :class="activeSettingTab === 'identity' ? 'bg-slate-800 text-teal-300' : 'bg-slate-100 text-slate-500'" 
                  class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold">
                BRAND
            </span>
        </button>

        <!-- Tab 2: Tampilan & Grid Beranda -->
        <button @click="activeSettingTab = 'layout'" 
                :class="activeSettingTab === 'layout' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
            <span class="text-sm">📐</span>
            <span>Tampilan & Grid Beranda</span>
            <span :class="activeSettingTab === 'layout' ? 'bg-slate-800 text-teal-300' : 'bg-slate-100 text-slate-500'" 
                  class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold uppercase">
                {{ $setting->featured_grid_layout ?? '2x4' }}
            </span>
        </button>

        <!-- Tab 3: Footer & Kontak -->
        <button @click="activeSettingTab = 'footer'" 
                :class="activeSettingTab === 'footer' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
            <span class="text-sm">🏢</span>
            <span>Footer & Kontak</span>
            <span :class="activeSettingTab === 'footer' ? 'bg-slate-800 text-teal-300' : 'bg-slate-100 text-slate-500'" 
                  class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold">
                PORTAL
            </span>
        </button>

        <!-- Tab 4: Pengaturan JP ASN -->
        <button @click="activeSettingTab = 'jp'" 
                :class="activeSettingTab === 'jp' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
            <span class="text-sm">⚖️</span>
            <span>Pengaturan JP Pegawai</span>
            <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-brand-50 text-brand-700 border border-brand-200"
                  x-text="jpTarget + ' JP'"></span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: IDENTITAS & LOGO LMS -->
    <!-- ========================================================================= -->
    <div x-show="activeSettingTab === 'identity'" class="space-y-6">

        <!-- Overview Status Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-500">Nama Aplikasi Aktif</span>
                <p class="text-lg font-black text-slate-900 mt-0.5 truncate" x-text="appName || 'LMS KAB Berau'"></p>
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
                
                <form id="identityForm" action="{{ route('admin.settings.identity.update') }}" method="POST" enctype="multipart/form-data" 
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
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-medium transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Badge</label>
                            <input type="text" name="badge_text" x-model="badgeText" placeholder="PRO"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono font-bold transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tagline / Slogan Subjudul</label>
                        <input type="text" name="tagline" x-model="tagline" placeholder="Ekosistem Pembelajaran Digital Terintegrasi"
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
                                <button type="submit" formaction="{{ route('admin.settings.reset-logo') }}" 
                                        class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 hover:underline">
                                    ↺ Reset ke Logo Bawaan
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
                                <button type="submit" formaction="{{ route('admin.settings.reset-favicon') }}" 
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

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all hover:shadow">
                            Simpan Pengaturan Identitas
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
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5 px-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-rose-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                            </div>

                            <div class="flex-1 bg-white rounded-lg px-3 py-1.5 flex items-center gap-2 shadow-2xs border border-slate-200/60 min-w-0">
                                <img :src="faviconPreview" class="w-4 h-4 object-contain shrink-0" alt="Tab Icon">
                                <span class="text-xs font-semibold text-slate-700 truncate" x-text="(appName || 'LMS KAB Berau') + ' | ' + (tagline || 'Ekosistem Pembelajaran')"></span>
                                <span class="text-[10px] text-slate-400 ml-auto">✕</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg px-3 py-1 text-[11px] text-slate-400 font-mono flex items-center gap-2 border border-slate-200/60">
                            <span>🔒</span>
                            <span class="text-slate-600 truncate">https://lms.beraukab.go.id/</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Navbar Header Preview -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">2. Pratinjau Header Navbar Pengunjung:</span>
                        <span class="text-[10px] font-mono text-teal-600 font-bold">Real-time</span>
                    </div>

                    <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-2xs flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <img :src="logoPreview" class="h-7 w-auto object-contain" alt="Logo">
                            <span class="text-xs font-extrabold text-slate-900 tracking-tight" x-text="appName || 'LMS KAB Berau'"></span>
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-semibold text-slate-600">
                            <span>Beranda</span>
                            <span>Katalog</span>
                            <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-bold">Masuk</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Admin Sidebar Header Preview -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">3. Pratinjau Sidebar Admin:</span>
                        <span class="text-[10px] font-mono text-teal-600 font-bold">Real-time</span>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                            1
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-slate-900 text-xs truncate" x-text="appName || 'LMS KAB Berau'"></span>
                                <span class="px-1 py-0.2 rounded text-[8px] font-mono font-bold bg-brand-50 text-brand-700 border border-brand-200" x-text="badgeText || 'PRO'"></span>
                            </div>
                            <p class="text-[9px] font-mono text-slate-400 uppercase tracking-wider">Management Console</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: FOOTER & KONTAK -->
    <!-- ========================================================================= -->
    <div x-show="activeSettingTab === 'footer'" x-cloak class="space-y-6">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-500">Email Support Publik</span>
                <p class="text-base font-black text-slate-900 mt-0.5 truncate" x-text="contactEmail || 'Belum diatur'"></p>
                <span class="text-[10px] font-mono text-emerald-600 font-semibold">● Aktif di Footer</span>
            </div>

            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-500">Kontak WhatsApp / Telp</span>
                <p class="text-base font-black text-slate-900 mt-0.5 truncate" x-text="contactPhone || 'Belum diatur'"></p>
                <span class="text-[10px] font-mono text-brand-600 font-semibold">● Terhubung Langsung</span>
            </div>

            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-500">Teks Hak Cipta</span>
                <p class="text-base font-black text-slate-900 mt-0.5 truncate" x-text="footerCopyright || '© ' + new Date().getFullYear() + ' ' + appName"></p>
                <span class="text-[10px] font-mono text-slate-500">Otomatis Terbit</span>
            </div>
        </div>

        <form id="footerForm" action="{{ route('admin.settings.footer.update') }}" method="POST" 
              class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-6">
            @csrf

            <!-- Section: Info Footer & Kontak -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Informasi Publik & Kontak Instansi</h3>
                    <p class="text-xs text-slate-500">Data yang tampil pada kolom informasi footer di seluruh halaman beranda dan kursus.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Support Resmi</label>
                        <input type="email" name="contact_email" x-model="contactEmail" placeholder="support@instansi.go.id"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor Telepon / WhatsApp Helpdesk</label>
                        <input type="text" name="contact_phone" x-model="contactPhone" placeholder="+62 812-xxxx-xxxx"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Ringkas Footer</label>
                    <textarea name="footer_description" rows="3" x-model="footerDesc" placeholder="Platform pembelajaran dan pengembangan kompetensi aparatur terpadu..."
                              class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat Kantor / Instansi</label>
                    <textarea name="footer_address" rows="2" x-model="footerAddress" placeholder="Jl. Pahlawan No. 1, Gedung Diklat Lantai 3..."
                              class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Hak Cipta (Copyright)</label>
                    <input type="text" name="footer_copyright" x-model="footerCopyright" placeholder="Contoh: © {{ date('Y') }} Pemerintah Kabupaten Berau - BKPSDM. Hak Cipta Dilindungi."
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <!-- Section: Media Sosial Links -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Tautan Jejaring Sosial Media</h3>
                    <p class="text-xs text-slate-500">Ikon sosial media akan otomatis tampil pada bagian bawah footer jika tautan diisi.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Instagram</label>
                        <input type="url" name="social_instagram" value="{{ $setting->social_instagram }}" placeholder="https://instagram.com/..."
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">YouTube</label>
                        <input type="url" name="social_youtube" value="{{ $setting->social_youtube }}" placeholder="https://youtube.com/@..."
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Facebook</label>
                        <input type="url" name="social_facebook" value="{{ $setting->social_facebook }}" placeholder="https://facebook.com/..."
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">TikTok</label>
                        <input type="url" name="social_tiktok" value="{{ $setting->social_tiktok }}" placeholder="https://tiktok.com/@..."
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">X / Twitter</label>
                        <input type="url" name="social_twitter" value="{{ $setting->social_twitter }}" placeholder="https://x.com/..."
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">LinkedIn</label>
                        <input type="url" name="social_linkedin" value="{{ $setting->social_linkedin }}" placeholder="https://linkedin.com/company/..."
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all hover:shadow">
                    Simpan Pengaturan Footer
                </button>
            </div>
        </form>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: PENGATURAN JP ASN (RINGKAS & ELEGAN) -->
    <!-- ========================================================================= -->
    <div x-show="activeSettingTab === 'jp'" x-cloak class="space-y-6">

        <!-- Quick ASN JP Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-500">Target Default Terpasang</span>
                <p class="text-2xl font-black text-brand-700 mt-1" x-text="jpTarget + ' JP / Tahun'"></p>
                <span class="text-[10px] font-mono text-slate-400">Kewajiban Jam Pelajaran Tahunan</span>
            </div>

            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-500">Siklus Perhitungan Aktif</span>
                <p class="text-lg font-black text-slate-900 mt-1" x-text="jpMonth == 1 ? '1 Jan - 31 Des' : '1 Apr - 31 Mar'"></p>
                <span class="text-[10px] font-mono text-emerald-600 font-semibold" x-text="jpMonth == 1 ? '● Tahun Kalender Masehi' : '● Tahun Anggaran APBD/APBN'"></span>
            </div>

            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-500">Peserta ASN Terhubung NIP</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalAsn) }} <span class="text-xs font-bold text-slate-400">Pegawai</span></p>
                <span class="text-[10px] font-mono text-indigo-600 font-semibold">Tercatat di Sistem LMS</span>
            </div>
        </div>

        <!-- Compact Form Settings -->
        <form id="jpForm" action="{{ route('admin.settings.jp.update') }}" method="POST" 
              class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-6">
            @csrf

            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Standarisasi Beban Jam Pelajaran (JP) ASN</h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">ASN Regulatory Standard</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Sesuai amanat UU No. 20 Tahun 2023 dan Peraturan LAN RI tentang pengembangan kompetensi pegawai sekurang-kurangnya 20 atau 24 JP per tahun.</p>
            </div>

            <!-- Option 1: Target Selector Pills -->
            <div class="space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Pilih Target JP Default Tahunan</label>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition"
                           :class="jpTarget == 20 ? 'bg-brand-50/50 border-brand-500 ring-2 ring-brand-500/20' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300'">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-black text-slate-900">20 JP / Tahun</span>
                            <input type="radio" name="jp_default_target" value="20" x-model="jpTarget" class="text-brand-600 focus:ring-brand-500">
                        </div>
                        <span class="text-[11px] text-slate-500 mt-1">Standar Nasional LAN RI & BKN untuk seluruh jenjang jabatan.</span>
                        <span class="mt-2 text-[10px] font-mono font-bold text-brand-700">Paling Umum Digunakan</span>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition"
                           :class="jpTarget == 24 ? 'bg-brand-50/50 border-brand-500 ring-2 ring-brand-500/20' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300'">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-black text-slate-900">24 JP / Tahun</span>
                            <input type="radio" name="jp_default_target" value="24" x-model="jpTarget" class="text-brand-600 focus:ring-brand-500">
                        </div>
                        <span class="text-[11px] text-slate-500 mt-1">Standar PermenpanRB / instansi dengan target kompetensi khusus.</span>
                        <span class="mt-2 text-[10px] font-mono font-bold text-teal-700">Standar Lanjutan</span>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition"
                           :class="jpTarget == 30 ? 'bg-brand-50/50 border-brand-500 ring-2 ring-brand-500/20' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300'">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-black text-slate-900">30 JP / Tahun</span>
                            <input type="radio" name="jp_default_target" value="30" x-model="jpTarget" class="text-brand-600 focus:ring-brand-500">
                        </div>
                        <span class="text-[11px] text-slate-500 mt-1">Target akselerasi indeks profesionalitas ASN daerah berprestasi.</span>
                        <span class="mt-2 text-[10px] font-mono font-bold text-purple-700">Target Khusus</span>
                    </label>
                </div>
            </div>

            <!-- Option 2: Fiscal Cycle Selector -->
            <div class="space-y-3 pt-3 border-t border-slate-100">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Siklus Periode Perhitungan Akumulasi JP</label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition"
                           :class="jpMonth == 1 ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 border-slate-200 hover:border-slate-300 text-slate-700'">
                        <input type="radio" name="jp_year_start_month" value="1" x-model="jpMonth" class="mt-0.5 text-brand-500 focus:ring-brand-500">
                        <div>
                            <span class="font-bold text-xs block">Tahun Kalender Masehi (1 Januari - 31 Desember)</span>
                            <span class="text-[11px] opacity-80 mt-0.5 block">Akumulasi dihitung per tahun masehi berjalan sesuai standar SKP e-Kinerja tahunan.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition"
                           :class="jpMonth == 4 ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 border-slate-200 hover:border-slate-300 text-slate-700'">
                        <input type="radio" name="jp_year_start_month" value="4" x-model="jpMonth" class="mt-0.5 text-brand-500 focus:ring-brand-500">
                        <div>
                            <span class="font-bold text-xs block">Tahun Anggaran Fiskal (1 April - 31 Maret)</span>
                            <span class="text-[11px] opacity-80 mt-0.5 block">Akumulasi mengikuti siklus penganggaran APBD / DPA pelatihan dinas daerah.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Integration Notes -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 flex items-start gap-3">
                <div class="text-base shrink-0">ℹ️</div>
                <div class="space-y-1">
                    <p class="font-bold text-slate-800">Catatan Integrasi JP:</p>
                    <p class="text-[11px] leading-relaxed">
                        Akumulasi JP pada profil peserta secara otomatis menggabungkan <strong>JP kursus internal LMS KAB Berau</strong> yang telah tuntas 100% dan <strong>JP pelatihan eksternal</strong> yang telah disetujui (approved) oleh administrator melalui menu verifikasi JP.
                    </p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all hover:shadow">
                    Simpan Pengaturan JP ASN
                </button>
            </div>
        </form>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: TAMPILAN & GRID BERANDA -->
    <!-- ========================================================================= -->
    <div x-show="activeSettingTab === 'layout'" x-cloak class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
            <div class="max-w-3xl">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-600"></span>
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Format Grid Program & Inisiatif Pelatihan Unggulan</h2>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-brand-50 text-brand-700 uppercase">
                        Aktif: {{ ($setting->featured_grid_layout ?? '2x4') === '2x3' ? '2x3 (3 Kolom)' : '2x4 (4 Kolom)' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-6">
                    Pilih tata letak kartu kursus unggulan yang ditampilkan di bagian utama beranda frontend LMS KAB Berau. Pengaturan ini langsung berlaku seketika pada halaman publik.
                </p>

                <form id="layoutForm" action="{{ route('admin.settings.layout.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Option 2x4 -->
                        <label class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition {{ ($setting->featured_grid_layout ?? '2x4') === '2x4' ? 'border-brand-600 bg-brand-50/20 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="font-extrabold text-sm text-slate-900 block">Format Grid 2 × 4 (8 Card)</span>
                                    <span class="text-[10px] text-slate-400">Rekomendasi Desktop & Monitor Standar</span>
                                </div>
                                <input type="radio" name="featured_grid_layout" value="2x4" class="w-4 h-4 text-brand-600 focus:ring-brand-500" {{ ($setting->featured_grid_layout ?? '2x4') === '2x4' ? 'checked' : '' }}>
                            </div>
                            
                            <!-- Visual Graphic 4 Columns -->
                            <div class="space-y-1.5 p-3 bg-slate-100 rounded-xl mb-3 border border-slate-200/60">
                                <div class="grid grid-cols-4 gap-1.5">
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 1</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 2</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 3</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 4</div>
                                </div>
                                <div class="grid grid-cols-4 gap-1.5">
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 5</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 6</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 7</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 8</div>
                                </div>
                            </div>
                            
                            <p class="text-[11px] text-slate-500 leading-relaxed mt-auto">
                                Menampilkan 4 kartu per baris (total hingga 8 kursus). Tampilan ringkas, padat, dan menyajikan lebih banyak ragam kompetensi dalam satu layar.
                            </p>
                        </label>

                        <!-- Option 2x3 -->
                        <label class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition {{ ($setting->featured_grid_layout ?? '2x4') === '2x3' ? 'border-brand-600 bg-brand-50/20 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="font-extrabold text-sm text-slate-900 block">Format Grid 2 × 3 (6 Card)</span>
                                    <span class="text-[10px] text-slate-400">Fokus Visual Luas & Thumbnail Besar</span>
                                </div>
                                <input type="radio" name="featured_grid_layout" value="2x3" class="w-4 h-4 text-brand-600 focus:ring-brand-500" {{ ($setting->featured_grid_layout ?? '2x4') === '2x3' ? 'checked' : '' }}>
                            </div>
                            
                            <!-- Visual Graphic 3 Columns -->
                            <div class="space-y-1.5 p-3 bg-slate-100 rounded-xl mb-3 border border-slate-200/60">
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 1</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 2</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 3</div>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 4</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 5</div>
                                    <div class="h-8 bg-slate-300 rounded-md flex items-center justify-center text-[8px] font-bold text-slate-500">Card 6</div>
                                </div>
                            </div>
                            
                            <p class="text-[11px] text-slate-500 leading-relaxed mt-auto">
                                Menampilkan 3 kartu per baris (total hingga 6 kursus). Ukuran kartu lebih lebar, tulisan judul lebih lega, dan thumbnail pelatihan lebih menonjol.
                            </p>
                        </label>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 flex items-start gap-3">
                        <div class="text-base shrink-0">💡</div>
                        <p class="text-[11px] leading-relaxed">
                            Jika terdapat lebih banyak pelatihan unggulan aktif yang melebihi jumlah kolom grid, peserta dapat mengklik tombol <strong>"Lihat Semua Katalog Pelatihan"</strong> di bagian bawah section beranda.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all hover:shadow">
                            Simpan Tata Letak Grid Beranda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
