@extends('layouts.admin')

@section('page_title', 'Modal Popup Beranda')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, activePopup: {} }">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Sistem Modal Popup Beranda</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $popups->count() }} Terdaftar
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Atur modal pop-up promosi / pengumuman yang muncul otomatis saat pengguna mengunjungi beranda LMS.</p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="addModal = true" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Buat Popup Baru</span>
            </button>
        </div>
    </div>

    <!-- ADD POPUP MODAL -->
    <div x-show="addModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="addModal = false" 
             x-show="addModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Buat Popup Pengumuman Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Hanya 1 popup yang akan aktif tayang dalam satu waktu.</p>
                </div>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.popups.store') }}" method="POST" class="p-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Pendaftaran Pelatihan Akreditasi RME 2026 Dibuka!"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Lencana (Badge Tag)</label>
                    <input type="text" name="badge_text" value="PENGUMUMAN PENTING"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Isi Pesan / Maklumat</label>
                    <textarea name="content" rows="3" placeholder="Jelaskan instruksi atau info singkat kepada peserta..."
                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"></textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">URL Gambar / Poster Pop-Up</label>
                        <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                            📐 Rekomendasi: 800 × 450 px (Rasio 16:9) atau 600 × 400 px
                        </span>
                    </div>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau WebP landscape (800×450 px). Tampil di bagian atas jendela dialog pop-up pengumuman.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teks Tombol Aksi <span class="text-red-500">*</span></label>
                        <input type="text" name="button_text" value="Lihat Selengkapnya" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Tombol (URL) <span class="text-red-500">*</span></label>
                        <input type="text" name="button_link" value="/courses" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <span>Jadikan Popup Utama (Aktif)</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button @click="addModal = false" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 transition">
                            Simpan Popup
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Popups List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($popups as $popup)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    @if($popup->image_url)
                        <div class="h-40 w-full bg-slate-100 relative overflow-hidden">
                            <img src="{{ $popup->image_url }}" alt="{{ $popup->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-5 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $popup->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                {{ $popup->is_active ? '✓ Tayang Aktif' : 'Nonaktif' }}
                            </span>
                            @if($popup->badge_text)
                                <span class="text-[10px] font-bold text-amber-600 tracking-wider uppercase">
                                    {{ $popup->badge_text }}
                                </span>
                            @endif
                        </div>

                        <h4 class="font-extrabold text-slate-900 text-sm leading-snug">
                            {{ $popup->title }}
                        </h4>

                        <p class="text-xs text-slate-500 line-clamp-3">
                            {{ $popup->content }}
                        </p>

                        <div class="pt-2 text-[11px] text-slate-600 font-mono">
                            <span class="font-bold text-brand-700">{{ $popup->button_text }}</span> &rarr; {{ $popup->button_link }}
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <form action="{{ route('admin.popups.toggle', $popup->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $popup->is_active ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }} transition">
                            {{ $popup->is_active ? 'Nonaktifkan' : 'Jadikan Aktif' }}
                        </button>
                    </form>

                    <span class="text-[11px] text-slate-400 font-mono">
                        {{ $popup->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 bg-white rounded-2xl border border-slate-200 text-center text-xs text-slate-400">
                Belum ada popup pengumuman dibuat.
            </div>
        @endforelse
    </div>

</div>
@endsection
