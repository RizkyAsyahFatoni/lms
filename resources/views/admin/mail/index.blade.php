@extends('layouts.admin')

@section('page_title', 'Pengaturan Email SMTP & Template Notifikasi')

@section('content')
<!-- Google Font: Urbanist (Kinlo signature font) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

<style>
.kinlo-root {
  font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --kinlo-ink: #15212a;
  --kinlo-accent: #0e6c84;
  --kinlo-accent-hi: #127d98;
  --kinlo-glass: rgba(255, 255, 255, 0.88);
  --kinlo-glass-rim: rgba(255, 255, 255, 0.95);
  --kinlo-glass-edge: rgba(23, 37, 45, 0.08);
  --kinlo-shadow: 0 2px 4px rgba(23, 37, 45, 0.04), 0 16px 36px -18px rgba(23, 37, 45, 0.16);
  --kinlo-ease: cubic-bezier(0.22, 1, 0.36, 1);
}
.kinlo-card {
  background: radial-gradient(120% 80% at 0% 0%, #ffffff, transparent 65%), var(--kinlo-glass);
  backdrop-filter: blur(24px) saturate(170%);
  border-radius: 20px;
  box-shadow: inset 0 1px 0 var(--kinlo-glass-rim), inset 0 0 0 1px var(--kinlo-glass-edge), var(--kinlo-shadow);
}
.kinlo-btn {
  height: 42px;
  font-size: 13.5px;
  font-weight: 700;
  border-radius: 12px;
  padding: 0 18px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all 0.2s var(--kinlo-ease);
  cursor: pointer;
  white-space: nowrap;
}
.kinlo-btn:active { transform: scale(0.97); }
.kinlo-btn-primary {
  background: var(--kinlo-accent);
  color: #ffffff;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 8px 20px -6px var(--kinlo-accent);
}
.kinlo-btn-primary:hover {
  background: var(--kinlo-accent-hi);
  transform: translateY(-1.5px);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35), 0 12px 24px -6px rgba(14, 108, 132, 0.55);
}
.kinlo-btn-ghost {
  background: rgba(255, 255, 255, 0.8);
  color: var(--kinlo-ink);
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px rgba(23, 37, 45, 0.12);
}
.kinlo-btn-ghost:hover {
  background: #ffffff;
  transform: translateY(-1.5px);
  box-shadow: inset 0 1px 0 #ffffff, inset 0 0 0 1px rgba(23, 37, 45, 0.2);
}
.var-pill {
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size: 11px;
  padding: 4px 10px;
  border-radius: 8px;
  background: rgba(14, 108, 132, 0.08);
  color: #0e6c84;
  border: 1px solid rgba(14, 108, 132, 0.18);
  cursor: pointer;
  transition: all 0.15s ease;
  user-select: none;
}
.var-pill:hover {
  background: #0e6c84;
  color: #ffffff;
  transform: translateY(-1px);
}
</style>

