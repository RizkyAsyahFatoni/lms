@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pengelolaan Profil & Akun</h1>
                {!! $user->role_badge !!}
            </div>
            <p class="text-xs text-slate-500 mt-1">Perbarui biodata resmi, data kepegawaian ASN, serta kata sandi akun LMS Anda.</p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-2xs transition self-start sm:self-auto">
            <span>&larr;</span>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Alert Notifications -->
    @if(isset($pendingRequest) && $pendingRequest)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs shadow-2xs space-y-2">
            <div class="flex items-center gap-2 font-extrabold text-sm text-amber-900">
                <span>⏳</span>
                <span>Permohonan Perubahan Data Kepegawaian Sedang Menunggu Verifikasi BKPSDM</span>
            </div>
            <p class="text-amber-800 leading-relaxed">
                Anda telah mengajukan perubahan data resmi kepegawaian pada {{ $pendingRequest->created_at->format('d M Y, H:i') }}. 
                Data kepegawaian baru akan disinkronkan setelah disetujui oleh Administrator BKPSDM.
            </p>
            <div class="p-3 bg-white/80 rounded-xl border border-amber-200/80 space-y-1 text-[11px]">
                <div class="font-bold text-slate-700">Data yang sedang diajukan verifikasi:</div>
                <ul class="list-disc list-inside space-y-0.5 text-slate-600">
                    @foreach($pendingRequest->proposed_changes as $key => $val)
                        <li>
                            <strong class="capitalize">{{ str_replace('_', ' ', $key) }}:</strong> 
                            <span class="text-emerald-700 font-semibold">{{ $val }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

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

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-2xs space-y-1">
            <p class="font-bold">Terdapat kesalahan pengisian formulir:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: User Summary Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 text-center space-y-4">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-tr from-brand-600 to-teal-400 text-white font-black text-2xl flex items-center justify-center shadow-md">
                    {{ substr($user->name, 0, 2) }}
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base leading-snug">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $user->email }}</p>
                    <div class="mt-2.5 inline-block">
                        {!! $user->role_badge !!}
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 text-left space-y-2.5 text-xs">
                    <div>
                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">OPD / Instansi:</span>
                        <span class="font-semibold text-slate-800">{{ $user->opd_name ?: ($user->headline ?: '-') }}</span>
                    </div>
                    @if($user->bagian)
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Bagian / Bidang:</span>
                            <span class="font-semibold text-slate-800">{{ $user->bagian }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Jabatan:</span>
                        <span class="font-semibold text-slate-800">{{ $user->jabatan ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">NIP:</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $user->nip ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Status Akun:</span>
                        <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Self-Activity Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <span>📜</span>
                        <span>Aktivitas Akun Saya</span>
                    </h4>
                    <span class="text-[10px] text-slate-400 font-mono">10 Terakhir</span>
                </div>

                @if($recentLogs->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada catatan aktivitas.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentLogs as $log)
                            <div class="text-xs border-b border-slate-50 pb-2 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between text-[10px] text-slate-400">
                                    <span class="font-bold text-slate-600">{!! $log->action_badge !!}</span>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-slate-700 font-medium mt-1 leading-snug">{{ $log->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Profile Form & Change Password -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Profile Data Form -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Biodata & Informasi Kepegawaian</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pastikan NIP, OPD, Bagian, dan Jabatan sesuai dengan dokumen SK resmi Anda.</p>
                    </div>
                    @if(!in_array($user->role, ['admin', 'super_admin', 'bkpsdm']))
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200 flex items-center gap-1">
                            <span>🛡️</span>
                            <span>Verifikasi BKPSDM Aktif</span>
                        </span>
                    @endif
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Induk Pegawai (NIP)</label>
                            <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" placeholder="19870101..."
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email Resmi <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP Aktif</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08123456789"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Perangkat Daerah / OPD</label>
                            <input type="text" name="opd_name" value="{{ old('opd_name', $user->opd_name) }}" placeholder="Contoh: Dinas Komunikasi dan Informatika"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Bagian / Bidang / Seksi</label>
                            <input type="text" name="bagian" value="{{ old('bagian', $user->bagian) }}" placeholder="Contoh: Bidang Aplikasi Informatika"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Jabatan Saat Ini</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Contoh: Pranata Komputer Ahli Muda"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Pangkat / Golongan Ruang</label>
                            <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', $user->pangkat_golongan) }}" placeholder="Contoh: Penata Tingkat I / III/d"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>

                    @if(!in_array($user->role, ['admin', 'super_admin', 'bkpsdm']))
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                            <label class="block font-bold text-slate-700">Catatan / Alasan Perubahan Data Kepegawaian (Opsional)</label>
                            <input type="text" name="change_reason" placeholder="Contoh: Mutasi per 1 Oktober 2026 berdasarkan SK Bupati No. 821/..."
                                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition text-xs">
                            <p class="text-[10px] text-slate-400">Catatan ini akan dikirimkan kepada Admin BKPSDM sebagai dasar persetujuan perubahan data OPD/Jabatan Anda.</p>
                        </div>
                    @endif

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bio Singkat / Catatan Kompetensi</label>
                        <textarea name="bio" rows="2" placeholder="Tuliskan latar belakang keahlian atau minat pengembangan kompetensi..."
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                            <span>💾 Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold text-slate-900 text-sm">Ganti Kata Sandi (Password)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui kata sandi secara berkala demi keamanan akun dan data pembelajaran Anda.</p>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                        <input type="password" name="current_password" required placeholder="Masukkan password yang aktif saat ini"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required minlength="6" placeholder="Ketik ulang password baru"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                            <span>🔒 Update Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
