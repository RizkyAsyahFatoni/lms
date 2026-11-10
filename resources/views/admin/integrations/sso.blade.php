@extends('layouts.admin')

@section('page_title', 'Pengaturan SSO Ready')

@section('content')
<div class="space-y-6"
     x-data="{
         isActive: {{ $setting->is_active ? 'true' : 'false' }},
         providerName: '{{ addslashes($setting->provider_name) }}',
         clientId: '{{ addslashes($setting->client_id ?? '') }}',
         clientSecret: '{{ addslashes($setting->client_secret ?? '') }}',
         authUrl: '{{ addslashes($setting->authorization_url ?? '') }}',
         tokenUrl: '{{ addslashes($setting->token_url ?? '') }}',
         userinfoUrl: '{{ addslashes($setting->userinfo_url ?? '') }}',
         scopes: '{{ addslashes($setting->scopes ?? 'openid profile email nip nik') }}',
         buttonLabel: '{{ addslashes($setting->button_label) }}',
         autoRegister: {{ $setting->auto_register ? 'true' : 'false' }},
         defaultRole: '{{ $setting->default_role }}',

         applyPreset(type) {
             if (type === 'satusehat') {
                 this.providerName = 'SATUSEHAT Identity (Kemenkes)';
                 this.authUrl = 'https://api-satusehat.kemkes.go.id/oauth2/v1/auth';
                 this.tokenUrl = 'https://api-satusehat.kemkes.go.id/oauth2/v1/token';
                 this.userinfoUrl = 'https://api-satusehat.kemkes.go.id/oauth2/v1/userinfo';
                 this.scopes = 'openid profile email nik faskes_id';
                 this.buttonLabel = 'Masuk dengan Akun SatuSehat Kemenkes';
             } else if (type === 'bkn') {
                 this.providerName = 'SSO ASN Nasional (BKN SIASN)';
                 this.authUrl = 'https://sso-asn.bkn.go.id/auth/realms/public/protocol/openid-connect/auth';
                 this.tokenUrl = 'https://sso-asn.bkn.go.id/auth/realms/public/protocol/openid-connect/token';
                 this.userinfoUrl = 'https://sso-asn.bkn.go.id/auth/realms/public/protocol/openid-connect/userinfo';
                 this.scopes = 'openid profile email nip instansi';
                 this.buttonLabel = 'Masuk dengan Akun ASN (BKN SIASN)';
             } else if (type === 'keycloak') {
                 this.providerName = 'Keycloak SSO Instansi Daerah';
                 this.authUrl = 'https://sso.banyuwangikab.go.id/realms/pemerintah/protocol/openid-connect/auth';
                 this.tokenUrl = 'https://sso.banyuwangikab.go.id/realms/pemerintah/protocol/openid-connect/token';
                 this.userinfoUrl = 'https://sso.banyuwangikab.go.id/realms/pemerintah/protocol/openid-connect/userinfo';
                 this.scopes = 'openid profile email nip';
                 this.buttonLabel = 'Masuk dengan SSO Pemerintah Daerah';
             }
         }
     }">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Sistem SSO Ready (Single Sign-On)</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase transition"
                      :class="isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500'">
                    <span x-text="isActive ? '● STATUS AKTIF' : '○ NONAKTIF'"></span>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Integrasi login satu pintu dengan SIASN BKN, Keycloak Pemkab, SatuSehat, atau OAuth2 instansi tanpa perlu koding ulang.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" form="ssoForm" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Pengaturan SSO</span>
            </button>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left 8 Cols: Form Configuration -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-6">
            
            <!-- 1-Click Preset Bar -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span>⚡</span>
                        <span>Preset Cepat (1-Click Template):</span>
                    </span>
                    <span class="text-[10px] font-mono text-slate-400">Pilih penyedia untuk auto-fill endpoint</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap pt-1">
                    <button type="button" @click="applyPreset('satusehat')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition flex items-center gap-1.5">
                        <span>🏥 SatuSehat Kemenkes</span>
                    </button>
                    <button type="button" @click="applyPreset('bkn')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition flex items-center gap-1.5">
                        <span>🏛️ BKN SIASN Identity</span>
                    </button>
                    <button type="button" @click="applyPreset('keycloak')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition flex items-center gap-1.5">
                        <span>🔑 Keycloak Instansi Daerah</span>
                    </button>
                </div>
            </div>

            <form id="ssoForm" action="{{ route('admin.sso.update') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Enable / Disable Switch -->
                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 bg-white">
                    <div>
                        <p class="text-xs font-bold text-slate-900">Aktifkan Tombol SSO di Halaman Login</p>
                        <p class="text-[11px] text-slate-500">Jika diaktifkan, tombol login SSO akan muncul otomatis untuk seluruh peserta dan ASN.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <!-- Provider Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Penyedia Identitas SSO <span class="text-rose-500">*</span></label>
                    <input type="text" name="provider_name" x-model="providerName" required placeholder="Contoh: SSO ASN BKN / SIASN Identity"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-semibold">
                </div>

                <!-- Client ID & Secret -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Client ID (API Key)</label>
                        <input type="text" name="client_id" x-model="clientId" placeholder="client_id_dari_pusat"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Client Secret (API Secret)</label>
                        <input type="password" name="client_secret" x-model="clientSecret" placeholder="••••••••••••••••"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono">
                    </div>
                </div>

                <!-- Endpoints -->
                <div class="space-y-3 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Authorization Endpoint URL</label>
                        <input type="url" name="authorization_url" x-model="authUrl" placeholder="https://sso.beraukab.go.id/oauth2/auth"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Token Endpoint URL</label>
                            <input type="url" name="token_url" x-model="tokenUrl" placeholder="https://sso.beraukab.go.id/oauth2/token"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">UserInfo Endpoint URL</label>
                            <input type="url" name="userinfo_url" x-model="userinfoUrl" placeholder="https://sso.beraukab.go.id/oauth2/userinfo"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono">
                        </div>
                    </div>
                </div>

                <!-- Scopes & Button Label -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">OAuth Scopes</label>
                        <input type="text" name="scopes" x-model="scopes" placeholder="openid profile email nip nik"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Tombol Login <span class="text-rose-500">*</span></label>
                        <input type="text" name="button_label" x-model="buttonLabel" required placeholder="Masuk dengan SSO ASN Daerah"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-semibold">
                    </div>
                </div>

                <!-- Auto Register & Default Role -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="auto_register" name="auto_register" value="1" x-model="autoRegister"
                               class="w-4 h-4 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                        <label for="auto_register" class="text-xs text-slate-700 font-semibold cursor-pointer">
                            Auto-Register Peserta Baru saat Pertama Kali Login SSO
                        </label>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 font-medium">Peran Bawaan:</span>
                        <select name="default_role" x-model="defaultRole" class="px-2.5 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg font-bold">
                            <option value="student">Peserta (Siswa ASN)</option>
                            <option value="instructor">Instruktur Pelatihan</option>
                        </select>
                    </div>
                </div>

            </form>

        </div>

        <!-- Right 4 Cols: Live Preview & Callback Info -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Live Preview of Login Button -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Pratinjau Tombol di Halaman Login</h3>
                    <span class="text-[10px] font-mono text-teal-600 font-bold">Real-time</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-3 text-center">
                    <p class="text-[11px] text-slate-400">Tampilan saat peserta membuka halaman login:</p>
                    
                    <div x-show="isActive" class="space-y-3">
                        <div class="w-full flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-teal-700 via-brand-700 to-slate-900 shadow-md transition">
                            <span class="text-base">🛡️</span>
                            <span x-text="buttonLabel || 'Masuk dengan SSO ASN'"></span>
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-mono text-slate-400">
                            <span class="flex-1 h-px bg-slate-200"></span>
                            <span>ATAU MASUK DENGAN EMAIL</span>
                            <span class="flex-1 h-px bg-slate-200"></span>
                        </div>
                    </div>

                    <div x-show="!isActive" class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                        ⚠️ SSO saat ini nonaktif. Tombol SSO tidak akan muncul di halaman login.
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 leading-relaxed">
                    <p class="font-bold text-slate-700 mb-1">Callback Redirect URI untuk didaftarkan ke IdP:</p>
                    <div class="p-2 rounded-lg bg-slate-100 font-mono text-[10px] text-slate-700 select-all break-all border border-slate-200">
                        {{ route('auth.sso.callback') }}
                    </div>
                </div>
            </div>

            @if(app()->environment('local', 'testing'))
            <!-- Simulasi Integrasi Terbatas Lingkungan Pengujian -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">🛡️</span>
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Uji Koneksi Autentikasi</h3>
                </div>
                <p class="text-xs text-slate-500">Verifikasi alur data profil pegawai dari penyedia identitas ke database LMS BKPSDM.</p>
                
                <form action="{{ Route::has('auth.sso.mock') ? route('auth.sso.mock') : '#' }}" method="POST" target="_blank" class="pt-2">
                    @csrf
                    <input type="hidden" name="name" value="Aparatur Sipil Negara BKPSDM">
                    <input type="hidden" name="email" value="asn.berau@beraukab.go.id">
                    <input type="hidden" name="nip" value="198805142014021003">
                    <input type="hidden" name="nik" value="3510091405880002">
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                        <span>Verifikasi Alur SSO &rarr;</span>
                    </button>
                </form>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection
