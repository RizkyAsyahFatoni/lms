<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarLearningRecord extends Model
{
    protected $fillable = [
        'webinar_id', 'user_id',
        'registered', 'attended', 'material_downloaded',
        'evaluation_submitted', 'certificate_issued',
        'jp_earned', 'certificate_number', 'completed_at',
    ];

    protected $casts = [
        'registered'           => 'boolean',
        'attended'             => 'boolean',
        'material_downloaded'  => 'boolean',
        'evaluation_submitted' => 'boolean',
        'certificate_issued'   => 'boolean',
        'completed_at'         => 'datetime',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Persentase progres kegiatan (0–100) */
    public function getProgressPercentAttribute(): int
    {
        $checkpoints = [
            $this->registered,
            $this->attended,
            $this->material_downloaded,
            $this->evaluation_submitted,
        ];
        $done = count(array_filter($checkpoints));
        return (int) round(($done / count($checkpoints)) * 100);
    }
}
