@extends('layouts.app')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_60%_50%_at_50%_30%,rgba(20,184,166,0.1),transparent)] pointer-events-none"></div>

    <div class="w-full max-w-md space-y-6 relative z-10">
        
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white font-extrabold text-2xl flex items-center justify-center mx-auto shadow-sm">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Akun Baru</h1>
            <p class="text-xs text-slate-500">Mulai perjalanan upskilling dan dapatkan sertifikat terakreditasi.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Anti-Bot Honeypot Trap (Hidden from humans) -->
                <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                    <label for="website_hp_identity">Do not fill this field</label>
                    <input type="text" id="website_hp_identity" name="website_hp_identity" tabindex="-1" autocomplete="off" value="">
                </div>
                <input type="hidden" name="form_load_token" value="{{ encrypt(time()) }}">

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Contoh: dr. Budi Santoso, Sp.A"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition @error('name') border-red-400 bg-red-50 @enderror">
                    @error('name')
                        <p class="text-[11px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Resmi</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="nama@instansi.go.id"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition @error('email') border-red-400 bg-red-50 @enderror">
                    @error('email')
                        <p class="text-[11px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Minimal 6 karakter"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition @error('password') border-red-400 bg-red-50 @enderror">
                    @error('password')
                        <p class="text-[11px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="Ulangi kata sandi"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                </div>

                <!-- ASN Identification Section -->
                <div class="pt-2 border-t border-slate-100" x-data="{ isAsn: {{ old('nip') ? 'true' : 'false' }} }">
                    <label class="flex items-center gap-2.5 cursor-pointer py-1">
                        <input type="checkbox" x-model="isAsn" class="w-4 h-4 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                        <span class="text-xs font-semibold text-slate-700">Saya adalah Aparatur Sipil Negara (ASN) / Pegawai Daerah</span>
                    </label>

                    <div x-show="isAsn" x-cloak x-transition class="space-y-3 mt-3 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                        <div>
                            <label for="nip" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Induk Pegawai (NIP)</label>
                            <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                                   placeholder="18 Digit NIP resmi (contoh: 198805142014021003)"
                                   class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-mono transition">
                            <p class="text-[10px] text-slate-400 mt-1">NIP digunakan untuk mencatat akumulasi 20-24 Jam Pelajaran (JP) tahunan.</p>
                        </div>

                        <div>
                            <label for="headline" class="block text-xs font-semibold text-slate-700 mb-1">Instansi / Perangkat Daerah (OPD)</label>
                            <input type="text" id="headline" name="headline" value="{{ old('headline') }}"
                                   placeholder="Contoh: BKPSDM / Bapenda / Dinas Pendidikan / Setda Berau"
                                   class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <span>Daftar Akun Baru</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

        </div>

        <p class="text-center text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:text-brand-800">Masuk di sini</a>
        </p>

    </div>
</div>
@endsection
