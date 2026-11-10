@extends('layouts.app')

@section('title', 'Pendaftaran Trainer & Instruktur | LMS KAB Berau')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-50 via-white to-slate-50">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Top Breadcrumb & Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200/80 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                <span>Fasilitator & Instruktur Nasional</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Bergabung Menjadi <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-700 via-teal-600 to-cyan-600">Trainer & Instruktur</span>
            </h1>
            <p class="text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Bagikan keahlian Anda kepada seluruh peserta ASN, PPPK, dan aparatur Pemerintah Kabupaten Berau. Buat kursus interaktif, live webinar, workshop in-class, dan bimbingan teknis (Bimtek).
            </p>
        </div>

        @if($user->trainer_status === 'pending')
            <!-- Pending State Card -->
            <div class="p-8 bg-white rounded-3xl border border-amber-200/80 shadow-lg shadow-amber-500/5 text-center space-y-5">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-3xl shadow-inner">
                    ⏳
                </div>
                <div class="space-y-2 max-w-lg mx-auto">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        Status: Menunggu Persetujuan Admin
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Pengajuan Anda Sedang Ditinjau</h2>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Terima kasih telah mengajukan diri sebagai Trainer LMS KAB Berau. Tim kurikulum kami sedang meninjau portofolio dan profil Anda. Anda akan menerima pembaruan status dalam 1x24 jam kerja.
                    </p>
                </div>

                <div class="max-w-md mx-auto p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-left text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Lengkap:</span>
                        <span class="font-bold text-slate-900">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Bidang Keahlian:</span>
                        <span class="font-bold text-slate-900">{{ $user->trainer_expertise }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Pengajuan:</span>
                        <span class="font-bold text-slate-900">{{ $user->trainer_applied_at ? $user->trainer_applied_at->format('d M Y, H:i') : '-' }}</span>
                    </div>
                    @if($user->trainer_cv_path)
                        <div class="flex justify-between items-center pt-1 border-t border-slate-200/60">
                            <span class="text-slate-500">Berkas Pendukung:</span>
                            <a href="{{ asset($user->trainer_cv_path) }}" target="_blank" class="text-brand-600 hover:text-brand-800 font-semibold underline">
                                📄 Lihat CV / Sertifikat
                            </a>
                        </div>
                    @endif
                </div>

                <div class="pt-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-2xs">
                        ← Kembali ke Dashboard Belajar
                    </a>
                </div>
            </div>

        @elseif($user->trainer_status === 'approved' || $user->role === 'instructor')
            <!-- Approved State Card -->
            <div class="p-8 bg-white rounded-3xl border border-emerald-200 shadow-lg shadow-emerald-500/5 text-center space-y-5">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-3xl shadow-inner">
                    🎉
                </div>
                <div class="space-y-2 max-w-lg mx-auto">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        Status: Trainer Terverifikasi
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Selamat! Akun Trainer Anda Telah Aktif</h2>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Anda kini memiliki akses untuk merancang kurikulum, membuat materi E-Course, menyelenggarakan Live Webinar, serta mengadakan Bimtek & Workshop. Setiap materi baru akan melalui kurasi cepat oleh admin sebelum tayang publik.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-3">
                    <a href="{{ route('admin.courses.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 transition shadow-sm">
                        Kelola Materi & Kurikulum Saya
                    </a>
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition">
                        Dashboard Belajar
                    </a>
                </div>
            </div>

        @else
            <!-- Registration Form Card -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Form (8 cols) -->
                <div class="lg:col-span-8 p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="text-base font-bold text-slate-900">Formulir Pengajuan Trainer & Instruktur</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Isi rincian keahlian dan lampirkan CV atau sertifikat kompetensi Anda.</p>
                    </div>

                    @if($user->trainer_status === 'rejected')
                        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs">
                            <p class="font-bold">⚠️ Pengajuan Sebelumnya Belum Disetujui</p>
                            <p class="text-[11px] mt-0.5 text-rose-700">Silakan perbarui keahlian dan unggah dokumen pendukung yang lebih lengkap untuk mengajukan permohonan kembali.</p>
                        </div>
                    @endif

                    <form action="{{ route('trainer.register.post') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
                        @csrf

                        <!-- User Info Readonly -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                                <input type="text" value="{{ $user->name }}" disabled
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-xs cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5">Email Terdaftar</label>
                                <input type="email" value="{{ $user->email }}" disabled
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-xs cursor-not-allowed">
                            </div>
                        </div>

                        <!-- Expertise -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">
                                Bidang Keahlian Utama <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="trainer_expertise" required
                                   value="{{ old('trainer_expertise', $user->trainer_expertise) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="Contoh: Tata Kelola Pemerintahan, Manajemen Kinerja ASN, Pelayanan Publik, AI & Teknologi">
                            @error('trainer_expertise')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Headline -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">
                                Headline Profesional / Jabatan
                            </label>
                            <input type="text" name="headline"
                                   value="{{ old('headline', $user->headline) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="Contoh: Senior Enterprise Architect | Fasilitator TOT Nasional">
                        </div>

                        <!-- Bio & Experience -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">
                                Biografi & Pengalaman Pengajaran <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="trainer_bio" rows="4" required
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                      placeholder="Jelaskan latar belakang pendidikan, sertifikasi yang dimiliki, dan pengalaman Anda sebagai pemateri, instruktur, atau praktisi (minimal 40 karakter)...">{{ old('trainer_bio', $user->trainer_bio) }}</textarea>
                            @error('trainer_bio')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload CV / Certificate -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">
                                Unggah CV / Sertifikat Keahlian / Portofolio (PDF, JPG, PNG - Maks 5MB)
                            </label>
                            <input type="file" name="trainer_cv" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Lampirkan bukti sertifikasi kompetensi atau riwayat pengalaman mengajar Anda.</p>
                            @error('trainer_cv')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Agreement & Submit -->
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-[11px] text-slate-400">Dengan mendaftar, Anda menyetujui standar kualitas kurikulum LMS KAB Berau.</p>
                            <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center justify-center gap-2">
                                <span>Kirim Pengajuan Trainer</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Benefits & Guidelines (4 cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Keuntungan Bergabung</h3>
                        
                        <div class="space-y-3.5 text-xs">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    📚
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Format Pelatihan Beragam</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Dukungan E-Course asinkron, Live Webinar, Workshop in-class, dan Bimtek intensif.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    🏛️
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Jangkauan Luas ASN & Profesional</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Materi dapat ditargetkan khusus untuk OPD, jabatan tertentu, maupun peserta umum.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    🏆
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">E-Sertifikat Otomatis Ber-JP</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Sistem sertifikat digital otomatis dengan kalkulasi Jam Pelajaran (JP) dan verifikasi QR Code.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gradient-to-tr from-brand-900 to-slate-900 text-white rounded-3xl shadow-md space-y-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-teal-400">Proses Kurasi</span>
                        <h4 class="text-sm font-bold">Persetujuan Cepat & Terstruktur</h4>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Materi yang dibuat oleh Trainer akan melalui review admin sebelum tayang untuk memastikan kualitas silabus dan materi yang disajikan.
                        </p>
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection
