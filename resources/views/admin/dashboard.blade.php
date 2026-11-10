@extends('layouts.admin')

@section('page_title', 'Dashboard & Ringkasan')

@section('content')
<div class="space-y-8">
    
    <!-- Top Greeting & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Superadmin Management Portal</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    SUPER ADMIN
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Pantau metrik pendaftar, kurikulum materi, slider beranda, workshop in-class, dan kelulusan peserta.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Buat Kursus Baru</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards (v0 Precision) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Pendaftar (Siswa)</p>
            <p class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($stats['total_students']) }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Kursus Aktif</p>
            <p class="text-2xl font-black text-brand-700 tracking-tight mt-1">{{ $stats['total_courses'] }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Total Modul</p>
            <p class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $stats['total_lessons'] }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Review Tugas</p>
            <p class="text-2xl font-black text-amber-600 tracking-tight mt-1">{{ $stats['pending_submissions'] }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Agenda Workshop</p>
            <p class="text-2xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['total_webinars'] }}</p>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <p class="text-[11px] text-slate-500 font-medium">Slider Beranda</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['active_sliders'] }} <span class="text-xs font-bold text-slate-400">Aktif</span></p>
        </div>
    </div>

    <!-- Two Column Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Active Courses & Upcoming Webinars -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Active Courses Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Program Pelatihan Terbaru</h3>
                        <p class="text-xs text-slate-500">Daftar kursus aktif dengan opsi Certificate Builder & Silabus.</p>
                    </div>
                    <a href="{{ route('admin.courses.index') }}" class="text-xs text-brand-700 font-semibold hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="p-4">Kursus</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Instruktur</th>
                                <th class="p-4">Sertifikat</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentCourses as $course)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="p-4">
                                        <div class="font-bold text-slate-900 line-clamp-1">{{ $course->title }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $course->duration_hours }} Jam • {{ $course->lessons->count() }} Modul</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $course->category->name }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-600 font-medium">
                                        {{ $course->instructor->name }}
                                    </td>
                                    <td class="p-4">
                                        <a href="{{ route('admin.courses.certificate-builder', $course->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 transition">
                                            <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>Builder</span>
                                        </a>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('admin.courses.lessons', $course->id) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-brand-50 hover:text-brand-700 font-semibold text-[11px] transition">
                                            Kelola Silabus
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming Webinars Strip -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Agenda Webinar Terdekat</h3>
                        <p class="text-xs text-slate-500">Tayang di bagian atas beranda utama sebagai agenda nasional.</p>
                    </div>
                    <a href="{{ route('admin.webinars.index') }}" class="text-xs text-brand-700 font-semibold hover:underline">
                        Kelola Agenda &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($upcomingWebinars as $webinar)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/60 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 flex flex-col items-center justify-center flex-shrink-0">
                                    <span class="text-[10px] font-bold uppercase">{{ $webinar->event_date->format('M') }}</span>
                                    <span class="text-base font-extrabold leading-none">{{ $webinar->event_date->format('d') }}</span>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 line-clamp-1">{{ $webinar->title }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                        <span>{{ $webinar->speaker_name }}</span>
                                        <span>&bull;</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $webinar->time_start }} - {{ $webinar->time_end }} WIB</span>
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-center">
                                {{ $webinar->location_type }}
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400">Belum ada agenda webinar terdaftar.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Recent Enrollments & Pending Assignments -->
        <div class="space-y-8">
            
            <!-- Pending Assignments Review -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Tugas Menunggu Nilai</h3>
                    <a href="{{ route('admin.submissions.index') }}" class="text-[11px] font-semibold text-brand-700 hover:underline">
                        Semua &rarr;
                    </a>
                </div>

                @if($pendingAssignments->isEmpty())
                    <p class="text-xs text-slate-400 py-4 text-center">Tidak ada tugas yang menunggu review saat ini.</p>
                @else
                    <div class="space-y-3">
                        @foreach($pendingAssignments as $sub)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800 truncate max-w-[160px]">{{ $sub->user->name }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : '-' }}</span>
                                </div>
                                <p class="text-[11px] text-slate-600 line-clamp-1">{{ $sub->assignment->lesson->title ?? 'Tugas' }}</p>
                                <div class="pt-1 flex justify-end">
                                    <a href="{{ route('admin.submissions.index') }}" class="text-[11px] font-bold text-brand-700 hover:underline">
                                        Review Sekarang &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Enrollments -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Aktivitas Pendaftaran</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-[11px] font-semibold text-brand-700 hover:underline">
                        Lihat Siswa &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($recentEnrollments as $enr)
                        <div class="flex items-center gap-3 text-xs">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0">
                                {{ substr($enr->user->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-900 truncate">{{ $enr->user->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $enr->course->title }}</p>
                            </div>
                            <span class="text-[10px] font-bold font-mono text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                                {{ $enr->progress_percent }}%
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
