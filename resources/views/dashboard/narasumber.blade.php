@extends('layouts.app')

@section('title', 'Ruang Kerja Narasumber & Instruktur | ' . $user->name)

@section('content')
<div class="min-h-screen bg-slate-50/70 pb-16">

    {{-- Hero Header --}}
    <div class="bg-gradient-to-r from-slate-950 via-amber-950/80 to-slate-900 text-white relative overflow-hidden border-b border-amber-900/40">
        {{-- Ambient decorative glow --}}
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                {{-- Left: User & Title --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            👨‍🏫 NARASUMBER & FASILITATOR CORPU
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-amber-200 border border-white/10">
                            Widyaiswara / Pengajar Ahli
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Ruang Kerja Pengajar — {{ $user->name }}
                    </h1>

                    <p class="text-xs sm:text-sm text-amber-200/80 max-w-2xl leading-relaxed">
                        Kelola materi dan silabus kursus yang Anda ampu, fasilitasi agenda webinar live, dan lakukan evaluasi penugasan peserta didik ASN.
                    </p>

                    <div class="pt-1 flex items-center gap-4 text-xs text-amber-300/70">
                        @if($user->jabatan)
                            <span>Jabatan: <strong class="text-white">{{ $user->jabatan }}</strong></span>
                        @endif
                        @if($user->opd_name)
                            <span>• Instansi: <strong class="text-white">{{ $user->opd_name }}</strong></span>
                        @endif
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    {{-- Switch to Learner Mode --}}
                    <a href="{{ route('dashboard.learner') }}" 
                       class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-bold text-amber-100 bg-amber-900/60 hover:bg-amber-800/80 border border-amber-700/50 rounded-xl shadow-sm transition group"
                       title="Buka portal belajar untuk melihat kursus dan sertifikat pribadi Anda sebagai ASN">
                        <span class="group-hover:rotate-12 transition-transform">🎓</span>
                        <span>Mode Belajar ASN Saya</span>
                    </a>

                    {{-- Question Bank --}}
                    <a href="{{ route('admin.question-banks.index') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 text-xs font-semibold text-white/90 bg-white/10 hover:bg-white/20 border border-white/15 rounded-xl transition">
                        <span>📂 Bank Soal</span>
                    </a>

                    {{-- Pending Review CTA --}}
                    <a href="#grading-queue" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-900 bg-gradient-to-r from-amber-300 via-orange-300 to-amber-200 hover:from-amber-200 hover:to-orange-200 rounded-xl shadow-lg transition whitespace-nowrap">
                        <span>📝 Antrian Penilaian</span>
                        @if($pendingSubmissions->count() > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-rose-600 text-white">
                                {{ $pendingSubmissions->count() }}
                            </span>
                        @endif
                    </a>
                </div>

            </div>

            {{-- 4 Trainer Key Metric Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mt-8">

                {{-- Card 1: Kursus Diampu --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-amber-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Kursus Diampu</span>
                        <span class="text-base group-hover:scale-110 transition-transform">📚</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $assignedCourses->count() }} <span class="text-xs font-semibold text-white/60">Kursus</span>
                    </div>
                    <p class="text-[11px] text-amber-200/70 mt-1">
                        Total silabus LMS terdaftar
                    </p>
                </div>

                {{-- Card 2: Webinar Difasilitasi --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-amber-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Webinar / Sesi Live</span>
                        <span class="text-base group-hover:scale-110 transition-transform">📡</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-teal-300">
                        {{ $assignedWebinars->count() }} <span class="text-xs font-semibold text-white/60">Sesi</span>
                    </div>
                    <p class="text-[11px] text-amber-200/70 mt-1">
                        Seminar & lokakarya narasumber
                    </p>
                </div>

                {{-- Card 3: Total Murid --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl relative overflow-hidden group hover:bg-white/15 transition shadow-sm">
                    <div class="flex items-center justify-between text-amber-300/80 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Total Peserta Didik</span>
                        <span class="text-base group-hover:scale-110 transition-transform">👥</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-amber-300">
                        {{ number_format($totalStudents) }} <span class="text-xs font-semibold text-white/60">ASN</span>
                    </div>
                    <p class="text-[11px] text-amber-200/70 mt-1">
                        Mengikuti kelas yang Anda ampu
                    </p>
                </div>

                {{-- Card 4: Tugas Menunggu Nilai --}}
                <div class="bg-gradient-to-br from-rose-500/20 to-amber-600/20 backdrop-blur-md border border-rose-400/30 p-4 rounded-2xl relative overflow-hidden group hover:border-rose-400/50 transition shadow-sm">
                    <div class="flex items-center justify-between text-rose-200 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider">Tugas Menunggu Nilai</span>
                        <span class="text-base group-hover:scale-110 transition-transform">📝</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-rose-300">
                        {{ $pendingSubmissions->count() }} <span class="text-xs font-semibold text-white/60">Tugas</span>
                    </div>
                    <p class="text-[11px] text-rose-200/80 mt-1">
                        Perlu evaluasi & feedback Anda
                    </p>
                </div>

            </div>

        </div>
    </div>

    {{-- Main Body Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

        {{-- Section 1: Kursus & Silabus yang Diampu --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>📚</span> Kursus & Silabus yang Anda Ampu
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola struktur materi, modul pembelajaran, dan bank kuis</p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-500">
                    {{ $assignedCourses->count() }} Kursus Terdaftar
                </span>
            </div>

            @if($assignedCourses->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($assignedCourses as $course)
                        <div class="rounded-2xl border border-slate-200/80 overflow-hidden bg-white hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <div class="relative h-36 bg-gradient-to-tr from-slate-800 to-indigo-900 overflow-hidden">
                                    @if($course->thumbnail)
                                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-white/30 text-3xl font-black">
                                            LMS
                                        </div>
                                    @endif
                                    <div class="absolute top-2.5 right-2.5">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-950/70 text-amber-300 backdrop-blur-sm border border-white/10">
                                            {{ $course->jp ?? 2 }} JP
                                        </span>
                                    </div>
                                    <div class="absolute bottom-2.5 left-2.5">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/90 text-slate-800 shadow-sm">
                                            {{ $course->category->name ?? 'Umum' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-4 space-y-2">
                                    <h3 class="font-bold text-slate-900 text-sm line-clamp-2">{{ $course->title }}</h3>
                                    <div class="flex items-center gap-3 text-xs text-slate-500 pt-1">
                                        <span>👥 {{ $course->students_count }} Peserta</span>
                                        <span>•</span>
                                        <span>📖 {{ $course->lessons_count }} Modul</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 pt-0 border-t border-slate-100 flex items-center justify-between gap-2 mt-2">
                                <a href="{{ route('admin.courses.lessons', $course->id) }}" 
                                   class="flex-1 text-center py-2 px-3 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/60 transition">
                                    Kelola Modul
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" 
                                   class="py-2 px-3 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center">
                    <div class="text-3xl mb-2">📚</div>
                    <p class="font-bold text-slate-700">Belum Ada Kursus yang Ditugaskan</p>
                    <p class="text-slate-400 text-xs mt-1">Administrator BKPSDM akan menugaskan kelas kursus kepada Anda.</p>
                </div>
            @endif
        </div>

        {{-- Section 2: Antrian Penilaian Tugas (Pending Submissions Queue) --}}
        <div id="grading-queue" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>📝</span> Antrian Penilaian Tugas Peserta Didik
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tugas yang telah dikumpulkan siswa dan memerlukan pemeriksaan nilai & masukan pengajar</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-rose-100 text-rose-800 border border-rose-200">
                    {{ $pendingSubmissions->count() }} Menunggu Diperiksa
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/70">
                        <tr>
                            <th class="py-3.5 px-4">Nama Siswa & OPD</th>
                            <th class="py-3.5 px-4">Kursus & Judul Tugas</th>
                            <th class="py-3.5 px-4">Waktu Kumpul</th>
                            <th class="py-3.5 px-4">Berkas Tugas</th>
                            <th class="py-3.5 px-4 w-64 text-center">Beri Nilai & Umpan Balik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($pendingSubmissions as $sub)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $sub->user->name ?? 'Siswa' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        NIP: {{ $sub->user->nip ?? '-' }} • {{ $sub->user->opd_name ?? '-' }}
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800 text-xs">{{ $sub->assignment->title ?? 'Tugas Modul' }}</div>
                                    <div class="text-[10px] text-indigo-600 mt-0.5">
                                        {{ $sub->assignment->lesson->course->title ?? '-' }}
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">
                                    {{ $sub->created_at ? $sub->created_at->translatedFormat('d M Y H:i') : '-' }}
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($sub->file_path)
                                        <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>Unduh Berkas</span>
                                        </a>
                                    @elseif($sub->content)
                                        <button type="button" @click="alert({{ json_encode($sub->content) }})" 
                                                class="px-2 py-1 rounded text-xs bg-slate-100 text-slate-700 hover:bg-slate-200">
                                            Lihat Teks Jawaban
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-[10px]">Tanpa lampiran</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <form method="POST" action="{{ route('narasumber.submissions.grade', $sub->id) }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="score" min="0" max="100" placeholder="0-100" required 
                                               class="w-20 px-2 py-1 text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-lg text-center focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                        
                                        <input type="text" name="feedback" placeholder="Catatan masukan..." 
                                               class="flex-1 px-2.5 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                        
                                        <button type="submit" 
                                                class="px-3 py-1 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-xs transition shrink-0">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <span>🎉</span> Tidak ada tugas yang menunggu penilaian. Semua tugas telah dievaluasi!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 3: Agenda Webinar Live Mendatang --}}
        @if($assignedWebinars->isNotEmpty())
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <span>📡</span> Agenda Webinar & Sesi Live yang Anda Fasilitasi
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal seminar daring dan tautan virtual room pengajar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($assignedWebinars as $webinar)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                                    <span class="font-mono font-bold text-teal-700">📅 {{ $webinar->event_date ?? 'Segera' }}</span>
                                    <span>{{ $webinar->time_start ?? '09:00' }} - {{ $webinar->time_end ?? '11:30' }} WITA</span>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">{{ $webinar->title }}</h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $webinar->description }}</p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200/80 flex items-center justify-between gap-2">
                                <a href="{{ route('admin.webinars.detail', $webinar->id) }}" 
                                   class="text-xs font-bold text-teal-700 hover:text-teal-900">
                                    Presensi & Peserta &rarr;
                                </a>
                                @if($webinar->webinar_url)
                                    <a href="{{ $webinar->webinar_url }}" target="_blank" 
                                       class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-xs transition">
                                        Masuk Zoom / Link
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</div>
@endsection
