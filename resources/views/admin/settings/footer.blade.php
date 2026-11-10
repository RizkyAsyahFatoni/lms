@extends('layouts.admin')

@section('page_title', 'Konfigurasi Footer')

@section('content')
<div class="space-y-6"
     x-data="{
         footerDesc: '{{ addslashes($setting->footer_description ?? '') }}',
         footerAddress: '{{ addslashes($setting->footer_address ?? '') }}',
         contactEmail: '{{ addslashes($setting->contact_email ?? 'bkpsdm@beraukab.go.id') }}',
         contactPhone: '{{ addslashes($setting->contact_phone ?? '(0554) 21064') }}',
         footerCopyright: '{{ addslashes($setting->footer_copyright ?? '') }}'
     }">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white flex items-center justify-center font-black shadow-sm text-lg">
                    🏢
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Konfigurasi & Setup Footer</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Atur deskripsi ringkas, informasi kantor, kontak support, dan tautan sosial media resmi.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" 
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition flex items-center gap-2">
                <span>Lihat Footer Beranda</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <button type="submit" form="footerForm" 
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                <span>Simpan Pengaturan Footer</span>
            </button>
        </div>
    </div>

    <!-- Quick Status Cards -->
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
            <span class="text-[11px] font-medium text-slate-500">Jejaring Sosial Media</span>
            <p class="text-base font-black text-slate-900 mt-0.5">
                @php
                    $socialCount = 0;
                    if($setting->social_facebook) $socialCount++;
                    if($setting->social_instagram) $socialCount++;
                    if($setting->social_youtube) $socialCount++;
                    if($setting->social_tiktok) $socialCount++;
                    if($setting->social_twitter) $socialCount++;
                    if($setting->social_linkedin) $socialCount++;
                @endphp
                {{ $socialCount }} Akun Terhubung
            </p>
            <span class="text-[10px] font-mono text-slate-500 font-semibold">Ikon Tampil di Footer</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Form Configuration (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <form id="footerForm" action="{{ route('admin.footer.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Section 1: Informasi Dasar & Deskripsi -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/90 shadow-2xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900">Deskripsi & Profil Singkat Platform</h2>
                        <p class="text-xs text-slate-500">Teks ini muncul di kolom pertama footer website untuk memberikan gambaran platform.</p>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Deskripsi Ringkas Footer</label>
                            <textarea name="footer_description" rows="3" x-model="footerDesc"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                      placeholder="Membangun ekosistem belajar digital terbaik untuk pengembangan kompetensi ASN, Profesional, dan Umum di Indonesia.">{{ $setting->footer_description }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Disarankan 1-2 kalimat padat dan informatif.</p>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Alamat Lengkap Kantor / Instansi</label>
                            <textarea name="footer_address" rows="2" x-model="footerAddress"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                      placeholder="Jl. APT. Pranoto No. 1, Tanjung Redeb, Kabupaten Berau, Kalimantan Timur">{{ $setting->footer_address }}</textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Teks Hak Cipta / Copyright</label>
                            <input type="text" name="footer_copyright" x-model="footerCopyright"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="© {{ date('Y') }} Pemerintah Kabupaten Berau - BKPSDM. Seluruh Hak Cipta Dilindungi."
                                   value="{{ $setting->footer_copyright }}">
                            <p class="text-[11px] text-slate-400 mt-1">Muncul di baris paling bawah copyright footer.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Informasi Kontak Support -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/90 shadow-2xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900">Saluran Bantuan & Kontak Support</h2>
                        <p class="text-xs text-slate-500">Email dan nomor WhatsApp yang dapat dihubungi oleh pengunjung atau peserta pelatihan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Email Support / Helpdesk</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm">✉️</span>
                                <input type="email" name="contact_email" x-model="contactEmail"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="bkpsdm@beraukab.go.id"
                                       value="{{ $setting->contact_email }}">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Nomor WhatsApp / Telepon</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm">📱</span>
                                <input type="text" name="contact_phone" x-model="contactPhone"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="+62 812-3456-7890"
                                       value="{{ $setting->contact_phone }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Tautan Sosial Media Resmi -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/90 shadow-2xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900">Tautan Media Sosial Resmi</h2>
                        <p class="text-xs text-slate-500">Masukkan URL lengkap profil sosial media Anda. Ikon akan otomatis tampil di footer.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Instagram URL</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold text-pink-500">IG</span>
                                <input type="url" name="social_instagram"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="https://instagram.com/bkpsdm_berau"
                                       value="{{ $setting->social_instagram }}">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">YouTube URL</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold text-red-500">YT</span>
                                <input type="url" name="social_youtube"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="https://youtube.com/@bkpsdmberau"
                                       value="{{ $setting->social_youtube }}">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">LinkedIn URL</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold text-blue-600">IN</span>
                                <input type="url" name="social_linkedin"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="https://linkedin.com/company/bkpsdm-berau"
                                       value="{{ $setting->social_linkedin }}">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">TikTok URL</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold text-slate-800">TT</span>
                                <input type="url" name="social_tiktok"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="https://tiktok.com/@bkpsdm_berau"
                                       value="{{ $setting->social_tiktok }}">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Twitter / X URL</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold text-slate-900">𝕏</span>
                                <input type="url" name="social_twitter"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="https://x.com/pemkab_berau"
                                       value="{{ $setting->social_twitter }}">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Facebook URL</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold text-blue-600">FB</span>
                                <input type="url" name="social_facebook"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                       placeholder="https://facebook.com/bkpsdmberau"
                                       value="{{ $setting->social_facebook }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        Simpan Semua Pengaturan Footer
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Real-Time Visual Preview Window (5 Cols) -->
        <div class="lg:col-span-5 space-y-4 sticky top-6">
            <div class="p-5 bg-slate-900 text-white rounded-3xl shadow-xl space-y-5 border border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Live Preview Footer</span>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">Real-time</span>
                </div>

                <!-- Preview Footer Content -->
                <div class="space-y-4 text-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-teal-500 flex items-center justify-center text-white font-bold text-xs">1</div>
                            <span class="font-extrabold text-white tracking-tight text-sm">{{ $setting->app_name ?? 'LMS KAB Berau' }}</span>
                        </div>
                        <p class="text-slate-400 text-[11px] leading-relaxed" x-text="footerDesc || 'Deskripsi platform akan muncul di sini...'"></p>
                    </div>

                    <div class="pt-2 border-t border-slate-800/80 space-y-2">
                        <span class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider">Alamat Kantor</span>
                        <p class="text-slate-300 text-[11px] flex items-start gap-1.5">
                            <span class="text-slate-500">📍</span>
                            <span x-text="footerAddress || 'Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan'"></span>
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-800/80 space-y-2">
                        <span class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider">Saluran Kontak</span>
                        <p class="text-slate-300 text-[11px] flex items-center gap-2">
                            <span class="text-slate-500">✉️</span>
                            <span x-text="contactEmail || 'bkpsdm@beraukab.go.id'"></span>
                        </p>
                        <p class="text-slate-300 text-[11px] flex items-center gap-2">
                            <span class="text-slate-500">📱</span>
                            <span x-text="contactPhone || '+62 812-3456-7890'"></span>
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-800 text-center text-slate-500 text-[10px]">
                        <p x-text="footerCopyright || '© 2026 Pemerintah Kabupaten Berau - BKPSDM.'"></p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-teal-50 rounded-2xl border border-teal-200/80 text-teal-800 text-xs">
                <p class="font-bold mb-1">💡 Tips Footer Profesional</p>
                <p class="text-teal-700 leading-relaxed text-[11px]">
                    Pastikan nomor WhatsApp dan email support selalu aktif untuk meningkatkan kepercayaan calon peserta dan instansi yang ingin bekerja sama.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
