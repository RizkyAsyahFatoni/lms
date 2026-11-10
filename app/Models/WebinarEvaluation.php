<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarEvaluation extends Model
{
    protected $fillable = [
        'webinar_id', 'user_id',
        'rating_materi', 'rating_narasumber', 'rating_platform',
        'rating_relevansi', 'rating_overall',
        'saran', 'komentar', 'will_recommend', 'submitted_at',
    ];

    protected $casts = [
        'will_recommend' => 'boolean',
        'submitted_at'   => 'datetime',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->rating_materi,
            $this->rating_narasumber,
            $this->rating_platform,
            $this->rating_relevansi,
            $this->rating_overall,
        ]);
        return empty($ratings) ? 0 : round(array_sum($ratings) / count($ratings), 1);
    }
}
