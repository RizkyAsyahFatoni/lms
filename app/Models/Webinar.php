<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'description',
        'speaker_name',
        'speaker_title',
        'speaker_institution',
        'speaker_bio',
        'speaker_avatar',
        'event_date',
        'time_start',
        'time_end',
        'location_type',
        'delivery_mode',
        'registration_link',
        'webinar_url',
        'recording_url',
        'cover_image',
        'quota',
        'jp',
        'price',
        'status',
        'is_featured',
        'cert_available',
        'cert_signatory_name',
        'cert_signatory_role',
        'cert_signatory_nip',
    ];

    protected $casts = [
        'event_date'      => 'date',
        'price'           => 'decimal:2',
        'quota'           => 'integer',
        'jp'              => 'integer',
        'is_featured'     => 'boolean',
        'cert_available'  => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(WebinarRegistration::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(WebinarMaterial::class)->orderBy('order');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(WebinarEvaluation::class);
    }

    public function learningRecords(): HasMany
    {
        return $this->hasMany(WebinarLearningRecord::class);
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
        return AttendanceSession::findOrCreateForWebinar($this);
    }

    /** Sisa kuota tersedia */
    public function getRemainingQuotaAttribute(): int
    {
        $registered = $this->registrations()
            ->whereIn('status', ['registered', 'confirmed'])
            ->count();
        return max(0, $this->quota - $registered);
    }

    /** Status badge label */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'ongoing'   => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            default     => 'Akan Datang',
        };
    }
}
