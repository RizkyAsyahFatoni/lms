<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'instructor_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'thumbnail',
        'level',
        'price',
        'rating',
        'students_count',
        'duration_hours',
        'jp',
        'certificate_available',
        'is_featured',
        'is_published',
        'cert_title',
        'cert_institution_name',
        'cert_institution_sub',
        'cert_signatory_name',
        'cert_signatory_role',
        'cert_signatory_nip',
        'cert_signature_image',
        'cert_logo_url',
        'cert_theme_color',
        'cert_statement',
        'cert_template_mode',
        'cert_bg_image',
        'cert_font_family',
        'cert_font_size',
        'cert_name_color',
        'cert_name_vertical_pos',
        'cert_show_qr',
        'cert_show_number',
        'cert_show_date',
        'cert_show_seal',
        'cert_show_jp',
        'cert_seal_text',
        'cert_elements_layout',
        'cert_show_header',
        'created_by',
        'approval_status',
        'course_type',
        'sub_category',
        'sub_category_id',
        'tags',
        'target_audience_type',
        'target_audience_detail',
        // KAK fields
        'learning_objectives',
        'target_audience_description',
        'start_date',
        'end_date',
        'schedule_time',
        'schedule_location',
        'registration_deadline',
        'learning_method',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'certificate_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'cert_show_qr' => 'boolean',
        'cert_show_number' => 'boolean',
        'cert_show_date' => 'boolean',
        'cert_show_seal' => 'boolean',
        'cert_show_jp' => 'boolean',
        'cert_show_header' => 'boolean',
        'cert_elements_layout' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_deadline' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function getTagsArrayAttribute(): array
    {
        if (empty($this->tags)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->tags))));
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(CourseEvaluation::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('progress_percent', 'completed_at', 'attended_at', 'attendance_notes', 'attendance_rating')
            ->withTimestamps();
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price <= 0) {
            return 'Gratis';
        }
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getSectionsAttribute()
    {
        return $this->lessons->groupBy('section_title');
    }

    public function isLiveWebinar(): bool
    {
        return in_array($this->course_type, ['live-webinar', 'live_webinar']);
    }

    public function isInClassOrHybrid(): bool
    {
        return in_array($this->course_type, ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar', 'hybrid']);
    }

    public function requiresAttendance(): bool
    {
        return $this->isLiveWebinar() || $this->isInClassOrHybrid();
    }

    public function getCourseTypeLabelAttribute(): string
    {
        return match ($this->course_type) {
            'live-webinar', 'live_webinar' => 'Live Webinar',
            'in-class', 'in_class' => 'Pelatihan Tatap Muka',
            'workshop' => 'Workshop In-Class',
            'bimtek' => 'Bimtek In-Class',
            'seminar' => 'Seminar Tatap Muka',
            'hybrid' => 'Pelatihan Hybrid',
            default => 'E-Course Mandiri',
        };
    }

    public function attendanceSession(): MorphOne
    {
        return $this->morphOne(AttendanceSession::class, 'attendable');
    }

    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

    public function getOrCreateAttendanceSession(): AttendanceSession
    {
        return AttendanceSession::findOrCreateForCourse($this);
    }

    /**
     * Accessor: Deskripsi kursus yang telah disanitasi dengan HTMLPurifier (Anti-XSS).
     */
    public function getCleanDescriptionAttribute(): string
    {
        if (empty($this->description)) {
            return '';
        }

        if (class_exists(\App\Services\HtmlPurifierService::class)) {
            return app(\App\Services\HtmlPurifierService::class)->purify($this->description);
        }

        return strip_tags($this->description, '<p><br><b><strong><i><em><u><ul><ol><li><a><h2><h3><h4>');
    }
}

