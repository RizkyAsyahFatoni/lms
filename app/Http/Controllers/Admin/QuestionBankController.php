<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BankQuestion;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * QuestionBankController — Kelola Bank Soal & Import Massal (KAK VIII poin 14)
 *
 * Fitur:
 * - CRUD bank soal & soal individual
 * - Import massal soal dari CSV/Excel
 * - Salin soal dari bank ke quiz tertentu (pre-test/post-test)
 * - Preview & export template CSV
 */
class QuestionBankController extends Controller
{
    private function checkAccess(): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isAdminOpd() && !$user->isNarasumber()) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Daftar semua bank soal
     */
    public function index(Request $request): View
    {
        $this->checkAccess();

        $query = QuestionBank::withCount('questions')
            ->with('creator')
            ->latest();

        if ($request->filled('q')) {
            $query->where(function ($sq) use ($request) {
                $sq->where('name', 'like', "%{$request->q}%")
                   ->orWhere('category', 'like', "%{$request->q}%");
            });
        }

        $banks = $query->paginate(20)->withQueryString();

        return view('admin.question-banks.index', compact('banks'));
    }

    /**
     * Buat bank soal baru
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
        ]);

        $bank = QuestionBank::create([
            'name'        => $request->name,
            'description' => $request->description,
            'category'    => $request->category,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('admin.question-banks.show', $bank)
            ->with('success', "Bank Soal \"{$bank->name}\" berhasil dibuat.");
    }

    /**
     * Detail bank soal + daftar soal
     */
    public function show(QuestionBank $questionBank, Request $request): View
    {
        $this->checkAccess();

        $query = $questionBank->questions();

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        if ($request->filled('q')) {
            $query->where('question', 'like', "%{$request->q}%");
        }

        $questions = $query->paginate(20)->withQueryString();

        return view('admin.question-banks.show', compact('questionBank', 'questions'));
    }

    /**
     * Update info bank soal
     */
    public function update(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
        ]);

        $questionBank->update($request->only('name', 'description', 'category'));

        return back()->with('success', 'Bank Soal berhasil diperbarui.');
    }

    /**
     * Hapus bank soal beserta semua soalnya
     */
    public function destroy(QuestionBank $questionBank): RedirectResponse
    {
        $this->checkAccess();
        $questionBank->delete();
        return redirect()->route('admin.question-banks.index')
            ->with('success', "Bank Soal \"{$questionBank->name}\" berhasil dihapus.");
    }

    /**
     * Tambah soal baru ke bank soal
     */
    public function storeQuestion(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'question'       => 'required|string',
            'type'           => 'required|in:multiple_choice,true_false,short_answer',
            'correct_answer' => 'required|string',
            'explanation'    => 'nullable|string',
            'difficulty'     => 'required|in:easy,medium,hard',
            'points'         => 'nullable|integer|min:1',
            'option_a'       => 'nullable|string',
            'option_b'       => 'nullable|string',
            'option_c'       => 'nullable|string',
            'option_d'       => 'nullable|string',
        ]);

        $options = null;
        if ($request->type === 'multiple_choice') {
            $options = array_filter([
                'A' => $request->option_a,
                'B' => $request->option_b,
                'C' => $request->option_c,
                'D' => $request->option_d,
            ]);
        } elseif ($request->type === 'true_false') {
            $options = ['true' => 'Benar', 'false' => 'Salah'];
        }

        $maxOrder = $questionBank->questions()->max('order') ?? 0;

        BankQuestion::create([
            'question_bank_id' => $questionBank->id,
            'question'         => $request->question,
            'type'             => $request->type,
            'options'          => $options,
            'correct_answer'   => $request->correct_answer,
            'explanation'      => $request->explanation,
            'difficulty'       => $request->difficulty,
            'points'           => $request->points ?? 1,
            'order'            => $maxOrder + 1,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan ke bank soal.');
    }

    /**
     * Hapus soal dari bank
     */
    public function destroyQuestion(BankQuestion $bankQuestion): RedirectResponse
    {
        $this->checkAccess();
        $bankId = $bankQuestion->question_bank_id;
        $bankQuestion->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * IMPORT MASSAL: Upload CSV berisi soal-soal
     * Format CSV: question,type,option_a,option_b,option_c,option_d,correct_answer,explanation,difficulty,points
     */
    public function importCsv(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong.');
        }

        // Skip header row jika ada
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        $hasHeader = in_array('question', $header) || in_array('soal', $header);
        $startRow = $hasHeader ? 1 : 0;

        $imported = 0;
        $errors = [];
        $maxOrder = $questionBank->questions()->max('order') ?? 0;

        for ($i = $startRow; $i < count($rows); $i++) {
            $row = array_map('trim', $rows[$i]);

            // Skip baris kosong
            if (empty(array_filter($row))) continue;

            // Mapping kolom: question, type, A, B, C, D, correct_answer, explanation, difficulty, points
            [$question, $type, $optA, $optB, $optC, $optD, $correctAnswer, $explanation, $difficulty, $points]
                = array_pad($row, 10, null);

            if (empty($question) || empty($correctAnswer)) {
                $errors[] = "Baris " . ($i + 1) . ": Soal dan jawaban benar wajib diisi.";
                continue;
            }

            $type = in_array(strtolower($type ?? ''), ['true_false', 'short_answer']) ? strtolower($type) : 'multiple_choice';
            $difficulty = in_array(strtolower($difficulty ?? ''), ['easy', 'hard']) ? strtolower($difficulty) : 'medium';

            $options = null;
            if ($type === 'multiple_choice') {
                $options = array_filter([
                    'A' => $optA, 'B' => $optB, 'C' => $optC, 'D' => $optD,
                ]);
            } elseif ($type === 'true_false') {
                $options = ['true' => 'Benar', 'false' => 'Salah'];
            }

            BankQuestion::create([
                'question_bank_id' => $questionBank->id,
                'question'         => $question,
                'type'             => $type,
                'options'          => $options,
                'correct_answer'   => strtoupper(trim($correctAnswer)),
                'explanation'      => $explanation,
                'difficulty'       => $difficulty,
                'points'           => (int)($points ?? 1) ?: 1,
                'order'            => ++$maxOrder,
            ]);
            $imported++;
        }

        ActivityLog::record(
            'import_questions',
            "Mengimpor {$imported} soal ke Bank Soal \"{$questionBank->name}\"",
            auth()->id(),
            ['bank_id' => $questionBank->id, 'count' => $imported]
        );

        $msg = "Berhasil mengimpor {$imported} soal.";
        if (!empty($errors)) {
            $msg .= " " . count($errors) . " baris dilewati: " . implode('; ', array_slice($errors, 0, 3));
        }

        return back()->with($imported > 0 ? 'success' : 'error', $msg);
    }

    /**
     * Download template CSV untuk import soal
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_bank_soal.csv"',
        ];

        $columns = ['question', 'type', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'explanation', 'difficulty', 'points'];
        $sampleRows = [
            ['Pancasila sebagai dasar negara Indonesia disahkan pada tanggal?', 'multiple_choice', '17 Agustus 1945', '18 Agustus 1945', '1 Juni 1945', '22 Juni 1945', 'B', 'PPKI mengesahkan UUD dan Pancasila pada 18 Agustus 1945', 'medium', '1'],
            ['Reformasi birokrasi bertujuan untuk meningkatkan kualitas pelayanan publik.', 'true_false', '', '', '', '', 'TRUE', 'Reformasi birokrasi memang bertujuan memperbaiki pelayanan publik', 'easy', '1'],
        ];

        return response()->stream(function () use ($columns, $sampleRows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Salin soal-soal dari bank ke kuis tertentu (pre-test/post-test)
     */
    public function copyToQuiz(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'quiz_id'          => 'required|exists:quizzes,id',
            'question_ids'     => 'required|array|min:1',
            'question_ids.*'   => 'exists:bank_questions,id',
        ]);

        $quiz = Quiz::findOrFail($request->quiz_id);
        $maxOrder = $quiz->questions()->max('order') ?? 0;
        $copied = 0;

        foreach ($request->question_ids as $bankQId) {
            $bankQ = BankQuestion::find($bankQId);
            if (!$bankQ) continue;

            // Hindari duplikasi
            $exists = $quiz->questions()
                ->where('question', $bankQ->question)
                ->exists();
            if ($exists) continue;

            QuizQuestion::create([
                'quiz_id'        => $quiz->id,
                'question'       => $bankQ->question,
                'options'        => $bankQ->options,
                'correct_answer' => $bankQ->correct_answer,
                'explanation'    => $bankQ->explanation,
                'order'          => ++$maxOrder,
            ]);
            $copied++;
        }

        return back()->with('success', "{$copied} soal berhasil disalin ke kuis.");
    }
}
