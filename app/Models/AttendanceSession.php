<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'session_code',
        'attendable_type',
        'attendable_id',
        'date',
        'time_start',
        'time_end',
        'open_at',
        'close_at',
        'tolerance_late_minutes',
        'min_duration_minutes',
        'allowed_methods',
        'is_active',
        'qr_secret_token',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'date'                   => 'date',
        'open_at'                => 'datetime',
        'close_at'               => 'datetime',
        'tolerance_late_minutes' => 'integer',
        'min_duration_minutes'   => 'integer',
        'is_active'              => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $session) {
            if (!$session->session_code) {
                $session->session_code = 'PRES-' . strtoupper(Str::random(8));
            }
            if (!$session->qr_secret_token) {
                $session->qr_secret_token = Str::random(40);
            }
        });
    }

    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Cek apakah jendela presensi sedang dibuka
     */
    public function isOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->open_at && $now->lt($this->open_at)) {
            return false;
        }

        if ($this->close_at && $now->gt($this->close_at)) {
            return false;
        }

        return true;
    }

    /**
     * Evaluasi status kehadiran: 'present' atau 'late'
     */
    public function evaluateStatus(?Carbon $checkInTime = null): string
    {
        $time = $checkInTime ?? now();

        if ($this->time_start) {
            $eventStart = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_start);
            $lateThreshold = $eventStart->copy()->addMinutes($this->tolerance_late_minutes);

            if ($time->gt($lateThreshold)) {
                return 'late';
            }
        }

        return 'present';
    }

    /**
     * Hitung durasi kehadiran aktual & apakah memenuhi syarat konfigurasi
     */
    public function calculateDuration(Carbon $checkIn, ?Carbon $checkOut = null): array
    {
        $out = $checkOut ?? now();
        $durationMinutes = max(0, $checkIn->diffInMinutes($out));
        $qualified = $durationMinutes >= $this->min_duration_minutes;

        return [
            'duration_minutes'      => $durationMinutes,
            'is_duration_qualified' => $qualified,
        ];
    }

    /**
     * URL Presensi via QR Code
     */
    public function getQrScanUrlAttribute(): string
    {
        return route('attendance.qr.scan', [
            'code'  => $this->session_code,
            'token' => $this->qr_secret_token,
        ]);
    }

    /**
     * URL Gambar QR Code (Dynamic generator image)
     */
    public function getQrImageUrlAttribute(): string
    {
        $data = urlencode($this->qr_scan_url);
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$data}";
    }

    /**
     * Cari atau buat sesi presensi otomatis untuk Webinar
     */
    public static function findOrCreateForWebinar(Webinar $webinar): self
    {
        return self::firstOrCreate(
            [
                'attendable_type' => Webinar::class,
                'attendable_id'   => $webinar->id,
            ],
            [
                'title'                  => 'Presensi Webinar: ' . $webinar->title,
                'session_code'           => 'WBN-' . str_pad($webinar->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4)),
                'date'                   => $webinar->event_date ?? now()->toDateString(),
                'time_start'             => $webinar->time_start ? substr($webinar->time_start, 0, 5) : '08:00',
                'time_end'               => $webinar->time_end ? substr($webinar->time_end, 0, 5) : '12:00',
                'open_at'                => $webinar->event_date ? Carbon::parse($webinar->event_date->format('Y-m-d') . ' ' . ($webinar->time_start ?? '08:00'))->subMinutes(30) : now()->subHour(),
                'close_at'               => $webinar->event_date ? Carbon::parse($webinar->event_date->format('Y-m-d') . ' ' . ($webinar->time_end ?? '12:00'))->addMinutes(60) : now()->addHours(6),
                'tolerance_late_minutes' => 15,
                'min_duration_minutes'   => 45,
                'allowed_methods'        => 'both',
                'is_active'              => true,
                'qr_secret_token'        => Str::random(40),
                'created_by'             => auth()->id(),
            ]
        );
    }

    /**
     * Cari atau buat sesi presensi otomatis untuk Course
     */
    public static function findOrCreateForCourse(Course $course): self
    {
        return self::firstOrCreate(
            [
                'attendable_type' => Course::class,
                'attendable_id'   => $course->id,
            ],
            [
                'title'                  => 'Presensi Kelas: ' . $course->title,
                'session_code'           => 'CRS-' . str_pad($course->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4)),
                'date'                   => now()->toDateString(),
                'time_start'             => '08:00',
                'time_end'               => '16:00',
                'open_at'                => now()->startOfDay(),
                'close_at'               => now()->endOfDay(),
                'tolerance_late_minutes' => 30,
                'min_duration_minutes'   => max(30, ($course->duration_hours ?? 2) * 30),
                'allowed_methods'        => 'both',
                'is_active'              => true,
                'qr_secret_token'        => Str::random(40),
                'created_by'             => auth()->id(),
            ]
        );
    }
}
