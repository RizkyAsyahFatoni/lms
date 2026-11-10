<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\EncryptionService;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nip',
        'nik',
        'nip_hash',   // HMAC-SHA256 blind index untuk pencarian NIP aman
        'nik_hash',   // HMAC-SHA256 blind index untuk verifikasi NIK
        'password',
        'role', // 'super_admin', 'admin_opd', 'asn_learner', 'narasumber', 'pimpinan', 'reviewer_talent'
        'is_active',
        'opd_name',
        'bagian',
        'jabatan',
        'pangkat_golongan',
        'avatar',
        'phone',
        'headline',
        'bio',
        'sso_provider',
        'sso_id',
        'trainer_status', // 'none', 'pending', 'approved', 'rejected'
        'trainer_bio',
        'trainer_expertise',
        'trainer_cv_path',
        'trainer_applied_at',
        'jp_target_per_year',
    ];

    /**
     * Kolom yang tidak pernah di-expose ke response JSON/array
     * (termasuk nilai terenkripsi mentah agar tidak bocor)
     */
    protected $hidden = [
        'password',
        'remember_token',
        'nip_hash',
        'nik_hash',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'trainer_applied_at' => 'datetime',
    ];

    /**
     * =========================================================
     * NIP & NIK ENCRYPTED ACCESSORS / MUTATORS
     * =========================================================
     * Otomatis enkripsi saat write, dekripsi saat read.
     * Data di database: ciphertext AES-256-CBC
     * Data di PHP: plaintext (hanya saat runtime, tidak disimpan)
     */

    /**
     * Accessor: Dekripsi NIP saat dibaca dari database
     */
    public function getNipAttribute(?string $value): ?string
    {
        if (empty($value)) return null;
        return app(EncryptionService::class)->decryptNip($value);
    }

    /**
     * Mutator: Enkripsi NIP + generate hash saat disimpan ke database
     */
    public function setNipAttribute(?string $value): void
    {
        if (empty($value)) {
            $this->attributes['nip'] = null;
            $this->attributes['nip_hash'] = null;
            return;
        }

        $enc = app(EncryptionService::class);
        $clean = preg_replace('/[^0-9]/', '', $value);

        $this->attributes['nip'] = $enc->encryptNip($clean);
        $this->attributes['nip_hash'] = $enc->hashNip($clean);
    }

    /**
     * Accessor: Dekripsi NIK saat dibaca dari database
     */
    public function getNikAttribute(?string $value): ?string
    {
        if (empty($value)) return null;
        return app(EncryptionService::class)->decryptNik($value);
    }

    /**
     * Mutator: Enkripsi NIK + generate hash saat disimpan ke database
     */
    public function setNikAttribute(?string $value): void
    {
        if (empty($value)) {
            $this->attributes['nik'] = null;
            $this->attributes['nik_hash'] = null;
            return;
        }

        $enc = app(EncryptionService::class);
        $clean = preg_replace('/[^0-9]/', '', $value);

        $this->attributes['nik'] = $enc->encryptNik($clean);
        $this->attributes['nik_hash'] = $enc->hashNik($clean);
    }

    /**
     * Helper: Dapatkan NIP dengan masking untuk tampilan publik
     * Contoh: "199001011990011001" → "1990010119900XXXXX"
     */
    public function getMaskedNipAttribute(): string
    {
        return app(EncryptionService::class)->maskNip($this->nip);
    }

    /**
     * Helper: Dapatkan NIK dengan masking untuk tampilan publik
     * Contoh: "3510091405880002" → "351009XXXXXXXX02"
     */
    public function getMaskedNikAttribute(): string
    {
        return app(EncryptionService::class)->maskNik($this->nik);
    }

    /**
     * Calculate total Jam Pelajaran (JP) from all learning types (Courses, Webinars, External).
     */
    public function getTotalJpAttribute(): int
    {
        return app(\App\Services\JpService::class)->getUserJpSummary($this)['total_jp'];
    }

    /**
     * Get comprehensive JP breakdown by learning type and period.
     */
    public function getJpBreakdown(?int $year = null, ?string $periodType = 'all', ?string $startDate = null, ?string $endDate = null): array
    {
        return app(\App\Services\JpService::class)->getUserJpSummary($this, $year, $periodType, $startDate, $endDate);
    }

    /**
     * Get itemized list of all activities contributing to JP.
     */
    public function getJpActivities(?int $year = null, ?string $periodType = 'all', ?string $startDate = null, ?string $endDate = null)
    {
        return app(\App\Services\JpService::class)->getUserJpActivities($this, $year, $periodType, $startDate, $endDate);
    }

    /**
     * Get JP from completed internal courses.
     */
    public function getInternalJp(?int $year = null): int
    {
        $query = $this->coursesEnrolled()
            ->where(function ($q) {
                $q->whereNotNull('enrollments.completed_at')
                  ->orWhere('enrollments.progress_percent', '>=', 100);
            });

        if ($year) {
            $query->whereYear('enrollments.completed_at', $year);
        }

        return (int) $query->sum('courses.jp');
    }

    /**
     * Get JP from approved external trainings.
     */
    public function getExternalJp(?int $year = null): int
    {
        $query = $this->externalTrainings()->where('status', 'approved');

        if ($year) {
            $query->where('year', $year);
        }

        return (int) $query->sum('jp_count');
    }

    /**
     * Get total JP for a specific year (internal + webinar + external).
     */
    public function getJpByYear(int $year): array
    {
        $summary = $this->getJpBreakdown($year);

        return [
            'internal'  => $summary['jp_elearning'] + $summary['jp_in_class'] + $summary['jp_hybrid'],
            'webinar'   => $summary['jp_webinar'],
            'external'  => $summary['jp_external'],
            'total'     => $summary['total_jp'],
            'target'    => $summary['target_jp'],
            'percent'   => $summary['percentage'],
            'achieved'  => $summary['achieved'],
            'remaining' => $summary['remaining'],
            'breakdown' => [
                'elearning' => $summary['jp_elearning'],
                'webinar'   => $summary['jp_webinar'],
                'in_class'  => $summary['jp_in_class'],
                'hybrid'    => $summary['jp_hybrid'],
                'external'  => $summary['jp_external'],
            ],
        ];
    }

    /**
     * Get JP progress percentage for current year.
     */
    public function getJpProgressPercent(?int $year = null): int
    {
        $year = $year ?: (int) date('Y');
        $data = $this->getJpByYear($year);
        return $data['percent'];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin', 'bkpsdm']);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin', 'bkpsdm']);
    }

    public function isAdminOpd(): bool
    {
        return $this->role === 'admin_opd';
    }

    public function isAsnLearner(): bool
    {
        return in_array($this->role, ['student', 'asn_learner']);
    }

    public function isNarasumber(): bool
    {
        return in_array($this->role, ['instructor', 'narasumber']) || $this->isApprovedTrainer();
    }

    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }

    public function isReviewerTalent(): bool
    {
        return $this->role === 'reviewer_talent';
    }

    public function hasPermission(string $permissionKey): bool
    {
        return RolePermission::hasPermission($this->role, $permissionKey);
    }

    public function getRoleNameAttribute(): string
    {
        $roles = RolePermission::getRolesDefinition();
        $normalized = match($this->role) {
            'admin', 'bkpsdm' => 'super_admin',
            'student'         => 'asn_learner',
            'instructor'      => 'narasumber',
            default           => $this->role,
        };

        return $roles[$normalized]['name'] ?? ucfirst($this->role);
    }

    public function getRoleBadgeAttribute(): string
    {
        $roles = RolePermission::getRolesDefinition();
        $normalized = match($this->role) {
            'admin', 'bkpsdm' => 'super_admin',
            'student'         => 'asn_learner',
            'instructor'      => 'narasumber',
            default           => $this->role,
        };

        $color = $roles[$normalized]['badge_color'] ?? 'bg-slate-100 text-slate-700 border-slate-200';
        $name = $roles[$normalized]['name'] ?? ucfirst($this->role);
        $icon = $roles[$normalized]['icon'] ?? '👤';

        return "<span class=\"inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {$color}\"><span>{$icon}</span><span>{$name}</span></span>";
    }

    public function isInstructor(): bool
    {
        return in_array($this->role, ['instructor', 'narasumber']) || $this->isApprovedTrainer();
    }

    public function isTrainer(): bool
    {
        return $this->trainer_status !== 'none';
    }

    public function isApprovedTrainer(): bool
    {
        return $this->trainer_status === 'approved' || in_array($this->role, ['instructor', 'narasumber']);
    }

    public function isPendingTrainer(): bool
    {
        return $this->trainer_status === 'pending';
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function createdCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function coursesEnrolled(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot('progress_percent', 'completed_at')
            ->withTimestamps();
    }

    public function coursesTeaching(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function externalTrainings(): HasMany
    {
        return $this->hasMany(ExternalTraining::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrollments()->where('course_id', $course->id)->exists();
    }

    public function getCourseProgress(Course $course): int
    {
        return $this->enrollments()->where('course_id', $course->id)->value('progress_percent') ?? 0;
    }

    public function profileChangeRequests(): HasMany
    {
        return $this->hasMany(ProfileChangeRequest::class);
    }

    public function pendingProfileChangeRequest()
    {
        return $this->hasOne(ProfileChangeRequest::class)->where('status', 'pending')->latestOfMany();
    }
}
