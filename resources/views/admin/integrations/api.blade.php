@extends('layouts.admin')

@section('page_title', 'Pengaturan & Integrasi API')

@section('content')
<div class="space-y-6" 
     x-data="{ 
         activeApiTab: '{{ request('tab', 'rest') }}',
         createModal: false, 
         activeTesterTab: 'participants', 
         copiedKey: null,
         copiedUrl: null
     }">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white flex items-center justify-center font-black shadow-sm text-lg">
                    🌐
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">API Gateway & Integrasi Sistem</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola kunci REST API ASN, Google OAuth 2.0 Authentication, dan API Payment Gateway.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Dynamic header action based on active tab -->
            <template x-if="activeApiTab === 'rest'">
                <button @click="createModal = true" 
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Terbitkan API Key Baru</span>
                </button>
            </template>
            <template x-if="activeApiTab === 'google'">
                <button type="submit" form="googleAuthForm"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                    <span>Simpan Pengaturan Google API</span>
                </button>
            </template>
            <template x-if="activeApiTab === 'payment'">
                <button type="submit" form="paymentGatewayForm"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                    <span>Simpan Pengaturan Payment</span>
                </button>
            </template>
        </div>
    </div>

    <!-- Top Tab Navigation Bar (v0 Precision Style) -->
    <div class="bg-white p-1.5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-1 overflow-x-auto">
        <!-- Tab 1: Rest API -->
        <button @click="activeApiTab = 'rest'" 
                :class="activeApiTab === 'rest' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
            <span class="text-sm">🌐</span>
            <span>Rest API (Integrasi ASN)</span>
            <span :class="activeApiTab === 'rest' ? 'bg-slate-800 text-teal-300' : 'bg-slate-100 text-slate-500'" 
                  class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold">
                {{ $apiKeys->count() }} Key
            </span>
        </button>

        <!-- Tab 2: Google Auth API -->
        <button @click="activeApiTab = 'google'" 
                :class="activeApiTab === 'google' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>API Google Auth</span>
            <span class="w-2 h-2 rounded-full {{ $googleSetting->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-slate-300' }}"></span>
        </button>

        <!-- Tab 3: Payment Gateway API -->
        <button @click="activeApiTab = 'payment'" 
                :class="activeApiTab === 'payment' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
            <span class="text-sm">💳</span>
            <span>API Payment Gateway</span>
            <span class="w-2 h-2 rounded-full {{ ($paymentSetting->is_active ?? false) ? 'bg-emerald-400 animate-pulse' : 'bg-slate-300' }}"></span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: REST API (ASN & SIASN INTEGRATION) -->
    <!-- ========================================================================= -->
    <div x-show="activeApiTab === 'rest'" x-cloak class="space-y-6">
        
        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <p class="text-[11px] text-slate-500 font-medium">Peserta ASN (NIP Valid)</p>
                <p class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($totalAsnParticipants) }}</p>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <p class="text-[11px] text-slate-500 font-medium">Sertifikat Terbit</p>
                <p class="text-2xl font-black text-brand-700 tracking-tight mt-1">{{ number_format($totalCertificates) }}</p>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <p class="text-[11px] text-slate-500 font-medium">Akumulasi Jam Pelajaran (JP)</p>
                <p class="text-2xl font-black text-emerald-600 tracking-tight mt-1">{{ number_format($totalJpDelivered) }} <span class="text-xs font-bold text-slate-400">JP</span></p>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
                <p class="text-[11px] text-slate-500 font-medium">API Token Aktif</p>
                <p class="text-2xl font-black text-indigo-600 tracking-tight mt-1">{{ $apiKeys->where('is_active', true)->count() }} <span class="text-xs font-bold text-slate-400">Kunci</span></p>
            </div>
        </div>

        <!-- API Keys Table Section -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-slate-900 text-sm tracking-tight">Daftar Kunci Akses API (API Keys) Terbit</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kunci API yang aktif dapat digunakan aplikasi pihak ketiga untuk mengakses data peserta & JP.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-600 self-start sm:self-center">
                    Bearer Token Auth
                </span>
            </div>

            @if($apiKeys->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                        🔑
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-700">Belum ada API Key yang diterbitkan</p>
                        <p class="text-[11px] text-slate-400">Klik tombol '+ Terbitkan API Key Baru' untuk membuat token integrasi pertama.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/60 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">Nama Aplikasi / Instansi Pemohon</th>
                                <th class="py-3 px-4">Token Autentikasi (Bearer Token)</th>
                                <th class="py-3 px-4">Pembatasan IP</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Dibuat</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($apiKeys as $apiKey)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $apiKey->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">ID: #{{ $apiKey->id }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 text-[11px] max-w-[200px] truncate border border-slate-200">
                                                {{ substr($apiKey->token, 0, 14) }}••••••••••••••••
                                            </span>
                                            <button type="button" 
                                                    @click="navigator.clipboard.writeText('{{ $apiKey->token }}'); copiedKey = {{ $apiKey->id }}; setTimeout(() => copiedKey = null, 2000)"
                                                    class="p-1 rounded hover:bg-slate-200 text-slate-500 transition" title="Salin Token Lengkap">
                                                <span x-show="copiedKey !== {{ $apiKey->id }}">📋</span>
                                                <span x-show="copiedKey === {{ $apiKey->id }}" class="text-emerald-600 font-bold text-[10px]">✓ Tersalin</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">
                                        {{ $apiKey->allowed_ips ?: 'Semua IP Diizinkan' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($apiKey->is_active)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                        {{ $apiKey->created_at->format('d M Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form action="{{ route('admin.api.toggle', $apiKey) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-2 py-1 rounded-lg text-[11px] font-semibold transition {{ $apiKey->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                    {{ $apiKey->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.api.destroy', $apiKey) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mencabut dan menghapus kunci API ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- REST API Endpoints Documentation & Live Interactive Tester -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-black text-slate-900 text-sm tracking-tight">Dokumentasi Endpoint REST API Eksternal ASN</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gunakan header <code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-brand-700">Authorization: Bearer {API_KEY}</code> untuk setiap request.</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button @click="activeTesterTab = 'participants'" :class="activeTesterTab === 'participants' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        Peserta by NIP
                    </button>
                    <button @click="activeTesterTab = 'all'" :class="activeTesterTab === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        Semua ASN
                    </button>
                    <button @click="activeTesterTab = 'certificates'" :class="activeTesterTab === 'certificates' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        Sertifikat & JP
                    </button>
                </div>
            </div>

            <div class="p-5 space-y-4">
                <!-- Endpoint 1: Participant by NIP -->
                <div x-show="activeTesterTab === 'participants'" class="space-y-3">
                    <div class="flex items-center gap-2 bg-slate-900 text-emerald-400 p-3 rounded-xl font-mono text-xs overflow-x-auto">
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold uppercase">GET</span>
                        <span class="text-white">{{ url('/api/v1/asn/participants') }}/{nip}</span>
                    </div>
                    <p class="text-xs text-slate-600">Mengambil detail data ASN berdasarkan 18 digit NIP resmi, termasuk akumulasi JP per tahun, kursus selesai, dan sertifikat yang valid.</p>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 font-mono text-[11px] text-slate-700 overflow-x-auto">
                        <pre>{
  "success": true,
  "message": "Data peserta ASN berhasil ditemukan",
  "data": {
    "nip": "198805142014021003",
    "name": "dr. Budi Santoso, Sp.A",
    "email": "budi.santoso@dinkes.go.id",
    "jp_current_year": 16,
    "jp_target": 20,
    "jp_target_achieved": false,
    "completed_courses_count": 3,
    "certificates_count": 2
  }
}</pre>
                    </div>
                </div>

                <!-- Endpoint 2: All Participants -->
                <div x-show="activeTesterTab === 'all'" x-cloak class="space-y-3">
                    <div class="flex items-center gap-2 bg-slate-900 text-emerald-400 p-3 rounded-xl font-mono text-xs overflow-x-auto">
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold uppercase">GET</span>
                        <span class="text-white">{{ url('/api/v1/asn/participants') }}?page=1&per_page=20</span>
                    </div>
                    <p class="text-xs text-slate-600">Sinkronisasi massal seluruh data peserta ASN terdaftar pada LMS KAB Berau untuk kebutuhan audit BKD/SIMPEG.</p>
                </div>

                <!-- Endpoint 3: Certificates & JP -->
                <div x-show="activeTesterTab === 'certificates'" x-cloak class="space-y-3">
                    <div class="flex items-center gap-2 bg-slate-900 text-emerald-400 p-3 rounded-xl font-mono text-xs overflow-x-auto">
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold uppercase">GET</span>
                        <span class="text-white">{{ url('/api/v1/asn/certificates') }}?year={{ date('Y') }}</span>
                    </div>
                    <p class="text-xs text-slate-600">Daftar seluruh sertifikat kompetensi yang diterbitkan beserta jumlah Jam Pelajaran (JP) yang diperoleh.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: GOOGLE OAUTH 2.0 API -->
    <!-- ========================================================================= -->
    <div x-show="activeApiTab === 'google'" x-cloak
         x-data="{
             googleActive: {{ $googleSetting->is_active ? 'true' : 'false' }},
             clientId: '{{ addslashes($googleSetting->client_id ?? '') }}',
             clientSecret: '{{ addslashes($googleSetting->client_secret ?? '') }}',
             buttonLabel: '{{ addslashes($googleSetting->button_label ?? 'Masuk dengan Akun Google') }}',
             copiedCallback: false,
             copiedOrigin: false
         }"
         class="space-y-6">

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-50/50 via-white to-indigo-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-2 flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-black text-slate-900 text-sm tracking-tight">Integrasi Google OAuth 2.0 Authentication</h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase"
                                  :class="googleActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                                  x-text="googleActive ? '● AKTIF DI HALAMAN LOGIN' : '○ NONAKTIF'"></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Memungkinkan peserta umum dan ASN login 1-klik menggunakan akun Google / Gmail instansi resmi.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 p-6">
                <!-- FORM SETTINGS (7 COLS) -->
                <form id="googleAuthForm" action="{{ route('admin.api.google.update') }}" method="POST" class="lg:col-span-7 space-y-4">
                    @csrf

                    <!-- Toggle Active -->
                    <div class="p-4 rounded-xl border" :class="googleActive ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Status Tombol Login Google</span>
                                <span class="text-[11px] text-slate-500 block">Tampilkan opsi masuk dengan Google pada form login publik & peserta</span>
                            </div>
                            <input type="checkbox" name="is_active" value="1" x-model="googleActive"
                                   class="w-5 h-5 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                        </label>
                    </div>

                    <!-- Client ID & Client Secret -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Google Client ID <span class="text-rose-500">*</span></label>
                            <input type="text" name="client_id" x-model="clientId" placeholder="Contoh: 1234567890-abcdefg.apps.googleusercontent.com"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Google Client Secret</label>
                            <input type="password" name="client_secret" x-model="clientSecret" placeholder="Contoh: GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono transition">
                        </div>
                    </div>

                    <!-- Button Label & Role -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Label Tombol Login</label>
                            <input type="text" name="button_label" x-model="buttonLabel" required
                                   placeholder="Masuk dengan Akun Google"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-medium transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Peran Pengguna Bawaan</label>
                            <select name="default_role" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold">
                                <option value="student" {{ $googleSetting->default_role === 'student' ? 'selected' : '' }}>Peserta (Siswa ASN)</option>
                                <option value="instructor" {{ $googleSetting->default_role === 'instructor' ? 'selected' : '' }}>Instruktur Pelatihan</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="google_auto_reg" name="auto_register" value="1" {{ $googleSetting->auto_register ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                        <label for="google_auto_reg" class="text-xs text-slate-700 font-medium cursor-pointer">
                            Otomatis Daftarkan Akun Baru jika email Google belum terdaftar di database
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all hover:shadow">
                            Simpan Pengaturan Google API
                        </button>
                    </div>
                </form>

                <!-- GUIDE & PREVIEW PANEL (5 COLS) -->
                <div class="lg:col-span-5 space-y-4">
                    <!-- Live Preview -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Pratinjau Tombol di Halaman Login:</span>
                            <span class="text-[10px] font-mono text-emerald-600 font-bold">Real-time</span>
                        </div>

                        <div x-show="googleActive" class="space-y-2">
                            <div class="w-full flex items-center justify-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 shadow-2xs">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span x-text="buttonLabel || 'Masuk dengan Akun Google'"></span>
                            </div>
                        </div>

                        <div x-show="!googleActive" class="p-3 bg-white rounded-lg border border-slate-200 text-center text-xs text-slate-400">
                            Tombol login Google disembunyikan dari halaman login.
                        </div>
                    </div>

                    <!-- Callback URIs to Copy -->
                    <div class="p-4 rounded-xl bg-slate-900 text-white space-y-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-400">URL Konfigurasi Google Cloud Console:</span>
                        
                        <div class="space-y-2 text-[11px] font-mono">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase">Authorized Redirect URI:</span>
                                <div class="flex items-center justify-between gap-2 p-2 bg-slate-800 rounded-lg text-emerald-400 mt-1">
                                    <span class="truncate">{{ route('auth.google.callback') }}</span>
                                    <button type="button" @click="navigator.clipboard.writeText('{{ route('auth.google.callback') }}'); copiedCallback = true; setTimeout(() => copiedCallback = false, 2000)"
                                            class="text-xs text-white hover:text-teal-300 transition shrink-0">
                                        <span x-show="!copiedCallback">Salin</span>
                                        <span x-show="copiedCallback" class="text-teal-300">✓</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: PAYMENT GATEWAY API -->
    <!-- ========================================================================= -->
    <div x-show="activeApiTab === 'payment'" x-cloak
         x-data="{
             paymentActive: {{ ($paymentSetting->is_active ?? false) ? 'true' : 'false' }},
             provider: '{{ $paymentSetting->provider ?? 'midtrans' }}',
             environment: '{{ $paymentSetting->environment ?? 'sandbox' }}',
             merchantId: '{{ addslashes($paymentSetting->merchant_id ?? '') }}',
             clientKey: '{{ addslashes($paymentSetting->client_key ?? '') }}',
             serverKey: '{{ addslashes($paymentSetting->server_key ?? '') }}',
             webhookSecret: '{{ addslashes($paymentSetting->webhook_secret ?? '') }}',
             webhookUrl: '{{ url('/api/payment/notification') }}',
             copiedWebhook: false
         }"
         class="space-y-6">

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-50/50 via-white to-emerald-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-xl shadow-xs shrink-0">
                        💳
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-black text-slate-900 text-sm tracking-tight">Integrasi API Payment Gateway Indonesia</h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase"
                                  :class="paymentActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                                  x-text="paymentActive ? '● GATEWAY AKTIF' : '○ NONAKTIF'"></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase"
                                  :class="environment === 'production' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
                                  x-text="environment.toUpperCase()"></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Integrasi pembayaran kursus berbayar / in-class workshop dengan QRIS, Virtual Account Bank, dan E-Wallet.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 p-6">
                <!-- FORM PAYMENT SETTINGS (7 COLS) -->
                <form id="paymentGatewayForm" action="{{ route('admin.api.payment.update') }}" method="POST" class="lg:col-span-7 space-y-4">
                    @csrf

                    <!-- Status Toggle -->
                    <div class="p-4 rounded-xl border" :class="paymentActive ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Status Pembayaran Otomatis</span>
                                <span class="text-[11px] text-slate-500 block">Aktifkan modul pembayaran online otomatis untuk kursus premium</span>
                            </div>
                            <input type="checkbox" name="is_active" value="1" x-model="paymentActive"
                                   class="w-5 h-5 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                        </label>
                    </div>

                    <!-- Provider & Environment Selector -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Penyedia Payment Gateway</label>
                            <select name="provider" x-model="provider" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                                <option value="midtrans">Midtrans (Snap & Core API)</option>
                                <option value="xendit">Xendit (Invoice & VA API)</option>
                                <option value="tripay">Tripay (Payment Gateway UKM/LMS)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Mode Environment</label>
                            <select name="environment" x-model="environment" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                                <option value="sandbox">Sandbox (Mode Pengujian / Simulasi)</option>
                                <option value="production">Production (Mode Transaksi Asli / Live)</option>
                            </select>
                        </div>
                    </div>

                    <!-- API Keys Fields -->
                    <div class="space-y-3 pt-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Merchant ID / Kode Toko</label>
                            <input type="text" name="merchant_id" x-model="merchantId" placeholder="Contoh: G123456789 atau T1234"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Client Key / Public API Key</label>
                            <input type="text" name="client_key" x-model="clientKey" placeholder="Contoh: SB-Mid-client-xxxxxxxx atau xnd_public_..."
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Server Key / Secret API Key <span class="text-rose-500">*</span></label>
                            <input type="password" name="server_key" x-model="serverKey" placeholder="Contoh: SB-Mid-server-xxxxxxxx atau xnd_development_..."
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Webhook Secret / Signature Verification Key</label>
                            <input type="password" name="webhook_secret" x-model="webhookSecret" placeholder="Kunci verifikasi callback/webhook dari dashboard gateway"
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono transition">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all hover:shadow">
                            Simpan Pengaturan Payment Gateway
                        </button>
                    </div>
                </form>

                <!-- SIDE GUIDE & WEBHOOK (5 COLS) -->
                <div class="lg:col-span-5 space-y-4">
                    <!-- Webhook Callback URL Card -->
                    <div class="p-4 rounded-xl bg-slate-900 text-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-400">Webhook Notification URL:</span>
                            <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-slate-800 text-slate-300">POST Request</span>
                        </div>
                        
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Masukkan URL ini pada kolom <em>Payment Notification URL</em> di dashboard Midtrans / Xendit / Tripay Anda agar status pembayaran terverifikasi otomatis.
                        </p>

                        <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-800 rounded-xl font-mono text-xs text-emerald-400 border border-slate-700">
                            <span class="truncate" x-text="webhookUrl"></span>
                            <button type="button" @click="navigator.clipboard.writeText(webhookUrl); copiedWebhook = true; setTimeout(() => copiedWebhook = false, 2000)"
                                    class="px-2.5 py-1 rounded-lg bg-teal-500 text-slate-950 font-bold text-[10px] hover:bg-teal-400 transition shrink-0">
                                <span x-show="!copiedWebhook">Salin URL</span>
                                <span x-show="copiedWebhook">✓ Tersalin</span>
                            </button>
                        </div>
                    </div>

                    <!-- Supported Channels Pill -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Kanal Pembayaran Yang Didukung:</span>
                        <div class="flex flex-wrap gap-1.5 text-[10px] font-mono font-bold">
                            <span class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-700">QRIS (Semua E-Wallet)</span>
                            <span class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-700">BCA Virtual Account</span>
                            <span class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-700">Mandiri Bill</span>
                            <span class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-700">BNI & BRI VA</span>
                            <span class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-700">ShopeePay / GoPay</span>
                            <span class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-700">Indomaret / Alfamart</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL: Terbitkan API Key Baru (Alpine) -->
    <div x-show="createModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="createModal = false" class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Terbitkan Kunci API Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.api.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Aplikasi / Instansi Pemohon <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: SIMPEG Prov Jatim, e-Kinerja BKN"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Batasi Alamat IP (Opsional)</label>
                    <input type="text" name="allowed_ips" placeholder="Contoh: 103.120.45.10, 192.168.1.0/24"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white font-mono transition">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan untuk mengizinkan request dari semua IP server.</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="createModal = false" type="button" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        Terbitkan Token
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
