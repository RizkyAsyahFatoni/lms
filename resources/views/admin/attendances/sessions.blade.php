@extends('layouts.admin')

@section('page_title', 'Kelola Sesi & Konfigurasi Presensi')

@section('content')
<div class="space-y-6" x-data="{
    createModal: false,
    editModal: false,
    manualModal: false,
    selectedSession: { id: null, title: '', session_code: '', date: '', time_start: '', time_end: '', tolerance_late_minutes: 15, min_duration_minutes: 60, allowed_methods: 'both', is_active: true, notes: '' },
    manualSession: { id: null, title: '', code: '' }
}">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Sesi Kegiatan & Konfigurasi Presensi</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $sessions->total() }} Sesi
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Konfigurasi jendela waktu buka/tutup presensi, QR Code dinamis, batas toleransi menit, dan durasi minimal kehadiran.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.attendances.index') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>📋</span>
                <span>Rekap Kehadiran</span>
            </a>
            <button @click="createModal = true" 
                    class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Sesi Presensi Baru</span>
            </button>
        </div>
    </div>

    <!-- Sessions Grid / List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($sessions as $sess)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition">
                <div class="space-y-3">
                    <!-- Session Status & Code -->
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-700">
                            {{ $sess->session_code }}
                        </span>
                        @if($sess->isOpen())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Presensi DIBUKA
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                🔒 DITUTUP
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug line-clamp-2" title="{{ $sess->title }}">
                        {{ $sess->title }}
                    </h3>

                    <!-- Date & Time Info -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1 text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Tanggal:</span>
                            <span class="font-bold text-slate-800">{{ $sess->date ? $sess->date->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Waktu Acara:</span>
                            <span class="font-semibold text-slate-800">{{ $sess->time_start ?: '08:00' }} - {{ $sess->time_end ?: '17:00' }} WIB</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Toleransi Keterlambatan:</span>
                            <span class="font-semibold text-amber-700">{{ $sess->tolerance_late_minutes }} Menit</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Durasi Minimal Kehadiran:</span>
                            <span class="font-semibold text-teal-700">{{ $sess->min_duration_minutes }} Menit</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Metode Diizinkan:</span>
                            <span class="font-semibold uppercase text-brand-700">
                                {{ $sess->allowed_methods === 'both' ? 'QR & Tombol' : ($sess->allowed_methods === 'qr_code' ? 'Hanya QR' : 'Hanya Tombol') }}
                            </span>
                        </div>
                    </div>

                    <!-- Attendance Count -->
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs text-slate-500">Total Hadir:</span>
                        <a href="{{ route('admin.attendances.index', ['session_id' => $sess->id]) }}" 
                           class="text-xs font-black text-brand-700 hover:underline">
                            {{ $sess->attendances_count }} Peserta &rarr;
                        </a>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <!-- Mode Proyektor QR -->
                        <a href="{{ route('admin.attendances.projector', $sess) }}" target="_blank"
                           class="px-2.5 py-1.5 rounded-xl bg-slate-900 text-white font-bold text-center hover:bg-brand-700 transition flex items-center justify-center gap-1">
                            <span>📽️</span>
                            <span>Proyektor QR</span>
                        </a>

                        <!-- Input Manual Panitia -->
                        <button @click="manualSession = { id: {{ $sess->id }}, title: '{{ addslashes($sess->title) }}', code: '{{ $sess->session_code }}' }; manualModal = true"
                                type="button"
                                class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition flex items-center justify-center gap-1">
                            <span>✍️</span>
                            <span>Input Manual</span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between gap-1 text-[11px] pt-1">
                        <!-- Toggle Open/Close -->
                        <form action="{{ route('admin.attendances.sessions.toggle', $sess) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-2 py-1 rounded-lg font-bold transition {{ $sess->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                {{ $sess->is_active ? 'Tutup Sesi' : 'Buka Sesi' }}
                            </button>
                        </form>

                        <div class="flex items-center gap-1">
                            <!-- Cetak Daftar Hadir -->
                            <a href="{{ route('admin.attendances.print', $sess) }}" target="_blank"
                               class="p-1 rounded-lg text-slate-500 hover:bg-slate-100" title="Cetak Lembar Daftar Hadir">
                                🖨️
                            </a>

                            <!-- Ekspor CSV -->
                            <a href="{{ route('admin.attendances.export', ['session' => $sess->id]) }}" 
                               class="p-1 rounded-lg text-slate-500 hover:bg-slate-100" title="Ekspor CSV Sesi Ini">
                                📥
                            </a>

                            <!-- Edit Konfigurasi -->
                            <button @click="selectedSession = {
                                        id: {{ $sess->id }},
                                        title: '{{ addslashes($sess->title) }}',
                                        session_code: '{{ $sess->session_code }}',
                                        date: '{{ $sess->date ? $sess->date->format('Y-m-d') : '' }}',
                                        time_start: '{{ $sess->time_start ?: '' }}',
                                        time_end: '{{ $sess->time_end ?: '' }}',
                                        tolerance_late_minutes: {{ $sess->tolerance_late_minutes }},
                                        min_duration_minutes: {{ $sess->min_duration_minutes }},
                                        allowed_methods: '{{ $sess->allowed_methods }}',
                                        is_active: {{ $sess->is_active ? 'true' : 'false' }},
                                        notes: '{{ addslashes($sess->notes ?? '') }}'
                                    }; editModal = true"
                                    class="p-1 rounded-lg text-slate-500 hover:bg-slate-100" title="Edit Konfigurasi">
                                ⚙️
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center text-xs text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
                Belum ada sesi presensi kegiatan yang dibuat. Klik tombol "Buat Sesi Presensi Baru" untuk memulai.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
        {{ $sessions->links() }}
    </div>

    <!-- MODAL BUAT SESI PRESENSI BARU -->
    <div x-show="createModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="createModal = false" 
             x-show="createModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Buat Sesi Presensi Digital Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Konfigurasi kegiatan, metode presensi, toleransi, dan durasi minimal.</p>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.attendances.sessions.store') }}" method="POST" class="p-5 space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Kegiatan / Sesi <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Webinar Strategi Digitalisasi Pelayanan Publik 2026"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Tautkan ke Webinar (Opsional)</label>
                        <select name="attendable_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                            <option value="">-- Kegiatan Mandiri / Umum --</option>
                            @foreach($webinars as $w)
                                <option value="{{ $w->id }}">{{ $w->title }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="attendable_type" value="App\Models\Webinar">
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Waktu Mulai</label>
                        <input type="time" name="time_start" value="08:30"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Waktu Selesai</label>
                        <input type="time" name="time_end" value="12:00"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Metode Presensi <span class="text-red-500">*</span></label>
                        <select name="allowed_methods" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold focus:bg-white transition">
                            <option value="both">📱 QR Code & 🖱️ Tombol</option>
                            <option value="qr_code">📱 QR Code Saja</option>
                            <option value="button">🖱️ Tombol Saja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Toleransi Terlambat</label>
                        <div class="relative">
                            <input type="number" name="tolerance_late_minutes" value="15" min="0" max="180" required
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition pr-12 font-bold text-slate-800">
                            <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold">MENIT</span>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Durasi Minimal Hadir</label>
                        <div class="relative">
                            <input type="number" name="min_duration_minutes" value="45" min="0" max="480" required
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition pr-12 font-bold text-slate-800">
                            <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold">MENIT</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                    <label for="create_is_active" class="text-xs font-semibold text-slate-700">Langsung aktifkan sesi presensi (peserta dapat langsung presensi)</label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="createModal = false" type="button" class="px-3.5 py-1.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        Simpan Sesi Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT KONFIGURASI SESI -->
    <div x-show="editModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="editModal = false" 
             class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Konfigurasi Sesi Presensi</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="selectedSession.title + ' (' + selectedSession.session_code + ')'"></p>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/attendance-sessions/' + selectedSession.id" method="POST" class="p-5 space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="selectedSession.title" required
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="date" x-model="selectedSession.date" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Metode Presensi</label>
                        <select name="allowed_methods" x-model="selectedSession.allowed_methods" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold focus:bg-white transition">
                            <option value="both">📱 QR Code & 🖱️ Tombol</option>
                            <option value="qr_code">📱 QR Code Saja</option>
                            <option value="button">🖱️ Tombol Saja</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Toleransi Terlambat (Menit)</label>
                        <input type="number" name="tolerance_late_minutes" x-model="selectedSession.tolerance_late_minutes" required min="0"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Durasi Minimal Kehadiran (Menit)</label>
                        <input type="number" name="min_duration_minutes" x-model="selectedSession.min_duration_minutes" required min="0"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" x-model="selectedSession.is_active" class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                    <label for="edit_is_active" class="text-xs font-semibold text-slate-700">Sesi Presensi Aktif (Buka Presensi)</label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="editModal = false" type="button" class="px-3.5 py-1.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL INPUT PRESENSI MANUAL OLEH PANITIA -->
    <div x-show="manualModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="manualModal = false" 
             class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Input Presensi Manual Panitia</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="manualSession.title"></p>
                </div>
                <button @click="manualModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/attendance-sessions/' + manualSession.id + '/manual'" method="POST" class="p-5 space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Pilih Peserta ASN <span class="text-red-500">*</span></label>
                    <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition font-medium">
                        <option value="">-- Pilih Peserta --</option>
                        @foreach(\App\Models\User::orderBy('name')->take(50)->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} (NIP: {{ $u->nip ?: '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold focus:bg-white transition">
                            <option value="present">🟢 Hadir Tepat Waktu</option>
                            <option value="late">🟡 Terlambat</option>
                            <option value="excused">🔵 Izin Dinas</option>
                            <option value="sick">🟣 Sakit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Durasi Hadir (Menit)</label>
                        <input type="number" name="duration_minutes" value="60" min="0" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan Verifikasi Panitia</label>
                    <textarea name="notes" rows="2" placeholder="Keterangan hadir manual / dispensasi dinas..."
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="manualModal = false" type="button" class="px-3.5 py-1.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        Simpan Presensi Manual
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
