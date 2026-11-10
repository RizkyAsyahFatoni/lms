<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. External Trainings (JP Submission dari Luar)
        Schema::create('external_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('training_name');
            $table->string('institution_name');
            $table->date('training_date');
            $table->date('training_end_date')->nullable();
            $table->integer('jp_count')->default(1);
            $table->string('certificate_file')->nullable();
            $table->string('supporting_file')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->integer('year'); // Otomatis dari training_date
            $table->timestamps();
        });

        // 2. Tambah kolom jp_target_per_year ke users
        Schema::table('users', function (Blueprint $table) {
            $table->integer('jp_target_per_year')->default(20)->after('trainer_applied_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_trainings');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jp_target_per_year');
        });
    }
};
