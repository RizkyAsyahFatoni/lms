<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add pdf_url column to lessons if not exists
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('pdf_url')->nullable()->after('video_url');
        });

        // 2. Quizzes (Pre-Test & Post-Test)
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70); // Persentase kelulusan
            $table->integer('duration_minutes')->default(15);
            $table->timestamps();
        });

        // 3. Quiz Questions
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options'); // ['A' => '...', 'B' => '...', ...]
            $table->string('correct_answer'); // 'A', 'B', 'C', 'D'
            $table->text('explanation')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        // 4. Quiz Attempts (Riwayat Mengerjakan Soal oleh Peserta)
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->default(0); // 0-100
            $table->boolean('is_passed')->default(false);
            $table->json('submitted_answers')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 5. Assignments (Tugas & Studi Kasus)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('instructions');
            $table->string('attachment_url')->nullable(); // File template tugas
            $table->integer('max_score')->default(100);
            $table->timestamps();
        });

        // 6. Assignment Submissions (Pengumpulan Tugas oleh Peserta)
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('submission_notes')->nullable();
            $table->string('file_url')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->string('status')->default('submitted'); // 'submitted', 'graded', 'revision'
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('pdf_url');
        });
    }
};
