<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('book-open');
            $table->string('badge_color')->default('teal'); // 'teal', 'blue', 'indigo', 'emerald'
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('level')->default('Pemula'); // 'Pemula', 'Menengah', 'Lanjutan'
            $table->decimal('price', 12, 2)->default(0); // 0 = Free
            $table->decimal('rating', 3, 2)->default(4.9);
            $table->integer('students_count')->default(0);
            $table->integer('duration_hours')->default(2);
            $table->boolean('certificate_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 3. Lessons (Bab & Modul Pembelajaran)
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('section_title'); // e.g. "Modul 1: Landasan & Konsep"
            $table->string('title');
            $table->string('slug');
            $table->integer('order')->default(1);
            $table->string('type')->default('video'); // 'video', 'reading', 'quiz'
            $table->string('video_url')->nullable();
            $table->longText('content')->nullable();
            $table->integer('duration_minutes')->default(15);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();
        });

        // 4. Enrollments (Pendaftaran Kursus oleh Peserta)
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('progress_percent')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });

        // 5. Lesson Progress (Checklist per Materi)
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });

        // 6. Certificates
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->timestamp('issued_at');
            $table->string('verification_hash')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('categories');
    }
};
