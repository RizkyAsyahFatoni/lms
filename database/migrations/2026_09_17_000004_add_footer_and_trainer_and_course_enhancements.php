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
        // 1. AppSettings - Footer Fields
        Schema::table('app_settings', function (Blueprint $table) {
            $table->text('footer_description')->nullable();
            $table->text('footer_address')->nullable();
            $table->string('contact_email')->nullable()->default('bkpsdm@beraukab.go.id');
            $table->string('contact_phone')->nullable()->default('+62 812-3456-7890');
            $table->string('footer_copyright')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('social_tiktok')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_linkedin')->nullable();
        });

        // 2. Users - Trainer Application Fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('trainer_status')->default('none'); // 'none', 'pending', 'approved', 'rejected'
            $table->text('trainer_bio')->nullable();
            $table->string('trainer_expertise')->nullable();
            $table->string('trainer_cv_path')->nullable();
            $table->timestamp('trainer_applied_at')->nullable();
        });

        // 3. Courses - Type, Target Audience & Approval
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'jp')) {
                $table->integer('jp')->nullable()->default(2);
            }
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_status')->default('approved'); // 'approved', 'pending', 'rejected'
            $table->string('course_type')->default('e-course'); // 'e-course', 'live-webinar', 'bimtek', 'workshop', 'hybrid'
            $table->string('sub_category')->nullable();
            $table->string('target_audience_type')->default('umum'); // 'umum', 'asn-opd', 'jabatan-tertentu'
            $table->string('target_audience_detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn([
                'approval_status',
                'course_type',
                'sub_category',
                'target_audience_type',
                'target_audience_detail'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'trainer_status',
                'trainer_bio',
                'trainer_expertise',
                'trainer_cv_path',
                'trainer_applied_at'
            ]);
        });

        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_description',
                'footer_address',
                'contact_email',
                'contact_phone',
                'footer_copyright',
                'social_facebook',
                'social_instagram',
                'social_youtube',
                'social_tiktok',
                'social_twitter',
                'social_linkedin'
            ]);
        });
    }
};
