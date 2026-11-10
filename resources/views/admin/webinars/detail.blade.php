@extends('layouts.admin')

@section('page_title', 'Detail: ' . Str::limit($webinar->title, 30))

@section('content')
<div class="space-y-6" 
     x-data="{
        activeTab: 'overview',
        addMaterialModal: false,
        materialType: 'document',
     }">

    {{-- ── HEADER WEBINAR ──────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #134e4a 60%, #0f766e 100%);">
        {{-- Cover overlay --}}
        @if($webinar->cover_image)
            <div class="absolute inset-0 opacity-40 bg-cover bg-center"
                 style="background-image: url('{{ $webinar->cover_image }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-900/60 to-transparent"></div>
        @endif
        <div class="relative z-10 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <a href="{{ route('admin.webinars.index') }}"
                           class="text-teal-300 hover:text-white text-xs flex items-center gap-1 transition">
                            ← Kembali ke Daftar
                        </a>
                        <span class="text-slate-600">•</span>
                        @php
                            $statusColor = match($webinar->status) {
                                'ongoing'   => 'bg-emerald-500',
                                'completed' => 'bg-slate-500',
                                default     => 'bg-amber-500',
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold text-white {{ $statusColor }}">
                            {{ $webinar->status_label }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/15 text-white uppercase">
                            {{ str_replace(['_','-'], ' ', $webinar->delivery_mode ?? 'webinar') }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-500/30 text-teal-200 border border-teal-400/40">
                            ⏱️ {{ $webinar->jp ?? 2 }} JP
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight mb-1">
                        {{ $webinar->title }}
                    </h1>
                    <p class="text-teal-200 text-sm">
                        📅 {{ $webinar->event_date->translatedFormat('l, d F Y') }}
                        &bull; ⏰ {{ $webinar->time_start }}–{{ $webinar->time_end }} WIB
                        @if($webinar->location_type)
                            &bull; 📍 {{ $webinar->location_type }}
                        @endif
                    </p>
                </div>
                {{-- Quick stats --}}
                <div class="flex items-center gap-4 text-center shrink-0">
                    @php
                        $totalReg = $webinar->registrations->count();
                        $attended = $webinar->registrations->where('is_attended', true)->count();
                        $evalCount = $webinar->evaluations->count();
                        $avgRating = $evalCount > 0 ? round($webinar->evaluations->avg('rating_overall'), 1) : 0;
                    @endphp
                    <div>
                        <p class="text-2xl font-black text-white">{{ $totalReg }}</p>
                        <p class="text-[10px] text-teal-300 uppercase tracking-wider">Pendaftar</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <p class="text-2xl font-black text-emerald-300">{{ $attended }}</p>
                        <p class="text-[10px] text-teal-300 uppercase tracking-wider">Hadir</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <p class="text-2xl font-black text-cyan-300">{{ $webinar->jp ?? 2 }}</p>
                        <p class="text-[10px] text-teal-300 uppercase tracking-wider">Bobot JP</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $webinar->remaining_quota }}</p>
                        <p class="text-[10px] text-teal-300 uppercase tracking-wider">Sisa Kuota</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <p class="text-2xl font-black text-yellow-300 flex items-center gap-1">
                            {{ $avgRating > 0 ? $avgRating : '—' }}
                            @if($avgRating > 0)<span class="text-base">★</span>@endif
                        </p>
                        <p class="text-[10px] text-teal-300 uppercase tracking-wider">Rating</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TABS ───────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-semibold flex-wrap">
        <button type="button" @click="activeTab = 'overview'"
                :class="activeTab === 'overview' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-1.5">
            <span>📋</span><span>Ikhtisar & Edit</span>
        </button>
        <button type="button" @click="activeTab = 'peserta'"
                :class="activeTab === 'peserta' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-1.5">
            <span>👥</span><span>Peserta & Presensi ({{ $totalReg }})</span>
        </button>
        <button type="button" @click="activeTab = 'materi'"
                :class="activeTab === 'materi' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-1.5">
            <span>📚</span><span>Materi ({{ $webinar->materials->count() }})</span>
        </button>
        <button type="button" @click="activeTab = 'evaluasi'"
                :class="activeTab === 'evaluasi' ? 'bg-teal-700 text-white' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-1.5">
            <span>📊</span><span>Evaluasi ({{ $evalCount }})</span>
        </button>
        <button type="button" @click="activeTab = 'record'"
                :class="activeTab === 'record' ? 'bg-indigo-700 text-white' : 'text-slate-600 hover:bg-slate-100'"
                class="px-4 py-2 rounded-xl transition flex items-center gap-1.5">
            <span>🗂️</span><span>Learning Record</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────── --}}
    {{-- TAB 1: IKHTISAR & EDIT --}}
    {{-- ─────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Edit form --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.webinars.update', $webinar->id) }}" method="POST"
                  class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-5 text-xs">
                @csrf
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900">✏️ Edit Informasi Webinar / Kegiatan</h2>
                </div>

                {{-- Judul & Deskripsi (poin 3) --}}
                <div class="space-y-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Judul Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ $webinar->title }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Deskripsi Kegiatan</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 outline-none"
                                  placeholder="Deskripsi lengkap tujuan dan manfaat mengikuti webinar ini...">{{ $webinar->description }}</textarea>
                    </div>
                </div>

                {{-- Narasumber (poin 4) --}}
                <div class="p-4 bg-indigo-50/60 rounded-2xl border border-indigo-200/80 space-y-3">
                    <p class="font-bold text-indigo-900 flex items-center gap-1.5">🎤 Narasumber / Instruktur</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Narasumber <span class="text-rose-500">*</span></label>
                            <input type="text" name="speaker_name" value="{{ $webinar->speaker_name }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jabatan / Gelar</label>
                            <input type="text" name="speaker_title" value="{{ $webinar->speaker_title }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Instansi / Lembaga</label>
                            <input type="text" name="speaker_institution" value="{{ $webinar->speaker_institution }}"
                                   placeholder="Kementerian / Universitas / OPD..."
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Foto Narasumber (URL)</label>
                            <input type="url" name="speaker_avatar" value="{{ $webinar->speaker_avatar }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Bio / Profil Singkat Narasumber</label>
                        <textarea name="speaker_bio" rows="3"
                                  placeholder="Latar belakang pendidikan, pengalaman, keahlian utama narasumber..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:ring-2 focus:ring-indigo-500">{{ $webinar->speaker_bio }}</textarea>
                    </div>
                </div>

                {{-- Jadwal & Lokasi (poin 5) --}}
                <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200/80 space-y-3">
                    <p class="font-bold text-amber-900 flex items-center gap-1.5">📅 Jadwal & Lokasi</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Kegiatan <span class="text-rose-500">*</span></label>
                            <input type="date" name="event_date" value="{{ $webinar->event_date->format('Y-m-d') }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Mulai (WIB)</label>
                            <input type="time" name="time_start" value="{{ $webinar->time_start }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Selesai (WIB)</label>
                            <input type="time" name="time_end" value="{{ $webinar->time_end }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Lokasi / Platform</label>
                        <input type="text" name="location_type" value="{{ $webinar->location_type }}"
                               placeholder="Zoom / GMeet / Aula Gedung X / Hybrid"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                {{-- Kuota (poin 6) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kuota Peserta <span class="text-rose-500">*</span></label>
                        <input type="number" name="quota" value="{{ $webinar->quota }}" min="1" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Harga (0 = Gratis)</label>
                        <input type="number" name="price" value="{{ $webinar->price }}" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Mode</label>
                        <select name="delivery_mode" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none">
                            @foreach(['online'=>'Online/Webinar','in_class'=>'In-Class','workshop'=>'Workshop','bimtek'=>'Bimtek','seminar'=>'Seminar','hybrid'=>'Hybrid'] as $val => $label)
                                <option value="{{ $val }}" {{ $webinar->delivery_mode === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none">
                            <option value="upcoming" {{ $webinar->status === 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                            <option value="ongoing" {{ $webinar->status === 'ongoing' ? 'selected' : '' }}>Sedang Berlangsung</option>
                            <option value="completed" {{ $webinar->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                {{-- Tautan (poin 7) --}}
                <div class="p-4 bg-teal-50/60 rounded-2xl border border-teal-200/80 space-y-3">
                    <p class="font-bold text-teal-900 flex items-center gap-1.5">🔗 Tautan Kegiatan <span class="text-rose-500 text-xs font-normal">*KAK Poin 7</span></p>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Link Webinar / Zoom / GMeet (hanya peserta terdaftar)</label>
                        <input type="url" name="webinar_url" value="{{ $webinar->webinar_url }}"
                               placeholder="https://zoom.us/j/9876543210?pwd=..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-teal-200 bg-white text-xs outline-none focus:ring-2 focus:ring-teal-500">
                        <p class="text-[11px] text-teal-700 mt-1">Tautan ini hanya akan ditampilkan kepada peserta yang sudah terdaftar dan dikonfirmasi.</p>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Link Pendaftaran Eksternal (opsional)</label>
                        <input type="url" name="registration_link" value="{{ $webinar->registration_link }}"
                               placeholder="https://bit.ly/daftar-webinar"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                {{-- Rekaman (poin 11) --}}
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">🎬 Tautan Rekaman / Video Pasca Kegiatan <span class="text-rose-500 text-xs font-normal">*KAK Poin 11</span></label>
                    <input type="url" name="recording_url" value="{{ $webinar->recording_url }}"
                           placeholder="https://youtube.com/watch?v=... atau Google Drive link"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs outline-none focus:ring-2 focus:ring-brand-500">
                    <p class="text-[11px] text-slate-400 mt-1">Rekaman tersedia setelah webinar selesai. Peserta yang hadir dapat mengakses ulang materi.</p>
                </div>

                {{-- Sertifikat (poin 13) --}}
                <div class="p-4 bg-yellow-50/60 rounded-2xl border border-yellow-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-yellow-900 flex items-center gap-1.5">🏅 Sertifikat Keikutsertaan <span class="text-rose-500 text-xs font-normal">*KAK Poin 13</span></p>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="cert_available" value="1" {{ $webinar->cert_available ? 'checked' : '' }}
                                   class="rounded text-brand-600 focus:ring-brand-500">
                            <span class="text-xs font-semibold text-slate-700">Terbitkan Sertifikat</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Penandatangan</label>
                            <input type="text" name="cert_signatory_name" value="{{ $webinar->cert_signatory_name }}"
                                   placeholder="Dr. H. Ahmad Fauzi, M.Kom"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jabatan Penandatangan</label>
                            <input type="text" name="cert_signatory_role" value="{{ $webinar->cert_signatory_role }}"
                                   placeholder="Kepala Badan Diklat Provinsi"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">NIP</label>
                            <input type="text" name="cert_signatory_nip" value="{{ $webinar->cert_signatory_nip }}"
                                   placeholder="19780512 200312 1 002"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono outline-none">
                        </div>
                    </div>
                </div>

                {{-- Cover & Toggle --}}
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex-1">
                        <label class="block font-semibold text-slate-700 mb-1">URL Gambar Cover</label>
                        <input type="url" name="cover_image" value="{{ $webinar->cover_image }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs outline-none">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ $webinar->is_featured ? 'checked' : '' }}
                               class="rounded text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-semibold text-slate-700">Tampilkan di Beranda</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.webinars.index') }}"
                       class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Info cards --}}
        <div class="space-y-4">
            {{-- Narasumber Card --}}
            <div class="p-5 bg-white rounded-3xl border border-slate-200/90 shadow-2xs text-xs space-y-3">
                <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2">🎤 Narasumber</h3>
                <div class="flex items-center gap-3">
                    @if($webinar->speaker_avatar)
                        <img src="{{ $webinar->speaker_avatar }}" class="w-12 h-12 rounded-full object-cover border-2 border-slate-200" alt="">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-600 to-teal-500 flex items-center justify-center text-white text-lg font-black">
                            {{ strtoupper(substr($webinar->speaker_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-slate-900">{{ $webinar->speaker_name }}</p>
                        <p class="text-slate-500">{{ $webinar->speaker_title }}</p>
                        @if($webinar->speaker_institution)
                            <p class="text-slate-400">{{ $webinar->speaker_institution }}</p>
                        @endif
                    </div>
                </div>
                @if($webinar->speaker_bio)
                    <p class="text-slate-600 leading-relaxed">{{ $webinar->speaker_bio }}</p>
                @endif
            </div>

            {{-- Tautan Cepat --}}
            <div class="p-5 bg-white rounded-3xl border border-slate-200/90 shadow-2xs text-xs space-y-2">
                <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2">🔗 Tautan</h3>
                @if($webinar->webinar_url)
                    <a href="{{ $webinar->webinar_url }}" target="_blank"
                       class="flex items-center gap-2 p-2 bg-teal-50 rounded-xl border border-teal-200 text-teal-800 hover:bg-teal-100 transition">
                        📹 <span class="font-semibold">Buka Link Webinar</span>
                    </a>
                @endif
                @if($webinar->registration_link)
                    <a href="{{ $webinar->registration_link }}" target="_blank"
                       class="flex items-center gap-2 p-2 bg-indigo-50 rounded-xl border border-indigo-200 text-indigo-800 hover:bg-indigo-100 transition">
                        📝 <span class="font-semibold">Link Pendaftaran</span>
                    </a>
                @endif
                @if($webinar->recording_url)
                    <a href="{{ $webinar->recording_url }}" target="_blank"
                       class="flex items-center gap-2 p-2 bg-rose-50 rounded-xl border border-rose-200 text-rose-800 hover:bg-rose-100 transition">
                        🎬 <span class="font-semibold">Tonton Rekaman</span>
                    </a>
                @endif
            </div>

            {{-- Kalender Mini (poin 2) --}}
            <div class="p-5 bg-white rounded-3xl border border-slate-200/90 shadow-2xs text-xs">
                <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3">📅 Kalender Kegiatan</h3>
                @php
                    $evDate = $webinar->event_date;
                    $daysLeft = now()->startOfDay()->diffInDays($evDate->startOfDay(), false);
                @endphp
                <div class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-teal-50 to-emerald-50 rounded-2xl border border-teal-200 text-center">
                    <span class="text-xs text-teal-600 font-bold uppercase tracking-wider">{{ $evDate->translatedFormat('F Y') }}</span>
                    <span class="text-5xl font-black text-teal-800 leading-none my-1">{{ $evDate->format('d') }}</span>
                    <span class="text-xs font-semibold text-teal-700">{{ $evDate->translatedFormat('l') }}</span>
                    <div class="mt-3 pt-3 border-t border-teal-200 w-full">
                        @if($daysLeft > 0)
                            <p class="font-bold text-amber-700">⏳ {{ $daysLeft }} hari lagi</p>
                        @elseif($daysLeft === 0)
                            <p class="font-bold text-emerald-700 animate-pulse">🟢 Hari ini!</p>
                        @else
                            <p class="text-slate-500">✅ Selesai {{ abs($daysLeft) }} hari lalu</p>
                        @endif
                        <p class="text-slate-600 mt-0.5">{{ $webinar->time_start }} – {{ $webinar->time_end }} WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────── --}}
    {{-- TAB 2: PESERTA & PRESENSI DIGITAL (KAK Poin 8 & 9) --}}
    {{-- ─────────────────────────────────────────────────────────── --}}
    @php
        $attSession = $webinar->getOrCreateAttendanceSession();
    @endphp
    <div x-show="activeTab === 'peserta'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span>👥</span>
                    <span>Daftar Peserta & Presensi Digital</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-teal-50 text-teal-800 border border-teal-200">
                        Kode Sesi: {{ $attSession->session_code }}
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $attended }} dari {{ $totalReg }} peserta hadir ({{ $totalReg > 0 ? round(($attended/$totalReg)*100) : 0 }}%) • 
                    Metode: <strong class="uppercase text-slate-700">{{ $attSession->allowed_methods === 'both' ? 'QR Code & Tombol' : $attSession->allowed_methods }}</strong> • 
                    Toleransi: <strong class="text-slate-700">{{ $attSession->tolerance_late_minutes }} Menit</strong>
                </p>
            </div>

            <!-- Action Buttons for Attendance Presentation & Reporting -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.attendances.projector', $attSession) }}" target="_blank"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5"
                   title="Buka QR Code di Layar Penuh / Proyektor Aula & Share Screen">
                    <span>📽️</span>
                    <span>Mode Proyektor QR</span>
                </a>

                <a href="{{ route('admin.attendances.print', $attSession) }}" target="_blank"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5"
                   title="Cetak Lembar Daftar Hadir Ber-Kop Resmi">
                    <span>🖨️</span>
                    <span>Cetak Lembar Hadir</span>
                </a>

                <a href="{{ route('admin.attendances.export', ['session' => $attSession->id]) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition flex items-center gap-1.5"
                   title="Download Rekap CSV Excel">
                    <span>📥 CSV</span>
                </a>

                <form action="{{ route('admin.webinars.attendance-all', $webinar->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Tandai SEMUA peserta sebagai hadir?')">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-sm">
                        ✅ Hadir Semua
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Peserta</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Kode Tiket</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Status Daftar</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Presensi</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Waktu Hadir</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($webinar->registrations as $reg)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-700 to-teal-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($reg->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $reg->user->name }}</p>
                                            <p class="text-slate-400 text-[11px]">{{ $reg->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-600">{{ $reg->ticket_code }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $sc = match($reg->status) { 'confirmed'=>'bg-emerald-100 text-emerald-800', 'cancelled'=>'bg-rose-100 text-rose-800', 'waitlist'=>'bg-amber-100 text-amber-800', default=>'bg-slate-100 text-slate-700' };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sc }}">{{ ucfirst($reg->status) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($reg->is_attended)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✅ Hadir</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">— Belum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $reg->attended_at ? $reg->attended_at->format('H:i, d M') : '—' }}
                                    @if($reg->attendance_method)
                                        <span class="text-[10px] text-slate-400 block">via {{ $reg->attendance_method }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(!$reg->is_attended)
                                        <form action="{{ route('admin.webinars.attendance', $webinar->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $reg->user_id }}">
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                                Tandai Hadir
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-emerald-600 font-semibold text-[11px]">✓ Tercatat</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    <span class="text-4xl block mb-2">👥</span>
                                    <p class="font-semibold">Belum ada peserta terdaftar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────── --}}
    {{-- TAB 3: MATERI (poin 10) --}}
    {{-- ─────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'materi'" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">📚 Materi Pembelajaran</h2>
                <p class="text-xs text-slate-500">Upload atau tambahkan tautan materi yang dibagikan kepada peserta.</p>
            </div>
            <button @click="addMaterialModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                + Tambah Materi
            </button>
        </div>

        {{-- Daftar Materi --}}
        <div class="space-y-3">
            @forelse($webinar->materials as $mat)
                <div class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-sm transition">
                    <span class="text-2xl">{{ $mat->type_icon }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 text-sm truncate">{{ $mat->title }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                                {{ $mat->type === 'video' ? 'bg-blue-100 text-blue-800' : ($mat->type === 'link' ? 'bg-orange-100 text-orange-800' : ($mat->type === 'presentation' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700')) }}">
                                {{ $mat->type }}
                            </span>
                            @if($mat->is_public)
                                <span class="text-[10px] font-bold text-emerald-700">🌐 Publik</span>
                            @else
                                <span class="text-[10px] font-bold text-slate-500">🔒 Peserta saja</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($mat->download_url)
                            <a href="{{ $mat->download_url }}" target="_blank"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200 hover:bg-teal-100 transition">
                                Buka →
                            </a>
                        @endif
                        <form action="{{ route('admin.webinars.materials.destroy', $mat->id) }}" method="POST"
                              onsubmit="return confirm('Hapus materi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center bg-white rounded-3xl border border-slate-200/90">
                    <span class="text-4xl">📄</span>
                    <p class="font-bold text-slate-700 mt-3">Belum ada materi ditambahkan</p>
                    <p class="text-xs text-slate-500 mt-1">Tambahkan dokumen, slide, video, atau tautan materi untuk peserta.</p>
                </div>
            @endforelse
        </div>

        {{-- Modal Tambah Materi --}}
        <div x-show="addMaterialModal" x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="addMaterialModal = false"
                 class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-900">+ Tambah Materi Webinar</h3>
                    <button @click="addMaterialModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <form action="{{ route('admin.webinars.materials.store', $webinar->id) }}" method="POST" class="p-5 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Judul Materi <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Slide Presentasi Sesi 1"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-2">Tipe Materi</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['document'=>'📄 Dokumen','presentation'=>'📊 Presentasi','video'=>'📹 Video','link'=>'🔗 Tautan'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-2 rounded-xl border cursor-pointer transition text-center"
                                       :class="materialType === '{{ $val }}' ? 'bg-teal-50 border-teal-500 ring-2 ring-teal-500/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100'">
                                    <input type="radio" name="type" value="{{ $val }}" x-model="materialType" class="sr-only">
                                    <span class="text-lg">{{ explode(' ', $label)[0] }}</span>
                                    <span class="text-[10px] font-bold mt-0.5">{{ explode(' ', $label)[1] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div x-show="materialType !== 'link'">
                        <label class="block font-semibold text-slate-700 mb-1">URL File (Google Drive / Direct Link)</label>
                        <input type="url" name="file_url" placeholder="https://drive.google.com/..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div x-show="materialType === 'link'">
                        <label class="block font-semibold text-slate-700 mb-1">URL Tautan Eksternal</label>
                        <input type="url" name="external_url" placeholder="https://..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_public" value="1" class="rounded text-brand-600">
                        <span class="font-semibold text-slate-700">Materi dapat diakses publik (tanpa login)</span>
                    </label>
                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button @click="addMaterialModal = false" type="button"
                                class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700">Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────── --}}
    {{-- TAB 4: EVALUASI (poin 12) --}}
    {{-- ─────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'evaluasi'" class="space-y-4">
        <div class="p-6 bg-gradient-to-br from-teal-900 to-teal-700 rounded-3xl text-white shadow-xl">
            <h2 class="text-lg font-black flex items-center gap-2">📊 Evaluasi Reaksi Peserta <span class="text-xs font-normal text-teal-300">KAK Poin 12 - Kirkpatrick Level 1</span></h2>
            <p class="text-teal-200 text-xs mt-1">{{ $evalCount }} peserta telah mengisi evaluasi dari total {{ $totalReg }} pendaftar.</p>
        </div>

        @if($evalCount === 0)
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-200/90">
                <span class="text-5xl">📋</span>
                <p class="font-bold text-slate-700 mt-4">Belum ada evaluasi masuk</p>
                <p class="text-xs text-slate-500 mt-1">Form evaluasi tersedia untuk peserta setelah kegiatan selesai.</p>
            </div>
        @else
            @php
                $ev = $webinar->evaluations();
                $ratings = [
                    'Kualitas Materi'       => ['val' => round($ev->avg('rating_materi'), 1),      'color' => 'blue'],
                    'Kualitas Narasumber'   => ['val' => round($ev->avg('rating_narasumber'), 1),  'color' => 'indigo'],
                    'Kemudahan Platform'    => ['val' => round($ev->avg('rating_platform'), 1),    'color' => 'teal'],
                    'Relevansi Pekerjaan'   => ['val' => round($ev->avg('rating_relevansi'), 1),   'color' => 'emerald'],
                    'Rating Keseluruhan'    => ['val' => round($ev->avg('rating_overall'), 1),     'color' => 'yellow'],
                ];
                $recCount = $ev->where('will_recommend', true)->count();
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-4 text-xs">
                    <h3 class="font-bold text-slate-900">⭐ Rata-Rata Rating</h3>
                    <div class="space-y-3">
                        @foreach($ratings as $label => $data)
                            <div class="flex items-center gap-3">
                                <span class="w-40 text-slate-600 shrink-0">{{ $label }}</span>
                                <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-{{ $data['color'] }}-500 rounded-full"
                                         style="width: {{ ($data['val'] / 5) * 100 }}%"></div>
                                </div>
                                <span class="font-bold text-slate-900 w-10 text-right">{{ $data['val'] }}/5</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="pt-3 border-t border-slate-100">
                        <span class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-[11px]">
                            👍 {{ $recCount }} dari {{ $evalCount }} ({{ $evalCount > 0 ? round(($recCount/$evalCount)*100) : 0 }}%) merekomendasikan
                        </span>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs space-y-3 text-xs">
                    <h3 class="font-bold text-slate-900">💬 Komentar Terbaru</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($webinar->evaluations()->whereNotNull('komentar')->latest()->take(5)->get() as $ev)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="font-bold text-slate-900">{{ $ev->user->name }}</p>
                                    <span class="text-yellow-500 font-mono">{{ str_repeat('★', $ev->rating_overall ?? 0) }}</span>
                                </div>
                                <p class="text-slate-600">{{ $ev->komentar }}</p>
                                @if($ev->saran)
                                    <p class="text-slate-400 italic mt-1">💡 {{ $ev->saran }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ─────────────────────────────────────────────────────────── --}}
    {{-- TAB 5: LEARNING RECORD (poin 14) --}}
    {{-- ─────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'record'" class="space-y-4">
        <div>
            <h2 class="text-sm font-bold text-slate-900">🗂️ Learning Record Peserta</h2>
            <p class="text-xs text-slate-500">Rekaman aktivitas belajar setiap peserta selama kegiatan berlangsung. KAK Poin 14.</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Peserta</th>
                            <th class="text-center px-3 py-3 font-semibold text-slate-600">Daftar</th>
                            <th class="text-center px-3 py-3 font-semibold text-slate-600">Hadir</th>
                            <th class="text-center px-3 py-3 font-semibold text-slate-600">Materi</th>
                            <th class="text-center px-3 py-3 font-semibold text-slate-600">Evaluasi</th>
                            <th class="text-center px-3 py-3 font-semibold text-slate-600">Sertifikat</th>
                            <th class="text-center px-3 py-3 font-semibold text-slate-600">JP</th>
                            <th class="text-center px-3 py-3 font-semibold text-slate-600">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($webinar->learningRecords as $lr)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-900">{{ $lr->user->name }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $lr->user->email }}</p>
                                </td>
                                @foreach(['registered','attended','material_downloaded','evaluation_submitted','certificate_issued'] as $field)
                                    <td class="px-3 py-3 text-center">
                                        @if($lr->$field)
                                            <span class="text-emerald-600 text-base">✅</span>
                                        @else
                                            <span class="text-slate-300 text-base">○</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-3 text-center font-bold text-teal-700">{{ $lr->jp_earned }}</td>
                                <td class="px-3 py-3 text-center">
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-teal-500 rounded-full"
                                                 style="width: {{ $lr->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-600 w-8">{{ $lr->progress_percent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                    <span class="text-4xl block mb-2">🗂️</span>
                                    <p class="font-semibold">Belum ada learning record tercatat</p>
                                    <p class="text-[11px] mt-1">Record otomatis dibuat saat peserta mendaftar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
