<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WebinarRegistration extends Model
{
    protected $fillable = [
        'webinar_id', 'user_id', 'status', 'ticket_code',
        'is_attended', 'attended_at', 'attendance_method',
        'attendance_token', 'notes',
    ];

    protected $casts = [
        'is_attended' => 'boolean',
        'attended_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $reg) {
            if (!$reg->ticket_code) {
                $reg->ticket_code = strtoupper('WBN-' . Str::random(8));
            }
            if (!$reg->attendance_token) {
                $reg->attendance_token = Str::uuid()->toString();
            }
        });
    }

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAttended(string $method = 'manual'): void
    {
        $this->update([
            'is_attended'       => true,
            'attended_at'       => now(),
            'attendance_method' => $method,
        ]);

        // Sync learning record
        WebinarLearningRecord::updateOrCreate(
            ['webinar_id' => $this->webinar_id, 'user_id' => $this->user_id],
            ['attended' => true]
        );
    }
}
