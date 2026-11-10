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
        // 1. Add course_id to sliders table
        Schema::table('sliders', function (Blueprint $table) {
            if (!Schema::hasColumn('sliders', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('id')->constrained('courses')->nullOnDelete();
            }
        });

        // 2. Add attendance fields to enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'attended_at')) {
                $table->timestamp('attended_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('enrollments', 'attendance_notes')) {
                $table->text('attendance_notes')->nullable()->after('attended_at');
            }
            if (!Schema::hasColumn('enrollments', 'attendance_rating')) {
                $table->tinyInteger('attendance_rating')->nullable()->after('attendance_notes');
            }
        });

        // 3. Add course_id and delivery_mode to webinars table
        Schema::table('webinars', function (Blueprint $table) {
            if (!Schema::hasColumn('webinars', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('id')->constrained('courses')->nullOnDelete();
            }
            if (!Schema::hasColumn('webinars', 'delivery_mode')) {
                $table->string('delivery_mode')->default('online')->after('location_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            if (Schema::hasColumn('sliders', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['attended_at', 'attendance_notes', 'attendance_rating']);
        });

        Schema::table('webinars', function (Blueprint $table) {
            if (Schema::hasColumn('webinars', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
            if (Schema::hasColumn('webinars', 'delivery_mode')) {
                $table->dropColumn('delivery_mode');
            }
        });
    }
};
