<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activeLesson->title }} — {{ $course->title }} | LMS KAB Berau Classroom</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col bg-[#f8fafc] text-slate-800 font-sans antialiased" x-data="{ sidebarOpen: true }">

    <!-- Top Classroom Navbar -->
    <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between shrink-0 z-20">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200/70 px-3 py-1.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="hidden sm:inline">Keluar Player</span>
            </a>

            <div class="h-4 w-[1px] bg-slate-200 hidden sm:block"></div>

            <div>
                <h1 class="text-xs sm:text-sm font-bold text-slate-900 truncate max-w-xs sm:max-w-md md:max-w-xl">
                    {{ $course->title }}
                </h1>
                <p class="text-[11px] text-slate-500 truncate">{{ $activeLesson->section_title }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Progress pill -->
            <div class="hidden sm:flex items-center gap-2">
                <div class="text-right">
                    <p class="text-[11px] font-bold text-brand-700">{{ $enrollment->progress_percent }}% Tuntas</p>
                </div>
                <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-brand-600 to-teal-500 h-2 rounded-full transition-all duration-300" style="width: {{ $enrollment->progress_percent }}%"></div>
                </div>
            </div>

            <!-- Toggle Curriculum Sidebar -->
            <button @click="sidebarOpen = !sidebarOpen" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span class="hidden md:inline" x-text="sidebarOpen ? 'Tutup Silabus' : 'Buka Silabus'"></span>
            </button>
        </div>
    </header>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Center/Left: Lesson Player View -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8 lg:p-10 space-y-6">
            <div class="max-w-4xl mx-auto space-y-6">

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="p-4 bg-sky-50 border border-sky-200 text-sky-800 rounded-2xl text-xs font-medium flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                <!-- 100% Certificate Completed Banner -->
                @if($enrollment->progress_percent >= 100 && $certificate)
                    <div class="p-5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm">Selamat! Anda Telah Menuntaskan Kursus Ini 🎉</h3>
                                <p class="text-xs text-emerald-100">Sertifikat kelulusan digital resmi Anda telah diterbitkan.</p>
                            </div>
                        </div>
                        <a href="{{ route('certificates.show', $certificate->certificate_number) }}" target="_blank"
                           class="px-4 py-2 bg-white text-emerald-800 hover:bg-emerald-50 rounded-xl text-xs font-bold shadow-sm transition whitespace-nowrap">
                            Lihat & Unduh Sertifikat
                        </a>
                    </div>
                @endif

                <!-- Lesson Header & Type Badge -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider 
                                @if($activeLesson->type === 'video') bg-brand-50 text-brand-700 border border-brand-200
                                @elseif($activeLesson->type === 'pdf') bg-rose-50 text-rose-700 border border-rose-200
                                @elseif(in_array($activeLesson->type, ['pre_test', 'post_test'])) bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($activeLesson->type === 'assignment') bg-purple-50 text-purple-700 border border-purple-200
                                @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                {{ str_replace('_', ' ', strtoupper($activeLesson->type)) }}
                            </span>
                            <span class="text-xs text-slate-500">{{ $activeLesson->duration_minutes }} Menit Belajar</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            {{ $activeLesson->title }}
                        </h2>
                    </div>

                    <!-- Non-quiz & Non-assignment mark complete button -->
                    @if(!in_array($activeLesson->type, ['pre_test', 'post_test', 'assignment']))
                        <form action="{{ route('classroom.complete', [$course->slug, $activeLesson->slug]) }}" method="POST">
                            @csrf
                            @php $isCompleted = isset($completedLessonsMap[$activeLesson->id]); @endphp
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm {{ $isCompleted ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-slate-900 text-white hover:bg-brand-700' }}">
                                @if($isCompleted)
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Selesai (Klik Batal)</span>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Tandai Selesai & Lanjut</span>
                                @endif
                            </button>
                        </form>
                    @endif
                </div>

                <!-- 1. VIDEO VIEW -->
                @if($activeLesson->type === 'video' && $activeLesson->video_url)
                    <div class="relative aspect-video w-full rounded-2xl overflow-hidden bg-slate-900 shadow-lg border border-slate-800">
                        <iframe class="w-full h-full" src="{{ $activeLesson->video_url }}" title="{{ $activeLesson->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @endif

                <!-- 2. PDF DOCUMENT READER VIEW -->
                @if($activeLesson->type === 'pdf')
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm">Dokumen Modul PDF Resmi</h3>
                                    <p class="text-[11px] text-slate-500">Dapat dibaca langsung di browser atau diunduh untuk belajar offline.</p>
                                </div>
                            </div>
                            @if($activeLesson->pdf_url)
                                <a href="{{ $activeLesson->pdf_url }}" target="_blank" download
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh PDF</span>
                                </a>
                            @endif
                        </div>

                        <!-- Embedded PDF Frame -->
                        <div class="w-full h-[600px] rounded-xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center">
                            @if($activeLesson->pdf_url)
                                <iframe src="{{ $activeLesson->pdf_url }}" class="w-full h-full" frameborder="0"></iframe>
                            @else
                                <div class="text-center p-8 text-slate-500 space-y-2">
                                    <svg class="w-12 h-12 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-bold text-sm text-slate-700">Dokumen Modul Materi</p>
                                    <p class="text-xs text-slate-400">Materi ajar dapat dipelajari melalui tayangan materi atau silabus instruktur.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 3. PRE-TEST & POST-TEST INTERACTIVE QUIZ VIEW -->
                @if(in_array($activeLesson->type, ['pre_test', 'post_test']) && $activeLesson->quiz)
                    @php 
                        $quiz = $activeLesson->quiz;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                        
                        <!-- Quiz Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $activeLesson->type === 'pre_test' ? 'Evaluasi Pemahaman Awal' : 'Ujian Akhir Kelulusan' }}
                                </span>
                                <h3 class="text-lg font-bold text-slate-900 mt-1">{{ $quiz->title }}</h3>
                                <p class="text-xs text-slate-500">{{ $quiz->description }}</p>
                            </div>
                            
                            <div class="flex items-center gap-3 text-xs">
                                <div class="p-2 bg-slate-50 rounded-xl border border-slate-200 text-center">
                                    <span class="text-[10px] text-slate-400 block font-medium">Batas Lulus</span>
                                    <span class="font-bold text-slate-900">{{ $quiz->passing_score }}%</span>
                                </div>
                                <div class="p-2 bg-slate-50 rounded-xl border border-slate-200 text-center">
                                    <span class="text-[10px] text-slate-400 block font-medium">Total Soal</span>
                                    <span class="font-bold text-slate-900">{{ $quiz->questions->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Previous Attempt Score Banner if exists -->
                        @if($latestQuizAttempt)
                            <div class="p-4 rounded-xl border {{ $latestQuizAttempt->is_passed ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900' }} flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-xs">Hasil Evaluasi Terakhir: Skor {{ $latestQuizAttempt->score }}/100</p>
                                    <p class="text-[11px] mt-0.5">
                                        Status: {{ $latestQuizAttempt->is_passed ? 'LULUS (Memenuhi Standar Kelulusan)' : 'BELUM MEMENUHI AMBANG BATAS' }}
                                    </p>
                                </div>
                                @if($latestQuizAttempt->is_passed)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-600 text-white">LULUS</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-600 text-white">REMEDI</span>
                                @endif
                            </div>
                        @endif

                        <!-- Quiz Form -->
                        <form action="{{ route('classroom.quiz.submit', [$course->slug, $activeLesson->slug]) }}" method="POST" class="space-y-6">
                            @csrf
                            
                            @foreach($quiz->questions as $index => $q)
                                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                                    <div class="flex items-start gap-3">
                                        <span class="w-6 h-6 rounded-lg bg-slate-900 text-white text-xs font-bold flex items-center justify-center shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                        <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-snug">
                                            {{ $q->question }}
                                        </p>
                                    </div>

                                    <!-- Options List -->
                                    <div class="space-y-2 pl-9">
                                        @foreach($q->options as $key => $optText)
                                            @php 
                                                $savedAns = $latestQuizAttempt->submitted_answers[$q->id] ?? null;
                                                $isSelected = $savedAns === $key;
                                            @endphp
                                            <label class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200 hover:border-brand-400 cursor-pointer transition text-xs font-medium text-slate-700">
                                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" required
                                                       {{ $isSelected ? 'checked' : '' }}
                                                       class="w-4 h-4 text-brand-600 border-slate-300 focus:ring-brand-500">
                                                <span class="font-bold text-slate-900">{{ $key }}.</span>
                                                <span>{{ $optText }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @if($latestQuizAttempt)
                                        <div class="mt-3 pl-9 text-[11px] text-slate-500 bg-white p-3 rounded-lg border border-slate-200/80">
                                            <span class="font-semibold text-brand-700">Pembahasan:</span> {{ $q->explanation }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="pt-2">
                                <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                                    <span>Kirimkan Jawaban Kuis</span>
                                </button>
                            </div>
                        </form>

                    </div>
                @endif

                <!-- 4. ASSIGNMENT / TUGAS PRAKTIK VIEW -->
                @if($activeLesson->type === 'assignment' && $activeLesson->assignment)
                    @php $assignment = $activeLesson->assignment; @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                        
                        <div class="pb-4 border-b border-slate-100 space-y-2">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                Studi Kasus & Tugas Mandiri
                            </span>
                            <h3 class="text-lg font-bold text-slate-900">{{ $assignment->title }}</h3>
                        </div>

                        <!-- Instructions -->
                        <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed font-normal bg-slate-50 p-5 rounded-xl border border-slate-200/80">
                            {!! nl2br(e($assignment->instructions)) !!}
                        </div>

                        <!-- Submission Status if exists -->
                        @if($userSubmission)
                            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs space-y-1">
                                <p class="font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Tugas Telah Dikumpulkan pada {{ $userSubmission->submitted_at->format('d M Y, H:i') }} WIB
                                </p>
                                @if($userSubmission->status === 'graded')
                                    <p class="font-semibold text-emerald-800">Nilai: {{ $userSubmission->score }}/{{ $assignment->max_score }}</p>
                                    <p class="text-slate-600">Catatan Fasilitator: {{ $userSubmission->feedback ?? 'Tugas memenuhi standar.' }}</p>
                                @else
                                    <p class="text-slate-500">Status: Menunggu review fasilitator.</p>
                                @endif
                            </div>
                        @endif

                        <!-- Submission Form -->
                        <form action="{{ route('classroom.assignment.submit', [$course->slug, $activeLesson->slug]) }}" method="POST" class="space-y-4 pt-2">
                            @csrf

                            <div>
                                <label for="submission_notes" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Jawaban Tugas / Penjelasan Penyelesaian
                                </label>
                                <textarea id="submission_notes" name="submission_notes" rows="6" required
                                          placeholder="Tuliskan struktur payload, analisa kasus, atau catatan pengerjaan tugas Anda di sini..."
                                          class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">{{ old('submission_notes', $userSubmission->submission_notes ?? '') }}</textarea>
                            </div>

                            <div>
                                <label for="file_url" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Link Dokumen Pendukung / Google Drive / GitHub (Opsional)
                                </label>
                                <input type="url" id="file_url" name="file_url" value="{{ old('file_url', $userSubmission->file_url ?? '') }}"
                                       placeholder="https://drive.google.com/..."
                                       class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                            </div>

                            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                                <span>{{ $userSubmission ? 'Perbarui Pengumpulan Tugas' : 'Kirimkan Tugas Saya' }}</span>
                            </button>
                        </form>

                    </div>
                @endif

                <!-- 5. READING / CONTENT BODY -->
                @if($activeLesson->content && !in_array($activeLesson->type, ['pre_test', 'post_test', 'assignment']))
                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Materi & Rangkuman Penjelasan:</h3>
                        <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed font-normal">
                            {!! nl2br(e($activeLesson->content)) !!}
                        </div>
                    </div>
                @endif

                <!-- Navigation Bottom Bar (Prev / Next) -->
                <div class="pt-4 flex items-center justify-between">
                    @if($prevLesson)
                        <a href="{{ route('classroom.show', [$course->slug, $prevLesson->slug]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            <span>Materi Sebelumnya</span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('classroom.show', [$course->slug, $nextLesson->slug]) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
                            <span>Materi Berikutnya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>

            </div>
        </main>

        <!-- Right Side: Syllabus Checklist Sidebar -->
        <aside x-show="sidebarOpen" class="w-80 lg:w-96 border-l border-slate-200/80 bg-white flex flex-col shrink-0">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-xs text-slate-900 uppercase tracking-wider">Silabus Pelatihan</h3>
                    <p class="text-[11px] text-slate-500">{{ $course->lessons->count() }} modul materi terstruktur</p>
                </div>
            </div>

            <!-- Lessons Scrollable List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @foreach($course->sections as $sectionTitle => $lessons)
                    <div>
                        <div class="bg-slate-50/80 px-4 py-2.5 text-[11px] font-bold text-slate-700 uppercase tracking-wider border-y border-slate-100">
                            {{ $sectionTitle }}
                        </div>
                        <div class="divide-y divide-slate-50">
                            @foreach($lessons as $lesson)
                                @php 
                                    $isDone = isset($completedLessonsMap[$lesson->id]);
                                    $isActive = $lesson->id === $activeLesson->id;
                                @endphp
                                <a href="{{ route('classroom.show', [$course->slug, $lesson->slug]) }}" 
                                   class="px-4 py-3 flex items-start gap-3 text-xs transition {{ $isActive ? 'bg-brand-50/80 text-brand-900 font-semibold border-l-4 border-brand-600' : 'hover:bg-slate-50 text-slate-700' }}">
                                    
                                    <!-- Checkmark Status Icon -->
                                    <div class="mt-0.5 shrink-0">
                                        @if($isDone)
                                            <div class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        @else
                                            <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="truncate leading-tight {{ $isActive ? 'font-bold text-slate-900' : '' }}">{{ $lesson->title }}</p>
                                        <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 font-normal">
                                            <span class="uppercase font-medium text-[9px] px-1 py-0.2 rounded 
                                                @if($lesson->type === 'video') bg-brand-50 text-brand-700
                                                @elseif($lesson->type === 'pdf') bg-rose-50 text-rose-700
                                                @elseif(in_array($lesson->type, ['pre_test', 'post_test'])) bg-amber-50 text-amber-700
                                                @elseif($lesson->type === 'assignment') bg-purple-50 text-purple-700
                                                @else bg-slate-100 text-slate-700 @endif">
                                                {{ str_replace('_', ' ', $lesson->type) }}
                                            </span>
                                            <span>•</span>
                                            <span>{{ $lesson->duration_minutes }} mnt</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

    </div>

</body>
</html>
