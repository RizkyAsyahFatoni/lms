@extends('layouts.admin')
@section('title', $questionBank->name)
@section('page_title', 'Bank Soal: ' . $questionBank->name)

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'soal', showAddForm: false }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.question-banks.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">← Bank Soal</a>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">📚 {{ $questionBank->name }}</h1>
            @if($questionBank->category)
            <p class="text-xs text-slate-500 mt-0.5">Kategori: {{ $questionBank->category }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-blue-100 text-blue-700 text-sm font-bold">
                {{ $questions->total() }} soal
            </span>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-slate-200">
        <button @click="activeTab = 'soal'" :class="activeTab === 'soal' ? 'border-b-2 border-blue-600 text-blue-700 bg-blue-50' : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-sm font-bold transition rounded-t-xl">
            📝 Daftar Soal
        </button>
        <button @click="activeTab = 'tambah'" :class="activeTab === 'tambah' ? 'border-b-2 border-blue-600 text-blue-700 bg-blue-50' : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-sm font-bold transition rounded-t-xl">
            ➕ Tambah Manual
        </button>
        <button @click="activeTab = 'import'" :class="activeTab === 'import' ? 'border-b-2 border-blue-600 text-blue-700 bg-blue-50' : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-sm font-bold transition rounded-t-xl">
            ⬆️ Import CSV Massal
        </button>
    </div>

    {{-- Tab: Daftar Soal --}}
    <div x-show="activeTab === 'soal'" class="space-y-4">

        {{-- Filter --}}
        <form method="GET" class="flex gap-3 flex-wrap">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari soal..."
                   class="flex-1 min-w-48 px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <select name="difficulty" class="px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Semua Kesulitan</option>
                <option value="easy" {{ request('difficulty') === 'easy' ? 'selected' : '' }}>Mudah</option>
                <option value="medium" {{ request('difficulty') === 'medium' ? 'selected' : '' }}>Sedang</option>
                <option value="hard" {{ request('difficulty') === 'hard' ? 'selected' : '' }}>Sulit</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-blue-700 transition">Filter</button>
        </form>

        {{-- Soal List --}}
        @if($questions->isEmpty())
        <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
            <div class="text-4xl mb-2">📭</div>
            <p class="text-slate-500">Belum ada soal. Tambah manual atau import CSV.</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($questions as $idx => $q)
            @php
            $diffColor = ['easy' => 'emerald', 'hard' => 'rose', 'medium' => 'amber'][$q->difficulty] ?? 'slate';
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs group hover:shadow-md transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-mono font-bold text-slate-400">#{{ ($questions->currentPage() - 1) * $questions->perPage() + $idx + 1 }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $diffColor }}-100 text-{{ $diffColor }}-700 border border-{{ $diffColor }}-200">
                                {{ $q->difficulty_label }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                {{ $q->type === 'multiple_choice' ? 'Pilihan Ganda' : ($q->type === 'true_false' ? 'Benar/Salah' : 'Isian') }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $q->points }} poin</span>
                        </div>
                        <p class="text-sm font-medium text-slate-900 leading-relaxed">{{ $q->question }}</p>

                        @if($q->options)
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                            @foreach($q->options as $key => $val)
                            <div class="flex items-center gap-2 text-xs px-3 py-1.5 rounded-lg {{ strtoupper($key) === strtoupper($q->correct_answer) ? 'bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold' : 'bg-slate-50 text-slate-600' }}">
                                <span class="font-mono font-bold {{ strtoupper($key) === strtoupper($q->correct_answer) ? 'text-emerald-600' : 'text-slate-400' }}">{{ strtoupper($key) }}.</span>
                                {{ $val }}
                                @if(strtoupper($key) === strtoupper($q->correct_answer))
                                <svg class="w-3.5 h-3.5 text-emerald-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if($q->explanation)
                        <p class="text-xs text-slate-500 mt-2 italic">💡 {{ $q->explanation }}</p>
                        @endif
                    </div>

                    <form action="{{ route('admin.question-banks.questions.destroy', $q) }}" method="POST"
                          onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl text-slate-300 hover:text-rose-600 hover:bg-rose-50 transition opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($questions->hasPages())
        <div>{{ $questions->links() }}</div>
        @endif
        @endif
    </div>

    {{-- Tab: Tambah Manual --}}
    <div x-show="activeTab === 'tambah'" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <h2 class="font-bold text-slate-900 mb-5">➕ Tambah Soal Secara Manual</h2>
        <form action="{{ route('admin.question-banks.questions.store', $questionBank) }}" method="POST" class="space-y-4"
              x-data="{ qtype: 'multiple_choice' }">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Soal <span class="text-rose-500">*</span></label>
                    <select name="type" x-model="qtype" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="multiple_choice">Pilihan Ganda (A/B/C/D)</option>
                        <option value="true_false">Benar / Salah</option>
                        <option value="short_answer">Isian Singkat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tingkat Kesulitan</label>
                    <select name="difficulty" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="easy">Mudah</option>
                        <option value="medium" selected>Sedang</option>
                        <option value="hard">Sulit</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Teks Soal <span class="text-rose-500">*</span></label>
                <textarea name="question" rows="3" required placeholder="Tuliskan soal di sini..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
            </div>

            {{-- Multiple Choice Options --}}
            <div x-show="qtype === 'multiple_choice'" class="space-y-3">
                <label class="block text-xs font-bold text-slate-700">Opsi Jawaban</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center shrink-0">{{ $opt }}</span>
                        <input type="text" name="option_{{ strtolower($opt) }}" placeholder="Opsi {{ $opt }}"
                               class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jawaban Benar <span class="text-rose-500">*</span></label>
                    <input type="text" name="correct_answer" required
                           placeholder="Untuk pilihan ganda: A, B, C, atau D. Untuk B/S: TRUE atau FALSE"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                           style="text-transform:uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Poin</label>
                    <input type="number" name="points" value="1" min="1"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Penjelasan Jawaban (Opsional)</label>
                <textarea name="explanation" rows="2" placeholder="Penjelasan mengapa jawaban ini benar..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                ✅ Simpan Soal
            </button>
        </form>
    </div>

    {{-- Tab: Import CSV --}}
    <div x-show="activeTab === 'import'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
            <h2 class="font-bold text-slate-900 mb-2">⬆️ Import Soal Massal via CSV</h2>
            <p class="text-sm text-slate-500 mb-5">
                Upload file CSV berisi banyak soal sekaligus. Kolom yang dibutuhkan: 
                <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">question, type, option_a, option_b, option_c, option_d, correct_answer, explanation, difficulty, points</code>
            </p>

            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 mb-5">
                <p class="text-xs text-blue-800 font-bold mb-1">📌 Petunjuk Import:</p>
                <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                    <li>Baris pertama bisa berisi header atau langsung data soal</li>
                    <li>Untuk soal pilihan ganda: isi option_a hingga option_d, correct_answer = A/B/C/D</li>
                    <li>Untuk soal benar/salah: type = true_false, correct_answer = TRUE atau FALSE</li>
                    <li>difficulty: easy, medium, atau hard (default: medium)</li>
                    <li>points: angka (default: 1)</li>
                </ul>
            </div>

            <div class="flex gap-3 mb-5">
                <a href="{{ route('admin.question-banks.template') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-bold hover:bg-emerald-100 transition">
                    ⬇️ Download Template CSV
                </a>
            </div>

            <form action="{{ route('admin.question-banks.import', $questionBank) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-blue-400 transition">
                    <div class="text-4xl mb-2">📄</div>
                    <p class="text-sm text-slate-600 font-medium mb-3">Drag & drop file CSV atau</p>
                    <label class="cursor-pointer">
                        <span class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">Pilih File CSV</span>
                        <input type="file" name="csv_file" accept=".csv,.txt" class="hidden" required
                               onchange="document.getElementById('csv-name').textContent = this.files[0].name">
                    </label>
                    <p id="csv-name" class="text-xs text-slate-500 mt-2">Belum ada file dipilih (maks. 2 MB)</p>
                </div>

                <button type="submit" class="mt-4 w-full px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                    ⬆️ Import Soal Sekarang
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
