<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * KAK E-Learning/E-Course Requirements:
     * - Tujuan pembelajaran (learning_objectives)
     * - Sasaran peserta deskriptif (target_audience_description)
     * - Jadwal / tanggal rilis & pelaksanaan (schedule fields)
     * - Metode pembelajaran (learning_method)
     * - Tautan pembelajaran di lessons (type: 'link', external_url)
     * - Evaluasi pembelajaran: tabel baru course_evaluations
     */
    public function up(): void
    {
        // 1. Tambah field baru ke tabel courses (KAK poin 4, 5, 7, 8)
        Schema::table('courses', function (Blueprint $table) {
            // Poin 4: Tujuan Pembelajaran
            $table->text('learning_objectives')->nullable()->after('description');

            // Poin 5: Sasaran Peserta deskriptif
            $table->text('target_audience_description')->nullable()->after('target_audience_detail');

            // Poin 7: Jadwal
            $table->date('start_date')->nullable()->after('learning_objectives');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('schedule_time', 10)->nullable()->after('end_date');
            $table->text('schedule_location')->nullable()->after('schedule_time');
            $table->date('registration_deadline')->nullable()->after('schedule_location');

            // Poin 8: Metode Pembelajaran
            $table->string('learning_method')->nullable()->after('course_type');
        });

        // 2. Tambah kolom 'external_url' ke lessons untuk tipe 'link' (KAK poin 13)
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('external_url')->nullable()->after('pdf_url');
        });

        // 3. Buat tabel course_evaluations (KAK poin 16: Evaluasi Pembelajaran)
        Schema::create('course_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Rating reaksi peserta (1-5)
            $table->tinyInteger('rating_materi')->nullable();
            $table->tinyInteger('rating_instruktur')->nullable();
            $table->tinyInteger('rating_platform')->nullable();
            $table->tinyInteger('rating_relevansi')->nullable();
            $table->tinyInteger('rating_overall')->nullable();

            // Komentar & masukan terbuka
            $table->text('saran_materi')->nullable();
            $table->text('komentar_umum')->nullable();
            $table->boolean('will_recommend')->default(true);

            // Rekap nilai akademik terintegrasi
            $table->integer('pretest_score')->nullable();
            $table->integer('posttest_score')->nullable();
            $table->integer('assignment_score')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_evaluations');

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'learning_objectives',
                'target_audience_description',
                'start_date',
                'end_date',
                'schedule_time',
                'schedule_location',
                'registration_deadline',
                'learning_method',
            ]);
        });
    }
};