<div class="kinlo-root space-y-6 pb-12" x-data="{ 
    activeTab: '{{ request('tab', 'smtp') }}',
    testModal: false,
    previewModal: false,
    previewUrl: '',
    selectedTpl: {{ $selectedTemplate ? $selectedTemplate->id : 'null' }},

    copyVar(tag) {
        navigator.clipboard.writeText(tag);
        alert('Variabel ' + tag + ' berhasil disalin ke clipboard!');
    },

    openPreview(url) {
        this.previewUrl = url;
        this.previewModal = true;
    }
}">

    {{-- KINLO HERO BANNER CARD --}}
    <div class="kinlo-card p-6 lg:p-7">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="space-y-1.5 flex-1 min-w-0">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                    <span>Admin</span>
                    <span>&bull;</span>
                    <span class="text-cyan-800">Pemberitahuan Otomatis & Email SPBE</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight whitespace-nowrap shrink-0">
                        📧 Konfigurasi Email SMTP & Template Notifikasi
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $mailSetting->is_active ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-amber-100 text-amber-900 border border-amber-300' }}">
                        {{ $mailSetting->is_active ? '🟢 SMTP AKTIF' : '🟡 HANYA IN-APP WEB' }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 max-w-3xl leading-relaxed">
                    Kelola integrasi server SMTP kedinasan untuk pengiriman surat elektronik resmi, pengujian koneksi langsung, dan kustomisasi balasan email otomatis ke peserta diklat.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="testModal = true" class="kinlo-btn kinlo-btn-ghost bg-amber-50/90 text-amber-950 border border-amber-200 hover:bg-amber-100">
                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>⚡ Uji Koneksi SMTP</span>
                </button>
            </div>
        </div>

        {{-- Top Tab Navigation --}}
        <div class="flex items-center gap-2 mt-6 pt-5 border-t border-slate-200/70 overflow-x-auto">
            <button type="button" @click="activeTab = 'smtp'" 
                    class="kinlo-btn" 
                    :class="activeTab === 'smtp' ? 'kinlo-btn-primary' : 'kinlo-btn-ghost'">
                <span>⚙️ Server SMTP & Diagnostik</span>
            </button>
            <button type="button" @click="activeTab = 'templates'" 
                    class="kinlo-btn" 
                    :class="activeTab === 'templates' ? 'kinlo-btn-primary' : 'kinlo-btn-ghost'">
                <span>📝 Template Balasan Email Peserta (7 Notifikasi KAK)</span>
            </button>
            <button type="button" @click="activeTab = 'broadcast'" 
                    class="kinlo-btn" 
                    :class="activeTab === 'broadcast' ? 'kinlo-btn-primary' : 'kinlo-btn-ghost'">
                <span>📢 Pengingat Webinar & Evaluasi</span>
            </button>
        </div>
    </div>

    {{-- TAB 1: PENGATURAN SERVER SMTP & DIAGNOSTIK --}}
    <div x-show="activeTab === 'smtp'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Form SMTP (2 Kolom) --}}
            <div class="lg:col-span-2 kinlo-card p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Konfigurasi Protokol SMTP</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Parameter koneksi ke Mail Server instansi / Google Workspace / relay pemerintah.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $mailSetting->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                        {{ $mailSetting->is_active ? 'Status: Pengiriman Email Aktif' : 'Status: Pengiriman Email Dimatikan' }}
                    </span>
                </div>

                <form action="{{ route('admin.mail.update-smtp') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Saklar Aktifkan Pengiriman Email --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <label for="is_active_toggle" class="text-xs font-extrabold text-slate-800 uppercase tracking-wider block">Aktifkan Pengiriman Email Keluar</label>
                            <p class="text-[11px] text-slate-500 mt-0.5">Jika nonaktif, notifikasi tetap tersimpan di lonceng web/aplikasi tanpa mengirim email SMTP.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="is_active_toggle" value="1" {{ $mailSetting->is_active ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-800"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Mail Driver / Protocol <span class="text-rose-500">*</span></label>
                            <select name="mail_mailer" required class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-800 shadow-sm">
                                <option value="smtp" {{ $mailSetting->mail_mailer === 'smtp' ? 'selected' : '' }}>SMTP (Simple Mail Transfer Protocol) - Standar</option>
                                <option value="sendmail" {{ $mailSetting->mail_mailer === 'sendmail' ? 'selected' : '' }}>Sendmail (Linux Local Server)</option>
                                <option value="log" {{ $mailSetting->mail_mailer === 'log' ? 'selected' : '' }}>Log Driver (Simulasi / Pengujian Lokal)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">SMTP Host / Server <span class="text-rose-500">*</span></label>
                            <input type="text" name="mail_host" value="{{ old('mail_host', $mailSetting->mail_host) }}" required placeholder="Contoh: smtp.gmail.com atau mail.beraukab.go.id"
                                   class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-medium text-slate-800 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">SMTP Port <span class="text-rose-500">*</span></label>
                            <input type="number" name="mail_port" value="{{ old('mail_port', $mailSetting->mail_port) }}" required placeholder="Contoh: 587 atau 465"
                                   class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-medium text-slate-800 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Enkripsi Keamanan <span class="text-rose-500">*</span></label>
                            <select name="mail_encryption" class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-800 shadow-sm">
                                <option value="tls" {{ $mailSetting->mail_encryption === 'tls' ? 'selected' : '' }}>TLS (Transport Layer Security - Port 587 Rekomendasi)</option>
                                <option value="ssl" {{ $mailSetting->mail_encryption === 'ssl' ? 'selected' : '' }}>SSL (Secure Sockets Layer - Port 465)</option>
                                <option value="none" {{ !$mailSetting->mail_encryption || $mailSetting->mail_encryption === 'none' ? 'selected' : '' }}>Tanpa Enkripsi (None / Port 25)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Username SMTP / Email Akun</label>
                            <input type="text" name="mail_username" value="{{ old('mail_username', $mailSetting->mail_username) }}" placeholder="admin@beraukab.go.id"
                                   class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-medium text-slate-800 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Password / App Password SMTP</label>
                            <input type="password" name="mail_password" placeholder="{{ $mailSetting->mail_password ? '•••••••••••••••• (Tersimpan - Kosongkan jika tidak diubah)' : 'Masukkan kata sandi atau App Password' }}"
                                   class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-medium text-slate-800 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Alamat Email Pengirim (From Email) <span class="text-rose-500">*</span></label>
                            <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $mailSetting->mail_from_address) }}" required placeholder="bkpsdm@beraukab.go.id"
                                   class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-medium text-slate-800 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nama Pengirim Resmi (From Name) <span class="text-rose-500">*</span></label>
                            <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $mailSetting->mail_from_name) }}" required placeholder="LMS KAB Berau - BKPSDM"
                                   class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-medium text-slate-800 shadow-sm">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-[11px] text-slate-400">Pastikan port dan firewall server mengizinkan koneksi keluar ke host SMTP terkait.</p>
                        <button type="submit" class="kinlo-btn kinlo-btn-primary">
                            <span>Simpan Konfigurasi SMTP</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Kartu Riwayat Uji & Status Diagnostik (1 Kolom) --}}
            <div class="space-y-6">
                <div class="kinlo-card p-6">
                    <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>🩺</span>
                        <span>Status Diagnostik Koneksi</span>
                    </h3>

                    <div class="mt-4 space-y-3.5 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px] font-bold uppercase">Terakhir Diuji:</span>
                            <span class="font-bold text-slate-800 font-mono">
                                {{ $mailSetting->last_tested_at ? $mailSetting->last_tested_at->format('d M Y, H:i:s') . ' WITA' : 'Belum pernah diuji' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[11px] font-bold uppercase">Hasil Uji Terakhir:</span>
                            @if($mailSetting->last_test_status === 'success')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold mt-1">
                                    <span>✓ Berhasil Terkirim</span>
                                </span>
                            @elseif($mailSetting->last_test_status === 'failed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-800 border border-rose-200 font-bold mt-1">
                                    <span>✕ Gagal Menghubungi Server</span>
                                </span>
                            @else
                                <span class="text-slate-500 italic">Menunggu pengujian...</span>
                            @endif
                        </div>

                        @if($mailSetting->last_test_message)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] font-mono text-slate-600 break-words leading-relaxed">
                            {{ $mailSetting->last_test_message }}
                        </div>
                        @endif

                        <div class="pt-2">
                            <button type="button" @click="testModal = true" class="w-full kinlo-btn kinlo-btn-primary">
                                <span>Kirim Email Percobaan &rarr;</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Panduan Pengaturan Port & TLS --}}
                <div class="kinlo-card p-5 bg-cyan-900 text-white">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-cyan-200 flex items-center gap-1.5">
                        <span>💡</span>
                        <span>Rekomendasi Port SMTP</span>
                    </h4>
                    <ul class="mt-2.5 space-y-2 text-[11px] text-cyan-100 leading-relaxed">
                        <li>&bull; <strong>Port 587 (TLS):</strong> Standar paling umum dan aman untuk Google Workspace, Microsoft 365, & Mailtrap.</li>
                        <li>&bull; <strong>Port 465 (SSL):</strong> Enkripsi langsung sebelum jabat tangan protokol.</li>
                        <li>&bull; <strong>Google Mail:</strong> Wajib gunakan <em>App Password 16-digit</em> jika akun menggunakan 2FA.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: TEMPLATE BALASAN EMAIL PESERTA (7 SKENARIO KAK) --}}
    <div x-show="activeTab === 'templates'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Navigasi Daftar 7 Template (4 Kolom) --}}
            <div class="lg:col-span-4 kinlo-card p-4 space-y-2">
                <div class="p-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900">7 Skenario Notifikasi KAK</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih template di bawah untuk melihat dan menyesuaikan isi pesan balasan.</p>
                </div>

                <div class="space-y-1.5 pt-1">
                    @foreach($templates as $tpl)
                    @php $isActive = $selectedTemplate && $selectedTemplate->id === $tpl->id; @endphp
                    <a href="{{ route('admin.mail.index', ['tab' => 'templates', 'template_id' => $tpl->id]) }}"
                       class="block p-3 rounded-xl transition-all {{ $isActive ? 'bg-cyan-800 text-white shadow-md' : 'bg-slate-50/70 hover:bg-slate-100/90 text-slate-800' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs truncate">{{ $tpl->name }}</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.2 rounded {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                                {{ $tpl->category }}
                            </span>
                        </div>
                        <p class="text-[11px] mt-1 {{ $isActive ? 'text-cyan-100' : 'text-slate-500' }} line-clamp-2 leading-relaxed">
                            {{ $tpl->description }}
                        </p>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Editor Template Terpilih (8 Kolom) --}}
            <div class="lg:col-span-8 kinlo-card p-6">
                @if($selectedTemplate)
                <div class="border-b border-slate-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-slate-900">{{ $selectedTemplate->name }}</h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-cyan-100 text-cyan-900">
                                Kode: {{ $selectedTemplate->code }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">{{ $selectedTemplate->description }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="openPreview('{{ route('admin.mail.templates.preview', $selectedTemplate) }}')" 
                                class="kinlo-btn kinlo-btn-ghost text-xs">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Pratinjau Email</span>
                        </button>
                    </div>
                </div>

                {{-- Variabel Dinamis Pembantu (Click to Copy) --}}
                <div class="mb-5 p-4 rounded-xl bg-slate-50/90 border border-slate-200/80">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span>🏷️</span>
                            <span>Variabel Otomatis Tersedia (Klik untuk Menyalin):</span>
                        </span>
                        <span class="text-[10px] text-slate-400">Otomatis diganti data riil saat email dikirim</span>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        @if(!empty($selectedTemplate->available_variables))
                            @foreach($selectedTemplate->available_variables as $vTag)
                                <button type="button" @click="copyVar('{{ $vTag }}')" class="var-pill" title="Klik untuk salin">
                                    {{ $vTag }}
                                </button>
                            @endforeach
                        @else
                            <button type="button" @click="copyVar('{nama_peserta}')" class="var-pill">{nama_peserta}</button>
                            <button type="button" @click="copyVar('{nama_kegiatan}')" class="var-pill">{nama_kegiatan}</button>
                            <button type="button" @click="copyVar('{link_kegiatan}')" class="var-pill">{link_kegiatan}</button>
                        @endif
                    </div>
                </div>

                <form action="{{ route('admin.mail.templates.update', $selectedTemplate) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Subjek / Judul Email Masuk <span class="text-rose-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject', $selectedTemplate->subject) }}" required
                               class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-900 shadow-sm">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Isi Balasan Email (Format HTML & Styling Kedinasan) <span class="text-rose-500">*</span></label>
                            <span class="text-[11px] text-slate-400">Mendukung format HTML baku</span>
                        </div>
                        <textarea name="body_html" rows="13" required
                                  class="w-full p-4 font-mono text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 leading-relaxed text-slate-800 shadow-sm">{{ old('body_html', $selectedTemplate->body_html) }}</textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <form action="{{ route('admin.mail.templates.reset', $selectedTemplate) }}" method="POST" onsubmit="return confirm('Kembalikan template ini ke teks standar bawaan sistem?')">
                            @csrf
                            <button type="submit" form="resetForm{{ $selectedTemplate->id }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline">
                                ↺ Kembalikan ke Format Standar
                            </button>
                        </form>

                        <button type="submit" class="kinlo-btn kinlo-btn-primary">
                            <span>Simpan Perubahan Template</span>
                        </button>
                    </div>
                </form>

                {{-- Hidden Reset Form --}}
                <form id="resetForm{{ $selectedTemplate->id }}" action="{{ route('admin.mail.templates.reset', $selectedTemplate) }}" method="POST" class="hidden">
                    @csrf
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- TAB 3: PENGINGAT WEBINAR & EVALUASI (BROADCAST TRIGGER) --}}
    <div x-show="activeTab === 'broadcast'" class="space-y-6">
        <div class="kinlo-card p-6">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h2 class="text-base font-extrabold text-slate-900">Aksi Cepat Pengingat Kegiatan (Broadcast Reminder)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kirimkan notifikasi pengingat H-1 atau Hari-H secara massal kepada peserta kegiatan.</p>
            </div>

            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Agenda Webinar Mendatang</h3>

                @forelse($upcomingWebinars as $wbn)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-900 border border-amber-200">
                                📅 {{ $wbn->event_date?->format('d M Y') }}
                            </span>
                            <span class="text-xs font-bold text-slate-900">{{ $wbn->title }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Pukul: <strong>{{ $wbn->time_start ?: '09:00' }} WITA</strong> &bull; Peserta Terdaftar: <strong>{{ $wbn->registrations()->count() }} orang</strong>
                        </p>
                    </div>

                    <form action="{{ route('admin.mail.send-webinar-reminder', $wbn) }}" method="POST" onsubmit="return confirm('Kirimkan pengingat webinar ini ke seluruh peserta terdaftar?')">
                        @csrf
                        <button type="submit" class="kinlo-btn kinlo-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Blast Pengingat Peserta</span>
                        </button>
                    </form>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-xl">
                    <p class="text-xs font-semibold">Tidak ada agenda webinar mendatang yang memerlukan pengingat saat ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL UJI KONEKSI SMTP LIVE --}}
    <div x-show="testModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4">
        <div @click.away="testModal = false" class="kinlo-card bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚡</span>
                    <h3 class="font-extrabold text-slate-900 text-base">Uji Koneksi Server SMTP</h3>
                </div>
                <button type="button" @click="testModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                Ketik alamat email Anda untuk menerima pesan diagnostik uji coba. Sistem akan melakukan koneksi jabat tangan (*handshake*) ke host <strong>{{ $mailSetting->mail_host }}:{{ $mailSetting->mail_port }}</strong>.
            </p>

            <form action="{{ route('admin.mail.test') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Kirim Email Uji Coba Ke:</label>
                    <input type="email" name="test_email" required value="{{ auth()->user()->email }}" placeholder="nama.anda@beraukab.go.id"
                           class="w-full h-[46px] px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-600 font-semibold text-slate-900 shadow-sm">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="testModal = false" class="kinlo-btn kinlo-btn-ghost text-xs">Batal</button>
                    <button type="submit" class="kinlo-btn kinlo-btn-primary text-xs">
                        <span>Kirim Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL LIVE PREVIEW TEMPLATE --}}
    <div x-show="previewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4">
        <div @click.away="previewModal = false" class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="text-lg">👁️</span>
                    <h3 class="font-extrabold text-slate-900 text-sm">Pratinjau Surat Elektronik (Responsive HTML Preview)</h3>
                </div>
                <button type="button" @click="previewModal = false" class="text-slate-400 hover:text-slate-600 font-bold px-2 py-1">✕</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 bg-slate-100">
                <iframe :src="previewUrl" class="w-full h-[550px] bg-white rounded-xl shadow-inner border border-slate-200"></iframe>
            </div>
        </div>
    </div>

</div>
@endsection

