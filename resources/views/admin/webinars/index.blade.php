@extends('layouts.admin')

@section('page_title', 'Agenda & Workshop In-Class')

@section('content')
<div class="space-y-6" 
     x-data="{ 
        addModal: false, 
        editModal: false, 
        activeFilter: 'all',
        selectedCourseId: '',
        coursesData: @js($courses->map(fn($c) => [
            'id' => $c->id,
            'title' => $c->title,
            'speaker_name' => $c->instructor->name ?? '',
            'speaker_title' => $c->instructor->headline ?? '',
            'price' => (int) $c->price,
            'cover_image' => $c->thumbnail ? (str_starts_with($c->thumbnail, 'http') || str_starts_with($c->thumbnail, '/') ? $c->thumbnail : asset('storage/' . $c->thumbnail)) : '',
            'delivery_mode' => in_array($c->course_type, ['in-class', 'in_class']) ? 'in_class' : ($c->course_type === 'workshop' ? 'workshop' : ($c->course_type === 'bimtek' ? 'bimtek' : ($c->course_type === 'seminar' ? 'seminar' : ($c->course_type === 'hybrid' ? 'hybrid' : 'online'))))
        ])),
        activeWebinar: {},
        fillFromCourse(courseId) {
            const course = this.coursesData.find(c => c.id == courseId);
            if (course) {
                this.$refs.webinarTitle.value = course.title;
                this.$refs.webinarSpeaker.value = course.speaker_name;
                this.$refs.webinarSpeakerTitle.value = course.speaker_title;
                this.$refs.webinarPrice.value = course.price;
                if (course.cover_image) {
                    this.$refs.webinarCover.value = course.cover_image;
                }
                const deliverySelect = this.$refs.webinarDelivery;
                if (deliverySelect) {
                    deliverySelect.value = course.delivery_mode;
                }
            }
        }
     }">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen Agenda & Workshop</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-teal-50 text-teal-700 border border-teal-200">
                    {{ $webinars->total() }} Agenda
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Kelola sesi Pelatihan Tatap Muka (In-Class, Workshop, Bimtek, Seminar) dan Live Webinar daring yang terintegrasi presensi.</p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="addModal = true; selectedCourseId = ''" 
                    type="button"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Buat Agenda / Workshop Baru</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- SEGMENTED TABS: IN-CLASS VS E-COURSE WEBINAR -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        <button @click="activeFilter = 'all'" 
                :class="activeFilter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>📋 Semua Agenda</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                {{ $webinars->total() }}
            </span>
        </button>

        <button @click="activeFilter = 'in_class'" 
                :class="activeFilter === 'in_class' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>🏛️ In-Class / Tatap Muka (Workshop, Bimtek, Seminar)</span>
        </button>

        <button @click="activeFilter = 'online'" 
                :class="activeFilter === 'online' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>📹 E-Course (Live Webinar Zoom / Live)</span>
        </button>
    </div>

    <!-- ADD WEBINAR / AGENDA MODAL -->
    <div x-show="addModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="addModal = false" 
             x-show="addModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-2xl rounded-2xl shadow-xl border border-slate-200 overflow-hidden max-h-[92vh] flex flex-col">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Publikasikan Agenda / Workshop Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Bisa mengambil data dari kursus yang sudah dibuat atau membuat langsung khusus in-class.</p>
                </div>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.webinars.store') }}" method="POST" class="p-5 space-y-4 overflow-y-auto">
                @csrf

                <!-- Opsi Ambil dari Kursus LMS -->
                <div class="p-3.5 bg-indigo-50/70 border border-indigo-200/80 rounded-xl space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-indigo-900">
                        ⚡ Ambil Data dari Kursus yang Ada (Opsional)
                    </label>
                    <select name="course_id" x-model="selectedCourseId" @change="fillFromCourse($event.target.value)"
                            class="w-full px-3 py-2 text-xs bg-white border border-indigo-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-semibold">
                        <option value="">-- Buat Mandiri / Tanpa Kursus --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">
                                [{{ $c->course_type_label }}] {{ $c->title }} ({{ $c->instructor->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-indigo-600 font-medium">Memilih kursus akan otomatis mengisi judul, nama pemateri, format, dan cover agenda.</span>
                </div>

                <!-- Format Penyelenggaraan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Format Penyelenggaraan Acara <span class="text-red-500">*</span>
                    </label>
                    <select name="delivery_mode" x-ref="webinarDelivery" required
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold text-slate-800">
                        <optgroup label="🏛️ Pelatihan In-Class & Tatap Muka">
                            <option value="in_class">🏢 Pelatihan Tatap Muka (Dalam Ruangan / Kelas)</option>
                            <option value="workshop" selected>🛠️ Workshop In-Class (Praktik Interaktif)</option>
                            <option value="bimtek">📑 Bimbingan Teknis (Bimtek Tatap Muka)</option>
                            <option value="seminar">🎤 Seminar Tatap Muka</option>
                            <option value="hybrid">🌐 Pelatihan Hybrid (Daring & Tatap Muka)</option>
                        </optgroup>
                        <optgroup label="💻 E-Course & Daring">
                            <option value="online">📹 Live Webinar Daring (Zoom / YouTube Live)</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Agenda / Workshop <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-ref="webinarTitle" required placeholder="Contoh: Workshop In-Class: Penguatan Kompetensi Aparatur Kabupaten Berau"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Singkat / Silabus Pembelajaran</label>
                    <textarea name="description" rows="2" placeholder="Uraikan ringkasan kegiatan, tujuan pembelajaran, dan sasaran peserta..."
                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Pemateri / Fasilitator <span class="text-red-500">*</span></label>
                        <input type="text" name="speaker_name" x-ref="webinarSpeaker" required placeholder="dr. Raditya Pratama, Sp.A"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Gelar / Jabatan Pemateri</label>
                        <input type="text" name="speaker_title" x-ref="webinarSpeakerTitle" placeholder="Lead Solution Architect / Widyaiswara Ahli"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Asal Instansi Narasumber</label>
                        <input type="text" name="speaker_institution" placeholder="Kementerian Kesehatan RI / RSUP Sanglah"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">URL Foto / Avatar Pemateri</label>
                        <input type="url" name="speaker_avatar" placeholder="https://..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                        <input type="date" name="event_date" value="{{ now()->addDays(7)->toDateString() }}" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="text" name="time_start" value="08:30" required placeholder="08:30"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="text" name="time_end" value="16:00" required placeholder="16:00"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Lokasi Ruangan / Nama Gedung <span class="text-red-500">*</span></label>
                        <input type="text" name="location_type" value="Ruang Workshop Lantai 3 / Zoom Online" required
                               placeholder="Contoh: Hotel Grand Sahid Lt. 2 / Zoom Meeting"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Live Webinar (Zoom / Meet / YouTube)</label>
                        <input type="url" name="webinar_url" placeholder="https://zoom.us/j/12345678"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Registrasi Eksternal (Opsional)</label>
                        <input type="url" name="registration_link" placeholder="https://form.kemkes.go.id/..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Rekaman / Video Setelah Kegiatan</label>
                        <input type="url" name="recording_url" placeholder="https://youtube.com/watch?v=..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">URL Cover / Poster Acara</label>
                        <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                            📐 Rekomendasi: 1280 × 720 px (Rasio 16:9 Landscape)
                        </span>
                    </div>
                    <input type="url" name="cover_image" x-ref="webinarCover" placeholder="https://images.unsplash.com/photo-..."
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <span class="text-[10px] text-slate-400">Format: JPG, PNG, atau WebP (1280×720 px atau 1920×1080 px). Tampil sebagai poster acara dan thumbnail agenda diklat.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kuota Peserta <span class="text-red-500">*</span></label>
                        <input type="number" name="quota" value="100" min="1" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Pelajaran (JP) <span class="text-red-500">*</span></label>
                        <input type="number" name="jp" value="2" min="1" max="100" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold text-teal-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Biaya Registrasi (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" x-ref="webinarPrice" value="0" min="0" step="1000" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Status Publikasi <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-semibold">
                            <option value="upcoming" selected>Akan Datang (Upcoming)</option>
                            <option value="ongoing">Sedang Berlangsung (Live)</option>
                            <option value="completed">Selesai (Completed)</option>
                        </select>
                    </div>
                </div>

                <!-- Penerbitan Sertifikat (KAK Poin 13) -->
                <div class="p-3 bg-teal-50/70 border border-teal-200/80 rounded-xl space-y-2.5">
                    <label class="flex items-center gap-2 text-xs font-bold text-teal-900 cursor-pointer">
                        <input type="checkbox" name="cert_available" value="1" checked class="rounded border-teal-300 text-teal-600 focus:ring-teal-500">
                        <span>🏅 Terbitkan E-Sertifikat untuk Peserta Hadir (Otomatis Learning Record)</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-teal-800 mb-0.5">Nama Penandatangan</label>
                            <input type="text" name="cert_signatory_name" placeholder="Dr. H. Ahmad Fauzi, M.Kes"
                                   class="w-full px-2.5 py-1.5 text-xs bg-white border border-teal-200 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-teal-800 mb-0.5">Jabatan Penandatangan</label>
                            <input type="text" name="cert_signatory_role" placeholder="Kepala Pusat Pengembangan SDM"
                                   class="w-full px-2.5 py-1.5 text-xs bg-white border border-teal-200 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-teal-800 mb-0.5">NIP Penandatangan</label>
                            <input type="text" name="cert_signatory_nip" placeholder="19820415 200801 1 005"
                                   class="w-full px-2.5 py-1.5 text-xs bg-white border border-teal-200 rounded-lg focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Sorot di Beranda (Featured)</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button @click="addModal = false" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                            Publikasikan Agenda
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT WEBINAR MODAL -->
    <div x-show="editModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="editModal = false" 
             x-show="editModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-2xl rounded-2xl shadow-xl border border-slate-200 overflow-hidden max-h-[92vh] flex flex-col">
            
            <div class="p-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Edit Agenda / Workshop</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui jadwal, narasumber, atau tautan presensi/pertemuan.</p>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/admin/webinars/' + activeWebinar.id" method="POST" class="p-5 space-y-4 overflow-y-auto">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Hubungkan ke Kursus LMS (Opsional)</label>
                    <select name="course_id" x-model="activeWebinar.course_id"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-semibold">
                        <option value="">-- Tanpa Relasi Kursus --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Format Penyelenggaraan <span class="text-red-500">*</span></label>
                    <select name="delivery_mode" x-model="activeWebinar.delivery_mode" required
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                        <optgroup label="🏛️ Pelatihan In-Class & Tatap Muka">
                            <option value="in_class">🏢 Pelatihan Tatap Muka (Dalam Ruangan / Kelas)</option>
                            <option value="workshop">🛠️ Workshop In-Class (Praktik Interaktif)</option>
                            <option value="bimtek">📑 Bimbingan Teknis (Bimtek Tatap Muka)</option>
                            <option value="seminar">🎤 Seminar Tatap Muka</option>
                            <option value="hybrid">🌐 Pelatihan Hybrid (Daring & Tatap Muka)</option>
                        </optgroup>
                        <optgroup label="💻 E-Course & Daring">
                            <option value="online">📹 Live Webinar Daring (Zoom / YouTube Live)</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Agenda / Workshop <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="activeWebinar.title" required
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Singkat / Silabus Pembelajaran</label>
                    <textarea name="description" x-model="activeWebinar.description" rows="2" placeholder="Uraikan ringkasan kegiatan, tujuan pembelajaran, dan sasaran peserta..."
                              class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Pemateri <span class="text-red-500">*</span></label>
                        <input type="text" name="speaker_name" x-model="activeWebinar.speaker_name" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Gelar / Jabatan Pemateri</label>
                        <input type="text" name="speaker_title" x-model="activeWebinar.speaker_title"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Asal Instansi Narasumber</label>
                        <input type="text" name="speaker_institution" x-model="activeWebinar.speaker_institution" placeholder="Kementerian Kesehatan RI / RSUP Sanglah"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">URL Foto / Avatar Pemateri</label>
                        <input type="url" name="speaker_avatar" x-model="activeWebinar.speaker_avatar" placeholder="https://..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Acara <span class="text-red-500">*</span></label>
                        <input type="date" name="event_date" x-model="activeWebinar.event_date" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="text" name="time_start" x-model="activeWebinar.time_start" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="text" name="time_end" x-model="activeWebinar.time_end" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Lokasi Ruangan / Nama Gedung <span class="text-red-500">*</span></label>
                        <input type="text" name="location_type" x-model="activeWebinar.location_type" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Live Webinar (Zoom / Meet)</label>
                        <input type="url" name="webinar_url" x-model="activeWebinar.webinar_url" placeholder="https://zoom.us/j/..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Registrasi Eksternal</label>
                        <input type="url" name="registration_link" x-model="activeWebinar.registration_link"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tautan Rekaman / Video</label>
                        <input type="url" name="recording_url" x-model="activeWebinar.recording_url" placeholder="https://youtube.com/..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">URL Cover Poster</label>
                        <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                            📐 Rekomendasi: 1280 × 720 px (Rasio 16:9 Landscape)
                        </span>
                    </div>
                    <input type="url" name="cover_image" x-model="activeWebinar.cover_image"
                           class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kuota <span class="text-red-500">*</span></label>
                        <input type="number" name="quota" x-model="activeWebinar.quota" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Pelajaran (JP) <span class="text-red-500">*</span></label>
                        <input type="number" name="jp" x-model="activeWebinar.jp" min="1" max="100" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold text-teal-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Biaya (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" x-model="activeWebinar.price" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" x-model="activeWebinar.status" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-semibold">
                            <option value="upcoming">Akan Datang (Upcoming)</option>
                            <option value="ongoing">Sedang Berlangsung (Live)</option>
                            <option value="completed">Selesai (Completed)</option>
                        </select>
                    </div>
                </div>

                <!-- Penerbitan Sertifikat (KAK Poin 13) -->
                <div class="p-3 bg-teal-50/70 border border-teal-200/80 rounded-xl space-y-2.5">
                    <label class="flex items-center gap-2 text-xs font-bold text-teal-900 cursor-pointer">
                        <input type="checkbox" name="cert_available" value="1" x-model="activeWebinar.cert_available" class="rounded border-teal-300 text-teal-600 focus:ring-teal-500">
                        <span>🏅 Terbitkan E-Sertifikat untuk Peserta Hadir (Otomatis Learning Record)</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1" x-show="activeWebinar.cert_available">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-teal-800 mb-0.5">Nama Penandatangan</label>
                            <input type="text" name="cert_signatory_name" x-model="activeWebinar.cert_signatory_name" placeholder="Dr. H. Ahmad Fauzi, M.Kes"
                                   class="w-full px-2.5 py-1.5 text-xs bg-white border border-teal-200 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-teal-800 mb-0.5">Jabatan Penandatangan</label>
                            <input type="text" name="cert_signatory_role" x-model="activeWebinar.cert_signatory_role" placeholder="Kepala Puslat SDM"
                                   class="w-full px-2.5 py-1.5 text-xs bg-white border border-teal-200 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-teal-800 mb-0.5">NIP Penandatangan</label>
                            <input type="text" name="cert_signatory_nip" x-model="activeWebinar.cert_signatory_nip" placeholder="19820415 200801 1 005"
                                   class="w-full px-2.5 py-1.5 text-xs bg-white border border-teal-200 rounded-lg focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button @click="editModal = false" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Webinars & Agenda Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Jadwal Workshop In-Class & Webinar</h3>
            <span class="text-xs text-slate-500 font-mono">Total {{ $webinars->total() }} Sesi Terjadwal</span>
        </div>

        @if($webinars->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">Belum ada agenda webinar / workshop terdaftar.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Tanggal & Waktu</th>
                            <th class="py-3 px-4">Judul & Format Kegiatan</th>
                            <th class="py-3 px-4">Narasumber</th>
                            <th class="py-3 px-4">Metode / Lokasi</th>
                            <th class="py-3 px-4">Kuota / Biaya</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($webinars as $wb)
                            @php
                                $deliveryMode = $wb->delivery_mode ?? ($wb->location_type && str_contains(strtolower($wb->location_type), 'zoom') ? 'online' : 'in_class');
                                $isOnline = $deliveryMode === 'online';
                            @endphp

                            <tr class="hover:bg-slate-50/70 transition"
                                x-show="activeFilter === 'all' || (activeFilter === 'in_class' && '{{ $deliveryMode }}' !== 'online') || (activeFilter === 'online' && '{{ $deliveryMode }}' === 'online')">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-xl {{ $isOnline ? 'bg-indigo-50 border-indigo-100 text-indigo-700' : 'bg-amber-50 border-amber-100 text-amber-700' }} border flex flex-col items-center justify-center font-bold">
                                            <span class="text-[9px] uppercase leading-none">{{ $wb->event_date->format('M') }}</span>
                                            <span class="text-sm leading-none mt-0.5">{{ $wb->event_date->format('d') }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $wb->event_date->format('Y') }}</div>
                                            <div class="text-[10px] text-slate-500 font-mono">{{ $wb->time_start }} - {{ $wb->time_end }} WIB</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                        @if($deliveryMode === 'online')
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                📹 Live Webinar (E-Course)
                                            </span>
                                        @elseif($deliveryMode === 'workshop')
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
                                                🛠️ Workshop In-Class
                                            </span>
                                        @elseif($deliveryMode === 'bimtek')
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-teal-50 text-teal-800 border border-teal-200">
                                                📑 Bimtek In-Class
                                            </span>
                                        @elseif($deliveryMode === 'seminar')
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-purple-50 text-purple-800 border border-purple-200">
                                                🎤 Seminar Tatap Muka
                                            </span>
                                        @elseif($deliveryMode === 'hybrid')
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-sky-50 text-sky-800 border border-sky-200">
                                                🌐 Pelatihan Hybrid
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                🏢 Tatap Muka (In-Class)
                                            </span>
                                        @endif

                                        @if($wb->course)
                                            <span class="text-[9px] font-semibold text-slate-400">
                                                • Terhubung: <a href="{{ route('courses.show', $wb->course->slug) }}" target="_blank" class="text-brand-600 hover:underline">{{ Str::limit($wb->course->title, 20) }}</a>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="font-extrabold text-slate-900 leading-snug line-clamp-2">{{ $wb->title }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $wb->speaker_name }}</div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-[180px]">{{ $wb->speaker_title ?: '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-medium text-slate-700 block truncate max-w-[160px]" title="{{ $wb->location_type }}">
                                        {{ $wb->location_type }}
                                    </span>
                                    @if($wb->registration_link)
                                        <a href="{{ $wb->registration_link }}" target="_blank" class="text-[10px] text-indigo-600 hover:underline flex items-center gap-1 mt-0.5">
                                            <span>Link Ruang</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">
                                        <span class="text-teal-700 font-bold">{{ $wb->registrations_count ?? $wb->registrations->count() }}</span>
                                        <span class="text-slate-400">/</span> {{ number_format($wb->quota) }} Peserta
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        @if(($wb->materials_count ?? 0) > 0)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 font-medium">📄 {{ $wb->materials_count }} materi</span>
                                        @endif
                                        @if($wb->recording_url)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-purple-50 text-purple-700 font-medium">📹 Rekaman</span>
                                        @endif
                                        <span class="text-[9px] px-1.5 py-0.2 rounded bg-teal-50 text-teal-700 font-bold border border-teal-200">⏱️ {{ $wb->jp ?? 2 }} JP</span>
                                        @if($wb->cert_available)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 font-medium">🏅 E-Sertifikat</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] {{ $wb->price > 0 ? 'font-bold text-slate-900' : 'text-emerald-600 font-bold' }} mt-0.5">
                                        {{ $wb->price > 0 ? 'Rp ' . number_format($wb->price, 0, ',', '.') : 'Gratis' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($wb->status === 'upcoming')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Akan Datang</span>
                                    @elseif($wb->status === 'ongoing')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse">Sedang Live</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Selesai</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.webinars.detail', $wb->id) }}"
                                           class="px-3 py-1.5 rounded-lg text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white transition shadow-2xs flex items-center gap-1">
                                            <span>🎯 Kelola</span>
                                        </a>

                                        <button @click="activeWebinar = { 
                                                    id: {{ $wb->id }}, 
                                                    course_id: '{{ $wb->course_id }}',
                                                    delivery_mode: '{{ $deliveryMode }}',
                                                    title: '{{ addslashes($wb->title) }}', 
                                                    description: '{{ addslashes($wb->description ?? '') }}',
                                                    speaker_name: '{{ addslashes($wb->speaker_name) }}', 
                                                    speaker_title: '{{ addslashes($wb->speaker_title ?? '') }}', 
                                                    speaker_institution: '{{ addslashes($wb->speaker_institution ?? '') }}',
                                                    speaker_avatar: '{{ addslashes($wb->speaker_avatar ?? '') }}',
                                                    event_date: '{{ $wb->event_date->format('Y-m-d') }}', 
                                                    time_start: '{{ $wb->time_start }}', 
                                                    time_end: '{{ $wb->time_end }}', 
                                                    location_type: '{{ addslashes($wb->location_type) }}', 
                                                    webinar_url: '{{ addslashes($wb->webinar_url ?? '') }}',
                                                    registration_link: '{{ addslashes($wb->registration_link ?? '') }}', 
                                                    recording_url: '{{ addslashes($wb->recording_url ?? '') }}',
                                                    cover_image: '{{ addslashes($wb->cover_image ?? '') }}', 
                                                    quota: {{ $wb->quota }}, 
                                                    jp: {{ $wb->jp ?? 2 }}, 
                                                    price: {{ (int) $wb->price }}, 
                                                    status: '{{ $wb->status }}',
                                                    cert_available: {{ $wb->cert_available ? 'true' : 'false' }},
                                                    cert_signatory_name: '{{ addslashes($wb->cert_signatory_name ?? '') }}',
                                                    cert_signatory_role: '{{ addslashes($wb->cert_signatory_role ?? '') }}',
                                                    cert_signatory_nip: '{{ addslashes($wb->cert_signatory_nip ?? '') }}'
                                                }; editModal = true"
                                                type="button" 
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                                            ✏️ Edit
                                        </button>

                                        <form action="{{ route('admin.webinars.destroy', $wb->id) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-red-600 hover:bg-red-50 transition">
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

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $webinars->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
