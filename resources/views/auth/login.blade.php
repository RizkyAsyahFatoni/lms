@extends('layouts.app')

@section('content')
<div class="min-h-[85vh] py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_60%_50%_at_50%_20%,rgba(20,184,166,0.12),transparent)] pointer-events-none"></div>

    <div class="max-w-5xl mx-auto space-y-8 relative z-10">
        
        <!-- Header -->
        <div class="text-center max-w-xl mx-auto space-y-2">
            @if($appSetting && $appSetting->logo_path)
                <img src="{{ $appSetting->logo_url }}" class="h-12 w-auto mx-auto object-contain" alt="{{ $appSetting->app_name }}">
            @else
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-700 via-brand-600 to-teal-500 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-md shadow-brand-600/20">
                    1
                </div>
            @endif
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Portal Masuk {{ $appSetting->app_name ?? 'LMS KAB Berau' }}</h1>
            <p class="text-xs sm:text-sm text-slate-500">Pilih jalur autentikasi sesuai profil kepesertaan Anda untuk mengakses pembelajaran dan sertifikat.</p>
        </div>

        @php 
            $ssoSetting = \App\Models\SsoSetting::getSettings(); 
            $googleSetting = \App\Models\GoogleSetting::getSettings();
        @endphp

        <!-- Mobile Segmented Tabs Switcher (Visible on mobile, hidden on lg) -->
        <div class="lg:hidden flex p-1 bg-slate-200/80 rounded-2xl max-w-md mx-auto" x-data="{ mobileTab: 'asn' }">
            <button @click="mobileTab = 'asn'" :class="mobileTab === 'asn' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" class="flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                <span>🏛️</span>
                <span>Jalur ASN Daerah</span>
            </button>
            <button @click="mobileTab = 'general'" :class="mobileTab === 'general' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" class="flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                <span>👥</span>
                <span>Peserta Umum</span>
            </button>
        </div>

        <!-- Dual Login Grid (Side-by-side on desktop, stacked with tab control on mobile) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch" x-data="{ mobileTab: 'asn' }">

            <!-- ======================================================== -->
            <!-- PANEL 1: JALUR KHUSUS ASN DAERAH (SINGLE SIGN-ON) -->
            <!-- ======================================================== -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white rounded-3xl p-6 sm:p-8 border border-slate-700/60 shadow-xl flex flex-col justify-between relative overflow-hidden"
                 :class="{ 'hidden lg:flex': mobileTab !== 'asn', 'flex': mobileTab === 'asn' }">
                
                <!-- Background decoration -->
                <div class="absolute -top-24 -right-24 w-60 h-60 rounded-full bg-brand-500/10 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-60 h-60 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>

                <div class="space-y-6 relative z-10">
                    <!-- Badge & Title -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-brand-500/20 text-brand-300 border border-brand-400/30">
                                Jalur Khusus ASN
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                SSO Ready
                            </span>
                        </div>
                        <h2 class="text-xl font-black text-white tracking-tight">Single Sign-On (SSO) Kepegawaian</h2>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Akses khusus bagi Pegawai ASN, PPPK, dan Pejabat Pemerintah Daerah yang telah terintegrasi dengan sistem kepegawaian.
                        </p>
                    </div>

                    <!-- ASN Feature List -->
                    <div class="space-y-2.5 bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm text-xs">
                        <div class="flex items-start gap-2.5 text-slate-200">
                            <svg class="w-4 h-4 text-brand-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Otomatis sinkronisasi <strong>NIP, NIK, & Unit Kerja</strong></span>
                        </div>
                        <div class="flex items-start gap-2.5 text-slate-200">
                            <svg class="w-4 h-4 text-teal-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Akumulasi otomatis <strong>target 20-24 Jam Pelajaran (JP)</strong> tahunan</span>
                        </div>
                        <div class="flex items-start gap-2.5 text-slate-200">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Dukungan verifikasi sertifikat keabsahan LAN / BKN</span>
                        </div>
                    </div>

                    <!-- Integration Specs pill -->
                    <div class="flex items-center gap-1.5 flex-wrap text-[10px] text-slate-400 font-mono">
                        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/10">SAML 2.0</span>
                        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/10">OAuth 2.0 / OIDC</span>
                        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/10">SIMPEG Daerah</span>
                        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/10">BKD / BKN Ready</span>
                    </div>
                </div>

                <!-- ASN Login Actions -->
                <div class="space-y-3 pt-6 mt-6 border-t border-white/10 relative z-10">
                    <a href="{{ route('auth.sso.redirect') }}" 
                       class="w-full flex items-center justify-center gap-3 px-5 py-3.5 rounded-2xl text-xs font-bold text-slate-950 bg-gradient-to-r from-brand-300 via-teal-300 to-emerald-300 hover:from-brand-200 hover:to-emerald-200 shadow-lg shadow-brand-500/20 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>{{ $ssoSetting->button_label ?? 'Masuk via SSO ASN Daerah / SIMPEG' }}</span>
                    </a>

                    @if(app()->environment('local', 'testing'))
                    <!-- Mock SSO Login for Local Demo -->
                    <form action="{{ route('auth.sso.mock') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-[11px] font-mono font-medium text-brand-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition shadow-2xs cursor-pointer">
                            <span>⚡ Simulasi Masuk SSO ASN (Demo SIMPEG)</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PANEL 2: JALUR PESERTA UMUM / LUAR DAERAH -->
            <!-- ======================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between"
                 :class="{ 'hidden lg:flex': mobileTab !== 'general', 'flex': mobileTab === 'general' }">
                
                <div class="space-y-5">
                    <!-- Badge & Title -->
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                Jalur Umum & Mandiri
                            </span>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Login Peserta Umum</h2>
                        <p class="text-xs text-slate-500">
                            Untuk masyarakat umum, akademisi, profesional swasta, atau peserta dari luar daerah.
                        </p>
                    </div>

                    <!-- Google 1-Click Login -->
                    @if($googleSetting && $googleSetting->is_active)
                        <a href="{{ route('auth.google.redirect') }}" 
                           class="w-full flex items-center justify-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-2xs hover:border-slate-300 hover:shadow-xs transition-all duration-150">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>{{ $googleSetting->button_label ?? 'Masuk dengan Akun Google' }}</span>
                        </a>

                        <div class="relative flex items-center justify-center pt-1">
                            <div class="border-t border-slate-200 w-full"></div>
                            <span class="bg-white px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 shrink-0">atau masuk dengan email</span>
                        </div>
                    @endif

                    <!-- Standard Email & Password Form -->
                    <form action="{{ route('login') }}" method="POST" class="space-y-3.5">
                        @csrf

                        <div>

                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="nama@email.com"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition @error('email') border-red-400 bg-red-50 @enderror">
                            @error('email')
                                <p class="text-[11px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="password" class="block text-xs font-semibold text-slate-700">Kata Sandi</label>
                                <a href="#" class="text-[11px] text-brand-600 hover:text-brand-700 font-medium">Lupa sandi?</a>
                            </div>
                            <input type="password" id="password" name="password" required
                                   placeholder="••••••••"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition @error('password') border-red-400 bg-red-50 @enderror">
                            @error('password')
                                <p class="text-[11px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                            <label for="remember" class="ml-2 block text-xs text-slate-600 font-medium">Ingat saya di perangkat ini</label>
                        </div>

                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                            <span>Masuk ke Akun Umum</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>

                    <!-- Quick Autofill Pills for General Form -->
                    <div class="pt-2 border-t border-slate-100/80">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1.5">
                            <span class="font-medium">Isi otomatis cepat:</span>
                            <span class="text-[10px] font-mono text-slate-400">klik untuk mengisi & masuk</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5">
                            <button type="button" 
                                    onclick="quickSubmit('admin@example.com', 'password')" 
                                    class="px-2.5 py-1.5 text-[11px] font-semibold rounded-xl bg-slate-50 hover:bg-purple-50 text-slate-700 hover:text-purple-800 border border-slate-200/80 hover:border-purple-300 transition flex items-center justify-between group cursor-pointer">
                                <span>👑 Admin</span>
                                <span class="text-[9px] text-slate-400 group-hover:text-purple-600 font-mono">1-Klik &rarr;</span>
                            </button>
                            <button type="button" 
                                    onclick="quickSubmit('budi@example.com', 'password')" 
                                    class="px-2.5 py-1.5 text-[11px] font-semibold rounded-xl bg-slate-50 hover:bg-teal-50 text-slate-700 hover:text-teal-800 border border-slate-200/80 hover:border-teal-300 transition flex items-center justify-between group cursor-pointer">
                                <span>👤 User (ASN)</span>
                                <span class="text-[9px] text-slate-400 group-hover:text-teal-600 font-mono">1-Klik &rarr;</span>
                            </button>
                            <button type="button" 
                                    onclick="quickSubmit('narasumber@berau.go.id', 'password')" 
                                    class="px-2.5 py-1.5 text-[11px] font-semibold rounded-xl bg-slate-50 hover:bg-amber-50 text-slate-700 hover:text-amber-800 border border-slate-200/80 hover:border-amber-300 transition flex items-center justify-between group cursor-pointer">
                                <span>🎓 Trainer</span>
                                <span class="text-[9px] text-slate-400 group-hover:text-amber-700 font-mono">1-Klik &rarr;</span>
                            </button>
                            <button type="button" 
                                    onclick="quickSubmit('admin.opd@berau.go.id', 'password')" 
                                    class="px-2.5 py-1.5 text-[11px] font-semibold rounded-xl bg-slate-50 hover:bg-sky-50 text-slate-700 hover:text-sky-800 border border-slate-200/80 hover:border-sky-300 transition flex items-center justify-between group cursor-pointer">
                                <span>🏛️ Admin OPD</span>
                                <span class="text-[9px] text-slate-400 group-hover:text-sky-600 font-mono">1-Klik &rarr;</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="pt-5 mt-5 border-t border-slate-100">
                    <p class="text-center text-xs text-slate-500">
                        Belum memiliki akun peserta umum?
                        <a href="{{ route('register') }}" class="font-bold text-brand-700 hover:text-brand-800">Daftar Akun Baru</a>
                    </p>
                </div>

            </div>

        </div>

        <!-- ======================================================== -->
        <!-- DEMO INSTANT LOGIN (1-KLIK MASUK SESUAI PERAN) -->
        <!-- ======================================================== -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-white flex items-center justify-center font-bold text-base shadow-xs shadow-amber-500/20 shrink-0">
                        ⚡
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-900">Akses Cepat Mode Demo (1-Klik Masuk)</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                FITUR DEMO
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Klik salah satu profil peran di bawah untuk langsung masuk otomatis ke dashboard masing-masing.</p>
                    </div>
                </div>
                <div class="inline-flex items-center gap-1.5 text-[11px] font-mono text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/70 shrink-0 self-start sm:self-auto">
                    <span>Sandi Bawaan:</span>
                    <code class="font-bold text-slate-800 bg-white px-1.5 py-0.5 rounded border border-slate-200">password</code>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- 1. Administrator -->
                <form action="{{ route('login') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="email" value="admin@example.com">
                    <input type="hidden" name="password" value="password">
                    <button type="submit" 
                            class="w-full h-full text-left p-3.5 rounded-2xl bg-slate-50/80 hover:bg-purple-50/70 border border-slate-200/90 hover:border-purple-300 transition-all duration-150 group shadow-2xs hover:shadow-sm flex flex-col justify-between cursor-pointer">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-base">👑</span>
                                <span class="text-[9px] font-mono font-black uppercase px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 border border-purple-200">
                                    ADMIN
                                </span>
                            </div>
                            <div class="text-xs font-bold text-slate-900 group-hover:text-purple-700 transition">Administrator LMS</div>
                            <div class="text-[11px] font-mono text-slate-500 truncate">admin@example.com</div>
                            <div class="text-[10px] text-slate-400 leading-tight">Super Admin & Pengaturan Master BKPSDM</div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-bold text-purple-600 group-hover:text-purple-700">
                            <span>Masuk Admin</span>
                            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </div>
                    </button>
                </form>

                <!-- 2. Peserta ASN (User) -->
                <form action="{{ route('login') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="email" value="budi@example.com">
                    <input type="hidden" name="password" value="password">
                    <button type="submit" 
                            class="w-full h-full text-left p-3.5 rounded-2xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/90 hover:border-teal-300 transition-all duration-150 group shadow-2xs hover:shadow-sm flex flex-col justify-between cursor-pointer">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-base">👤</span>
                                <span class="text-[9px] font-mono font-black uppercase px-2 py-0.5 rounded-md bg-teal-100 text-teal-700 border border-teal-200">
                                    USER (ASN)
                                </span>
                            </div>
                            <div class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">Budi Santoso, S.Kom</div>
                            <div class="text-[11px] font-mono text-slate-500 truncate">budi@example.com</div>
                            <div class="text-[10px] text-slate-400 leading-tight">Peserta ASN, Tracker 20 JP & Transkrip</div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-bold text-teal-600 group-hover:text-teal-700">
                            <span>Masuk User</span>
                            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </div>
                    </button>
                </form>

                <!-- 3. Trainer / Narasumber -->
                <form action="{{ route('login') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="email" value="narasumber@berau.go.id">
                    <input type="hidden" name="password" value="password">
                    <button type="submit" 
                            class="w-full h-full text-left p-3.5 rounded-2xl bg-slate-50/80 hover:bg-amber-50/70 border border-slate-200/90 hover:border-amber-300 transition-all duration-150 group shadow-2xs hover:shadow-sm flex flex-col justify-between cursor-pointer">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-base">🎓</span>
                                <span class="text-[9px] font-mono font-black uppercase px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">
                                    TRAINER
                                </span>
                            </div>
                            <div class="text-xs font-bold text-slate-900 group-hover:text-amber-800 transition">Fajar Hidayat, M.Kom</div>
                            <div class="text-[11px] font-mono text-slate-500 truncate">narasumber@berau.go.id</div>
                            <div class="text-[10px] text-slate-400 leading-tight">Widyaiswara / Penilaian & Kelas Ampu</div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-bold text-amber-700 group-hover:text-amber-800">
                            <span>Masuk Trainer</span>
                            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </div>
                    </button>
                </form>

                <!-- 4. Admin OPD -->
                <form action="{{ route('login') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="email" value="admin.opd@berau.go.id">
                    <input type="hidden" name="password" value="password">
                    <button type="submit" 
                            class="w-full h-full text-left p-3.5 rounded-2xl bg-slate-50/80 hover:bg-sky-50/70 border border-slate-200/90 hover:border-sky-300 transition-all duration-150 group shadow-2xs hover:shadow-sm flex flex-col justify-between cursor-pointer">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-base">🏛️</span>
                                <span class="text-[9px] font-mono font-black uppercase px-2 py-0.5 rounded-md bg-sky-100 text-sky-700 border border-sky-200">
                                    ADMIN OPD
                                </span>
                            </div>
                            <div class="text-xs font-bold text-slate-900 group-hover:text-sky-700 transition">Hendra Wijaya, S.Kom</div>
                            <div class="text-[11px] font-mono text-slate-500 truncate">admin.opd@berau.go.id</div>
                            <div class="text-[10px] text-slate-400 leading-tight">Diskominfo, Usulan Diklat & Kepatuhan JP</div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-bold text-sky-600 group-hover:text-sky-700">
                            <span>Masuk Admin OPD</span>
                            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>

        <script nonce="{{ $cspNonce ?? '' }}">
            function quickSubmit(email, password) {
                var emailInput = document.getElementById('email');
                var passwordInput = document.getElementById('password');
                if (emailInput && passwordInput) {
                    emailInput.value = email;
                    passwordInput.value = password;
                    var form = emailInput.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            }
        </script>

    </div>
</div>
@endsection
