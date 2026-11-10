@extends('layouts.admin')

@section('page_title', 'Integrasi REST API SIMPEG Kabupaten')

@section('content')
<div class="space-y-6" x-data="{
    showToken: false,
    mappingModal: false
}">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Integrasi REST API SIMPEG Kabupaten</h1>
                @if($setting->is_active)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aktif Terhubung
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                        Siap Pakai (Standby)
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Konektor otomatisasi penarikan data ASN dari SIMPEG BKPSDM Kabupaten via REST API tanpa perlu merombak coding aplikasi.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Test Connection Button -->
            <form action="{{ route('admin.simpeg.test') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                    <span>🔌</span>
                    <span>Uji Koneksi (Ping)</span>
                </button>
            </form>

            <!-- Sync All Button -->
            <form action="{{ route('admin.simpeg.sync-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menarik dan menyinkronkan seluruh data ASN dari SIMPEG sekarang?')">
                @csrf
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                    <span>🔄</span>
                    <span>Tarik Semua ASN (Sync)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('test_success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-300 text-emerald-900 text-xs shadow-2xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <span>🎉</span>
                <span>Uji Koneksi Berhasil!</span>
            </div>
            <p class="font-mono text-[11px]">{{ session('test_success') }}</p>
        </div>
    @endif

    @if(session('test_error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-300 text-rose-900 text-xs shadow-2xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <span>❌</span>
                <span>Uji Koneksi Gagal!</span>
            </div>
            <p class="font-mono text-[11px]">{{ session('test_error') }}</p>
        </div>
    @endif

    <!-- Metric Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                👥
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Total ASN Terdaftar</div>
                <div class="text-xl font-black text-slate-900">{{ number_format($totalAsn) }}</div>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg">
                🏛️
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">OPD Terdata</div>
                <div class="text-xl font-black text-slate-900">{{ number_format($totalOpd) }}</div>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                ⏱️
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Sinkronisasi Terakhir</div>
                <div class="text-xs font-bold text-slate-800">
                    {{ $setting->last_sync_at ? $setting->last_sync_at->diffForHumans() : 'Belum pernah' }}
                </div>
                <div class="text-[10px] text-slate-400 truncate max-w-[200px]">
                    {{ $setting->last_sync_message ?: 'Menunggu instruksi' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Configuration Form -->
    <form action="{{ route('admin.simpeg.update') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Endpoint & Authentication -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Server & Authentication Box -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">1. Server & Autentikasi REST API</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Atur URL base API SIMPEG dan kredensial keamanan.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $setting->is_active ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-2 text-xs font-bold text-slate-700">Aktifkan API</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">Nama Layanan / Provider <span class="text-red-500">*</span></label>
                            <input type="text" name="provider_name" value="{{ old('provider_name', $setting->provider_name) }}" required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">Base URL Server REST API SIMPEG <span class="text-red-500">*</span></label>
                            <input type="url" name="server_url" value="{{ old('server_url', $setting->server_url) }}" required placeholder="https://simpeg.namakabupaten.go.id/api/v1"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <p class="text-[10px] text-slate-400 mt-1">Masukkan URL dasar dari API gateway SIMPEG BKPSDM.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Metode Autentikasi <span class="text-red-500">*</span></label>
                            <select name="auth_type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                                <option value="bearer" {{ $setting->auth_type === 'bearer' ? 'selected' : '' }}>Bearer Token (Standard JWT)</option>
                                <option value="api_key_header" {{ $setting->auth_type === 'api_key_header' ? 'selected' : '' }}>Custom Header (X-API-KEY / Api-Key)</option>
                                <option value="basic" {{ $setting->auth_type === 'basic' ? 'selected' : '' }}>Basic Auth (username:password)</option>
                                <option value="none" {{ $setting->auth_type === 'none' ? 'selected' : '' }}>Tanpa Autentikasi (Jaringan Internal / IP Whitelist)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Header Kustom (Opsional)</label>
                            <input type="text" name="header_name" value="{{ old('header_name', $setting->header_name) }}" placeholder="Authorization / X-API-KEY"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">API Key / Secret Token</label>
                            <div class="relative">
                                <input :type="showToken ? 'text' : 'password'" name="api_key" value="{{ old('api_key', $setting->api_key) }}" placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition pr-10">
                                <button type="button" @click="showToken = !showToken" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                                    <span x-show="!showToken">👁️</span>
                                    <span x-show="showToken">🙈</span>
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Token rahasia yang diberikan oleh tim pengelola SIMPEG BKPSDM.</p>
                        </div>
                    </div>
                </div>

                <!-- Endpoint Routes Box -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-slate-900 text-sm">2. Endpoint Rute API SIMPEG</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tentukan endpoint URI penarikan data pegawai.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Endpoint Tarik Semua Pegawai <span class="text-red-500">*</span></label>
                            <input type="text" name="endpoint_all" value="{{ old('endpoint_all', $setting->endpoint_all) }}" required placeholder="/pegawai atau /asn"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <p class="text-[10px] text-slate-400 mt-1">Endpoint untuk mendapatkan koleksi seluruh data ASN.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Endpoint Tarik 1 Pegawai (per NIP) <span class="text-red-500">*</span></label>
                            <input type="text" name="endpoint_single" value="{{ old('endpoint_single', $setting->endpoint_single) }}" required placeholder="/pegawai/{nip}"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <p class="text-[10px] text-slate-400 mt-1">Gunakan tag <code>{nip}</code> sebagai placeholder NIP dinamis.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Pembungkus Response JSON (Wrapper)</label>
                            <input type="text" name="data_wrapper" value="{{ old('data_wrapper', $setting->data_wrapper) }}" placeholder="data / results / kosongkan jika root array"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <p class="text-[10px] text-slate-400 mt-1">Jika response SIMPEG berupa <code>{"status":200, "data":[...]}</code>, isi dengan <code>data</code>.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Password Default ASN Baru</label>
                            <input type="text" name="default_password" value="{{ old('default_password', $setting->default_password) }}" required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <p class="text-[10px] text-slate-400 mt-1">Password bawaan jika akun belum pernah login ke LMS.</p>
                        </div>
                    </div>
                </div>

                <!-- Field Mapping Box (Plug & Play JSON Normalization) -->
                @php
                    $activeMap = $setting->getActiveFieldMapping();
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">3. Pemetaan Kolom JSON (Field Mapping)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Sesuaikan nama atribut JSON yang dikirim oleh SIMPEG ke atribut database LMS tanpa ubah coding.
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-teal-50 text-teal-800 text-[10px] font-bold border border-teal-200">
                            Plug & Play
                        </span>
                    </div>

                    <div class="p-3 bg-brand-50/60 border border-brand-200 rounded-xl text-[11px] text-brand-800 space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <span>💡</span>
                            <span>Fleksibilitas Struktur Data SIMPEG:</span>
                        </div>
                        <p class="leading-relaxed">
                            Jika API SIMPEG Kabupaten menamai field NIP dengan <code>NIP_BARU</code>, nama pegawai dengan <code>NAMA_LENGKAP</code>, dan OPD dengan <code>SKPD</code>, cukup ketikkan nama field tersebut di kolom kanan di bawah ini. Sistem otomatis mengenalinya!
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">NIP (Nomor Induk Pegawai) <span class="text-red-500">*</span></label>
                            <input type="text" name="field_mapping[nip]" value="{{ $activeMap['nip'] ?? 'nip' }}" required
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: nip</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                            <input type="text" name="field_mapping[name]" value="{{ $activeMap['name'] ?? 'nama_lengkap' }}" required
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: nama_lengkap atau nama</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">Email Resmi</label>
                            <input type="text" name="field_mapping[email]" value="{{ $activeMap['email'] ?? 'email' }}"
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: email atau surel</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">Perangkat Daerah / OPD <span class="text-red-500">*</span></label>
                            <input type="text" name="field_mapping[opd_name]" value="{{ $activeMap['opd_name'] ?? 'nama_opd' }}" required
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: nama_opd / opd / skpd</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">Bagian / Bidang / Seksi</label>
                            <input type="text" name="field_mapping[bagian]" value="{{ $activeMap['bagian'] ?? 'bagian' }}"
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: bagian / bidang / unit_kerja</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">Jabatan Saat Ini</label>
                            <input type="text" name="field_mapping[jabatan]" value="{{ $activeMap['jabatan'] ?? 'jabatan' }}"
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: jabatan / nama_jabatan</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">Pangkat / Golongan Ruang</label>
                            <input type="text" name="field_mapping[pangkat_golongan]" value="{{ $activeMap['pangkat_golongan'] ?? 'pangkat_golongan' }}"
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: pangkat_golongan / golongan</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                            <input type="text" name="field_mapping[phone]" value="{{ $activeMap['phone'] ?? 'nomor_hp' }}"
                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg font-mono text-xs">
                            <span class="text-[9px] text-slate-400">Default: nomor_hp / no_hp / telepon</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2 text-xs">
                        <span>💾 Simpan Seluruh Konfigurasi SIMPEG</span>
                    </button>
                </div>
            </div>

            <!-- Right 1 Col: Quick Tools & Manual Sync by NIP -->
            <div class="space-y-6">

                <!-- Instant Sync per NIP -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-4">
                    <div class="border-b border-slate-100 pb-2.5 flex items-center gap-2">
                        <span class="text-base">🎯</span>
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-xs">Tarik Cepat ASN per NIP</h4>
                            <p class="text-[10px] text-slate-400">Sinkronisasi data langsung untuk 1 pegawai.</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.simpeg.sync-nip') }}" method="POST" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Induk Pegawai (NIP)</label>
                            <input type="text" name="nip" placeholder="Contoh: 198503152010011002" required
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <button type="submit" class="w-full py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-2xs transition flex items-center justify-center gap-1.5">
                            <span>🔍 Tarik Data dari SIMPEG</span>
                        </button>
                    </form>
                </div>

                <!-- CLI & Scheduled Cron Info Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-3 text-xs">
                    <div class="border-b border-slate-100 pb-2 flex items-center gap-2">
                        <span class="text-base">⚙️</span>
                        <h4 class="font-extrabold text-slate-900 text-xs">Artisan CLI & Cron Job</h4>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Sistem juga menyediakan perintah konsol Laravel untuk otomatisasi sinkronisasi rutin setiap malam:
                    </p>
                    
                    <div class="p-2.5 rounded-xl bg-slate-900 text-emerald-400 font-mono text-[10px] space-y-1">
                        <div class="text-slate-400"># Tarik seluruh pegawai:</div>
                        <div>php artisan simpeg:sync --all</div>
                        <div class="text-slate-400 pt-1"># Tarik per NIP:</div>
                        <div>php artisan simpeg:sync --nip=1985...</div>
                        <div class="text-slate-400 pt-1"># Uji koneksi ping:</div>
                        <div>php artisan simpeg:sync --test</div>
                    </div>
                </div>

                <!-- Security & Governance Notice -->
                <div class="bg-emerald-50/50 rounded-2xl border border-emerald-200/80 p-4 space-y-2 text-[11px] text-emerald-900">
                    <div class="font-bold flex items-center gap-1.5 text-xs text-emerald-800">
                        <span>🛡️</span>
                        <span>Keamanan & UU PDP:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-emerald-800/90 leading-relaxed">
                        <li>Data NIP hasil penarikan API otomatis dienkripsi dengan standar <strong>AES-256-CBC</strong>.</li>
                        <li>Sistem mendeteksi NIP menggunakan <strong>Blind Index HMAC-SHA256</strong> sehingga tidak terjadi akun ganda saat ASN mutasi.</li>
                        <li>Riwayat Jam Pelajaran (JP), kursus, dan sertifikat yang pernah diperoleh ASN di LMS tidak akan terhapus saat terjadi mutasi dari SIMPEG.</li>
                    </ul>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
