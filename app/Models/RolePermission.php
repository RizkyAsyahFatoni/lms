<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = [
        'role',
        'permission_key',
        'is_granted',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
    ];

    /**
     * Master daftar role dan definisi fungsinya sesuai KAK.
     */
    public static function getRolesDefinition(): array
    {
        return [
            'super_admin' => [
                'name'        => 'Super Admin / BKPSDM',
                'description' => 'Pengelolaan keseluruhan sistem pembelajaran, modul, pengguna, dan integrasi.',
                'badge_color' => 'bg-purple-100 text-purple-800 border-purple-200',
                'icon'        => '🛡️',
            ],
            'admin_opd' => [
                'name'        => 'Admin OPD',
                'description' => 'Pengelolaan kegiatan OPD setelah persetujuan BKPSDM.',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'icon'        => '🏛️',
            ],
            'asn_learner' => [
                'name'        => 'ASN / Learner',
                'description' => 'Mengikuti pembelajaran, pre/post-test, tugas, evaluasi, dan perolehan JP.',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'icon'        => '🎓',
            ],
            'narasumber' => [
                'name'        => 'Narasumber / Instruktur',
                'description' => 'Mengelola materi dan kegiatan pembelajaran yang ditugaskan.',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'icon'        => '👨‍🏫',
            ],
            'pimpinan' => [
                'name'        => 'Pimpinan',
                'description' => 'Melihat dashboard eksekutif, rekapitulasi, dan laporan kinerja pembelajaran.',
                'badge_color' => 'bg-rose-100 text-rose-800 border-rose-200',
                'icon'        => '👔',
            ],
            'reviewer_talent' => [
                'name'        => 'Reviewer / Admin Talent',
                'description' => 'Mengelola fitur penilaian kompetensi, evaluasi IDP, dan talent mapping ASN.',
                'badge_color' => 'bg-teal-100 text-teal-800 border-teal-200',
                'icon'        => '🔍',
            ],
        ];
    }

    /**
     * Master daftar permissions/hak akses yang dikelompokkan.
     */
    public static function getAllPermissions(): array
    {
        return [
            'Sistem & Master Data' => [
                'manage_system'         => 'Pengelolaan Keseluruhan Sistem & Pengaturan',
                'manage_users'          => 'Manajemen Pengguna (Tambah, Edit, Nonaktifkan)',
                'manage_roles'          => 'Pengelolaan Role Pengguna',
                'manage_permissions'    => 'Pengaturan Matriks Hak Akses (Permissions)',
                'view_activity_logs'    => 'Melihat Log & Audit Trail Aktivitas Pengguna',
                'reset_passwords'       => 'Reset Kata Sandi Pengguna',
            ],
            'Kurikulum & Modul Kursus' => [
                'manage_courses'        => 'Kelola Kursus & Silabus Pembelajaran',
                'approve_courses'       => 'Persetujuan / Approval Kursus dari Instruktur/OPD',
                'manage_opd_courses'    => 'Kelola Kegiatan Diklat Internal OPD',
                'approve_opd_requests'  => 'Persetujuan Usulan Kegiatan OPD oleh BKPSDM',
                'manage_assigned_materials' => 'Kelola Materi & Penugasan yang Ditugaskan',
                'grade_assignments'     => 'Menilai Tugas Peserta',
            ],
            'Webinar & Live Event' => [
                'manage_webinars'       => 'Kelola Jadwal & Modul Webinar',
                'conduct_webinars'      => 'Akses Fasilitator / Host Webinar Live',
            ],
            'Kompetensi & Talent' => [
                'manage_talent_reviews' => 'Kelola Penilaian Kompetensi & Talent Mapping',
                'review_idp_plans'      => 'Review Individual Development Plan (IDP)',
                'assess_competency'     => 'Penilaian Portofolio Diklat ASN',
                'verify_external_jp'    => 'Verifikasi Pengajuan JP Diklat Eksternal',
            ],
            'Laporan & Pembelajaran' => [
                'view_dashboard_reports'=> 'Akses Dashboard Eksekutif & Laporan Pimpinan',
                'view_opd_reports'      => 'Akses Rekapitulasi & Laporan OPD',
                'export_reports'        => 'Ekspor Laporan Pembelajaran ke Excel/PDF',
                'participate_learning'  => 'Akses Belajar (Kuis, Materi, Sertifikat, Evaluasi)',
            ],
        ];
    }

    /**
     * Cek apakah suatu role memiliki permission tertentu.
     */
    public static function hasPermission(string $role, string $permissionKey): bool
    {
        // Normalize role aliases
        $normalizedRole = match($role) {
            'admin', 'bkpsdm'   => 'super_admin',
            'student'           => 'asn_learner',
            'instructor'        => 'narasumber',
            default             => $role,
        };

        // Super Admin by default has all permissions
        if ($normalizedRole === 'super_admin') {
            return true;
        }

        return self::where('role', $normalizedRole)
            ->where('permission_key', $permissionKey)
            ->where('is_granted', true)
            ->exists();
    }
}
