@extends('layouts.app')

@section('content')
<!-- Top Course Header -->
<div class="relative bg-slate-900 text-white border-b border-slate-800 py-10 lg:py-14 overflow-hidden">
    @php
        $heroThumb = $course->thumbnail;
        $heroThumbUrl = $heroThumb ? (str_starts_with($heroThumb, 'http') || str_starts_with($heroThumb, '/') ? $heroThumb : asset('storage/' . $heroThumb)) : null;
    @endphp

    @if($heroThumbUrl)
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <img src="{{ $heroThumbUrl }}" alt="" class="w-full h-full object-cover object-center opacity-25 scale-105 filter blur-[2px]">
            <!-- Gradient overlays to ensure text readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-slate-900/75"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-slate-950/80"></div>
        </div>
    @else
        <div class="absolute inset-0 z-0 bg-gradient-to-b from-slate-900 to-slate-850"></div>
    @endif

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex items-center gap-2 text-xs text-slate-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('courses.index') }}" class="hover:text-white transition">Katalog</a>
            <span>/</span>
            <span class="text-brand-300 font-medium truncate max-w-xs">{{ $course->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-4">
                
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-brand-500/20 text-brand-300 border border-brand-400/30">
                        {{ $course->category->name }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide 
                        @if($course->isInClassOrHybrid()) bg-amber-500/20 text-amber-300 border border-amber-400/30
                        @elseif($course->isLiveWebinar()) bg-sky-500/20 text-sky-300 border border-sky-400/30
                        @else bg-purple-500/20 text-purple-300 border border-purple-400/30 @endif">
                        {{ $course->course_type_label }}
                    </span>
                    @if($course->jp)
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-teal-500/20 text-teal-300 border border-teal-400/30">
                            +{{ $course->jp }} JP
                        </span>
                    @endif
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white/10 text-slate-200">
                        Level: {{ $course->level }}
                    </span>
                    @if($course->certificate_available)
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            Sertifikat Resmi
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    {{ $course->title }}
                </h1>

                <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal">
                    {{ $course->subtitle }}
                </p>

                <!-- Course Meta Info -->
                <div class="pt-2 flex flex-wrap items-center gap-6 text-xs text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <div class="flex text-yellow-400">
                            @php $ratingRounded = round($course->rating); @endphp
                            @for($i=1; $i<=5; $i++)
                                @if($i <= $ratingRounded)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-4 h-4 fill-current text-slate-600" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        @if($course->rating > 0)
                            <span class="font-bold text-white">{{ number_format($course->rating, 1) }}</span>
                        @endif
                        <span class="text-slate-400">({{ number_format($course->students_count) }} peserta)</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Estimasi {{ $course->duration_hours }} Jam Belajar</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>{{ $course->lessons->count() }} Modul & Materi</span>
                    </div>
                </div>

                <!-- Instructor Header Snippet -->
                <div class="pt-2 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-brand-700 text-white font-bold text-xs flex items-center justify-center ring-2 ring-brand-400/30">
                        {{ substr($course->instructor->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Fasilitator / Instruktur</p>
                        <p class="text-xs font-semibold text-white">{{ $course->instructor->name }}</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Main Content Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ showAttendanceModal: false, attendanceRating: 5 }">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Left 2-Cols: Details, Curriculum, Instructor -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- 1. Deskripsi Kursus -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Ringkasan Pelatihan
                </h2>
                <div class="course-description-content text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    {!! $course->clean_description !!}
                </div>
            </div>

            <style>
                .course-description-content h2 {
                    font-size: 1.125rem;
                    font-weight: 800;
                    color: #0f172a;
                    margin-top: 1.25rem;
                    margin-bottom: 0.5rem;
                    letter-spacing: -0.01em;
                }
                .course-description-content h3 {
                    font-size: 0.95rem;
                    font-weight: 700;
                    color: #1e293b;
                    margin-top: 1rem;
                    margin-bottom: 0.4rem;
                }
                .course-description-content p {
                    margin-bottom: 0.75rem;
                    color: #475569;
                    line-height: 1.65;
                }
                .course-description-content ul {
                    list-style-type: disc;
                    padding-left: 1.25rem;
                    margin-top: 0.5rem;
                    margin-bottom: 0.85rem;
                }
                .course-description-content ol {
                    list-style-type: decimal;
                    padding-left: 1.25rem;
                    margin-top: 0.5rem;
                    margin-bottom: 0.85rem;
                }
                .course-description-content li {
                    margin-bottom: 0.35rem;
                    color: #475569;
                    line-height: 1.55;
                }
                .course-description-content strong {
                    color: #0f172a;
                    font-weight: 700;
                }
            </style>

            <!-- 2. Silabus & Kurikulum (Accordion) -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-5" x-data="{ activeSection: null }">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Kurikulum & Silabus Modul
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Total {{ $course->lessons->count() }} materi pembelajaran yang dapat diakses secara mandiri.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($course->sections as $sectionTitle => $lessons)
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <!-- Section Header -->
                            <div class="bg-slate-50 px-4 py-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span class="text-xs font-bold text-slate-800">{{ $sectionTitle }}</span>
                                </div>
                                <span class="text-[11px] text-slate-500">{{ $lessons->count() }} Materi</span>
                            </div>

                            <!-- Lessons List -->
                            <div class="divide-y divide-slate-100 bg-white">
                                @foreach($lessons as $lesson)
                                    <div class="px-4 py-3 flex items-center justify-between text-xs hover:bg-slate-50/70 transition">
                                        <div class="flex items-center gap-3">
                                            @if($lesson->type === 'video')
                                                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            @endif
                                            <span class="text-slate-700 font-medium">{{ $lesson->title }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-slate-400 text-[11px]">
                                            @if($lesson->is_free_preview)
                                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                    Pratinjau Gratis
                                                </span>
                                            @endif
                                            <span>{{ $lesson->duration_minutes }} menit</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 text-xs">
                            Sesi materi pelatihan atau tautan live streaming webinar akan dibagikan kepada peserta terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Profil Fasilitator / Instruktur -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Fasilitator & Pemateri
                </h2>
                
                <div class="flex items-start gap-4 pt-2">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white font-bold text-xl flex items-center justify-center shrink-0 shadow-sm">
                        {{ substr($course->instructor->name, 0, 1) }}
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold text-slate-900 text-base">{{ $course->instructor->name }}</h3>
                        <p class="text-xs text-brand-700 font-medium">{{ $course->instructor->headline }}</p>
                        <p class="text-xs text-slate-500 leading-relaxed pt-1">{{ $course->instructor->bio }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Col: Sticky Enrollment Card -->
        <div class="lg:sticky lg:top-24 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-lg p-6 space-y-5">
                
                <div class="relative aspect-video w-full rounded-xl overflow-hidden bg-slate-100 mb-2">
                    <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    <div class="absolute top-3 left-3">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-slate-900/80 text-white backdrop-blur-sm shadow-sm">
                            {{ $course->course_type_label }}
                        </span>
                    </div>
                    @if($course->jp)
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-teal-500 text-white shadow-sm">
                                +{{ $course->jp }} JP
                            </span>
                        </div>
                    @endif
                </div>

                <div class="space-y-1">
                    <span class="text-xs text-slate-500 font-medium">Biaya Investasi:</span>
                    <div class="flex items-baseline gap-2">
                        @if($course->price <= 0)
                            <span class="text-3xl font-extrabold text-emerald-600">GRATIS</span>
                            <span class="text-xs text-slate-400 font-medium">Program Terakreditasi</span>
                        @else
                            <span class="text-3xl font-extrabold text-slate-900">{{ $course->formatted_price }}</span>
                        @endif
                    </div>
                </div>

                <!-- Action Button depending on enrollment & attendance status -->
                @if($isEnrolled)
                    @if($course->requiresAttendance())
                        @if($enrollment && $enrollment->attended_at)
                            <!-- Presensi Terverifikasi & Sertifikat Ready -->
                            <div class="space-y-3 pt-2">
                                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 space-y-2">
                                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Presensi Terverifikasi</span>
                                    </div>
                                    <p class="text-[11px] text-emerald-700 leading-snug">
                                        Kehadiran Anda telah dicatat pada <span class="font-semibold">{{ $enrollment->attended_at->format('d M Y, H:i') }} WIB</span>. Sertifikat resmi telah otomatis terbit & <span class="font-bold">+{{ $course->jp }} JP</span> terakumulasi.
                                    </p>
                                    @if($enrollment->attendance_rating)
                                        <div class="flex items-center gap-1.5 text-[11px] text-emerald-800 pt-1">
                                            <span class="font-medium">Rating Evaluasi:</span>
                                            <div class="flex text-amber-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3.5 h-3.5 {{ $i <= $enrollment->attendance_rating ? 'fill-current' : 'text-slate-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endfor
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($certificate)
                                    <a href="{{ route('certificates.show', $certificate->certificate_number) }}" target="_blank"
                                       class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md transition-all duration-200 hover:-translate-y-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Lihat & Unduh Sertifikat Resmi</span>
                                    </a>
                                @endif

                                <a href="{{ route('dashboard') }}" 
                                   class="w-full flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                    <span>Lihat di Dashboard Pelatihan</span>
                                </a>
                            </div>
                        @else
                            <!-- Belum Presensi: Munculkan Tombol Presensi Prominen -->
                            <div class="space-y-3 pt-2">
                                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 space-y-1">
                                    <p class="font-bold flex items-center gap-1.5 text-amber-900">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Presensi Kehadiran Diperlukan
                                    </p>
                                    <p class="leading-relaxed">
                                        Silakan isi presensi kehadiran untuk otomatis menerbitkan <strong>Sertifikat Digital</strong> dan mengakumulasikan <strong>+{{ $course->jp }} JP</strong> ke akun Anda.
                                    </p>
                                </div>

                                <button type="button" 
                                        @click="showAttendanceModal = true"
                                        class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-brand-600 via-teal-600 to-emerald-600 hover:opacity-95 shadow-md shadow-brand-500/20 transition-all duration-200 hover:-translate-y-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <span>Isi Presensi & Terbitkan Sertifikat</span>
                                </button>

                                @if($course->lessons->count() > 0)
                                    <a href="{{ route('classroom.show', $course->slug) }}" 
                                       class="w-full flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                        <span>Akses Ruang Belajar / Modul</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    @else
                        <!-- Standar E-Course Mandiri -->
                        <div class="space-y-3 pt-2">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-700">Progres Belajar Anda:</span>
                                    <span class="text-brand-700">{{ $progressPercent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-brand-600 to-teal-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>

                            <a href="{{ route('classroom.show', $course->slug) }}" 
                               class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition">
                                <span>Lanjutkan Belajar di Classroom</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    @endif
                @else
                    <!-- Belum Terdaftar -->
                    <form action="{{ route('courses.enroll', $course->slug) }}" method="POST" class="pt-2 space-y-2">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                            <span>{{ $course->requiresAttendance() ? 'Daftar Kegiatan Sekarang' : 'Daftar & Mulai Belajar' }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        @if($course->requiresAttendance())
                            <p class="text-[11px] text-slate-500 text-center leading-tight">
                                Daftar sekarang untuk dapat mengisi presensi kehadiran & klaim <strong>+{{ $course->jp }} JP</strong>.
                            </p>
                        @endif
                    </form>
                @endif

                <!-- Included Features Checklist -->
                <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs text-slate-600 font-medium">
                    <p class="font-bold text-slate-900 text-xs uppercase tracking-wider">Fasilitas Peserta:</p>
                    
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Akses penuh seumur hidup materi pelatihan</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Sertifikat kelulusan resmi ber-QR Code valid</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Dapat diakses di perangkat Laptop, Tablet & HP</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Akumulasi {{ $course->jp }} JP tercatat otomatis di profil ASN</span>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Attendance Modal (Presensi Kehadiran & Sertifikat Otomatis) -->
    @if(Auth::check() && $isEnrolled && $course->requiresAttendance())
        <div x-show="showAttendanceModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden"
                 @click.outside="showAttendanceModal = false">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-slate-900 via-brand-900 to-slate-900 text-white p-6 relative overflow-hidden">
                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-teal-400/20 text-teal-300 border border-teal-400/30 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">Formulir Presensi Kehadiran</h3>
                                <p class="text-xs text-slate-300">{{ $course->course_type_label }}</p>
                            </div>
                        </div>
                        <button type="button" @click="showAttendanceModal = false" class="text-white/60 hover:text-white p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Form Content -->
                <form action="{{ route('courses.attendance', $course->slug) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    
                    <!-- Course summary notice -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs text-slate-700">
                        <p class="font-bold text-slate-900 truncate">{{ $course->title }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kredit Pembelajaran: <span class="font-bold text-brand-700">+{{ $course->jp }} JP</span></p>
                    </div>

                    <!-- Participant Data (Readonly confirmation) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Nama Peserta</span>
                            <p class="font-bold text-slate-900">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Email / NIP</span>
                            <p class="font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                            @if(Auth::user()->nip)
                                <p class="text-[10px] text-slate-500 font-mono">NIP: {{ Auth::user()->nip }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Timestamp of Attendance -->
                    <div class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <span class="text-slate-500 font-medium">Waktu Presensi:</span>
                        <span class="font-mono font-bold text-slate-800">{{ now()->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <!-- Rating Evaluasi Pelatihan -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block text-xs font-bold text-slate-700">Penilaian / Evaluasi Kegiatan:</label>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="attendance_rating" :value="attendanceRating">
                            <div class="flex items-center gap-1">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                    <button type="button" 
                                            @click="attendanceRating = star"
                                            class="p-1 text-slate-300 hover:text-amber-400 transition">
                                        <svg class="w-6 h-6 fill-current transition-colors" 
                                             :class="star <= attendanceRating ? 'text-amber-400' : 'text-slate-200'"
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <span class="text-xs font-bold text-slate-600" x-text="attendanceRating + ' / 5 Bintang'"></span>
                        </div>
                    </div>

                    <!-- Catatan / Umpan Balik -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Masukan / Ulasan Singkat (Opsional):</label>
                        <textarea name="attendance_notes" rows="2" 
                                  placeholder="Materi sangat bermanfaat dan aplikatif bagi implementasi di faskes..." 
                                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-slate-800 placeholder-slate-400"></textarea>
                    </div>

                    <!-- Instant Certificate Guarantee Alert -->
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-[11px] text-emerald-800">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Dengan menekan tombol di bawah, status presensi Anda langsung <strong>terverifikasi</strong>, sertifikat kelulusan digital langsung <strong>diterbitkan</strong>, dan kredit <strong>+{{ $course->jp }} JP</strong> langsung masuk ke dashboard & histori Anda.</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showAttendanceModal = false" 
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-brand-600 to-teal-600 hover:from-brand-700 hover:to-teal-700 shadow-md transition-all duration-200 hover:-translate-y-0.5">
                            Kirim Presensi & Terbitkan Sertifikat
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
@endsection

