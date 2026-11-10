<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'opd_name')) {
                $table->string('opd_name')->nullable()->after('headline');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('opd_name');
            }
            if (!Schema::hasColumn('users', 'pangkat_golongan')) {
                $table->string('pangkat_golongan')->nullable()->after('jabatan');
            }
        });

        // 2. Activity Logs Table (Audit Trail)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // e.g. 'login', 'logout', 'create_user', 'change_role', 'reset_password', 'toggle_status'
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });

        // 3. Role Permissions Table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // 'super_admin', 'admin_opd', 'asn_learner', 'narasumber', 'pimpinan', 'reviewer_talent'
            $table->string('permission_key');
            $table->boolean('is_granted')->default(true);
            $table->timestamps();

            $table->unique(['role', 'permission_key']);
        });

        // Seed default role permissions based on KAK
        $defaultPermissions = [
            // Super Admin / BKPSDM: Pengelolaan keseluruhan sistem
            'super_admin' => [
                'manage_system', 'manage_users', 'manage_roles', 'manage_permissions',
                'manage_courses', 'approve_courses', 'manage_webinars', 'manage_opd',
                'approve_opd_requests', 'view_reports', 'manage_talent_reviews',
                'view_activity_logs', 'reset_passwords', 'participate_learning'
            ],
            // Admin OPD: Pengelolaan kegiatan OPD setelah persetujuan BKPSDM
            'admin_opd' => [
                'manage_opd_courses', 'submit_opd_proposals', 'view_opd_participants',
                'view_opd_reports', 'participate_learning'
            ],
            // ASN / Learner: Mengikuti pembelajaran
            'asn_learner' => [
                'participate_learning', 'take_tests', 'submit_assignments',
                'download_certificates', 'submit_external_jp', 'evaluate_courses'
            ],
            // Narasumber: Mengelola materi/kegiatan yang ditugaskan
            'narasumber' => [
                'manage_assigned_materials', 'grade_assignments', 'conduct_webinars',
                'view_student_progress', 'participate_learning'
            ],
            // Pimpinan: Melihat dashboard dan laporan
            'pimpinan' => [
                'view_dashboard_reports', 'view_executive_summary', 'view_jp_statistics',
                'view_opd_reports', 'export_reports'
            ],
            // Reviewer / Admin Talent: Mengelola fitur penilaian kompetensi/talent
            'reviewer_talent' => [
                'manage_talent_reviews', 'review_idp_plans', 'assess_competency',
                'view_talent_dashboard', 'verify_external_jp'
            ],
        ];

        $now = now();
        $records = [];
        foreach ($defaultPermissions as $role => $permissions) {
            foreach ($permissions as $perm) {
                $records[] = [
                    'role'           => $role,
                    'permission_key' => $perm,
                    'is_granted'     => true,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        DB::table('role_permissions')->insert($records);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('activity_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'opd_name', 'jabatan', 'pangkat_golongan']);
        });
    }
};
