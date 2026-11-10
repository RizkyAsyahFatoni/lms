<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bank Soal: Soal-soal yang dapat digunakan pada berbagai kuis
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // Nama bank soal (e.g., "Bank Soal Kepemimpinan")
            $table->text('description')->nullable();
            $table->string('category')->nullable();         // Kategori/topik soal
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
        
        // Soal-soal dalam bank soal
        Schema::create('bank_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->cascadeOnDelete();
            $table->text('question');                       // Teks soal
            $table->string('type')->default('multiple_choice'); // multiple_choice | true_false | short_answer
            $table->json('options')->nullable();            // Opsi jawaban (untuk multiple choice)
            $table->string('correct_answer');               // Jawaban benar: "A", "B", "C", "D", atau "true/false"
            $table->text('explanation')->nullable();        // Penjelasan jawaban
            $table->integer('points')->default(1);          // Bobot/poin
            $table->string('difficulty')->default('medium'); // easy | medium | hard
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        // Relasi antara Quiz dan Bank Soal (quiz bisa pakai soal dari bank)
        Schema::create('quiz_bank_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('bank_question_id')->constrained('bank_questions')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['quiz_id', 'bank_question_id']);
        });
        
        // Tambah kolom quiz_type ke quizzes: pre_test | post_test | formative
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'quiz_type')) {
                $table->string('quiz_type')->default('formative')->after('title'); // pre_test | post_test | formative
            }
            if (!Schema::hasColumn('quizzes', 'course_id')) {
                // Relasi langsung ke course (selain via lesson) untuk pre/post test level kursus
                $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete()->after('lesson_id');
            }
            if (!Schema::hasColumn('quizzes', 'max_attempts')) {
                $table->integer('max_attempts')->default(3)->after('duration_minutes');
            }
            if (!Schema::hasColumn('quizzes', 'randomize_questions')) {
                $table->boolean('randomize_questions')->default(false)->after('max_attempts');
            }
            if (!Schema::hasColumn('quizzes', 'show_result_immediately')) {
                $table->boolean('show_result_immediately')->default(true)->after('randomize_questions');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_bank_question');
        Schema::dropIfExists('bank_questions');
        Schema::dropIfExists('question_banks');
        
        Schema::table('quizzes', function (Blueprint $table) {
            $cols = ['quiz_type', 'course_id', 'max_attempts', 'randomize_questions', 'show_result_immediately'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('quizzes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
