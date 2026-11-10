<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating_materi',
        'rating_instruktur',
        'rating_platform',
        'rating_relevansi',
        'rating_overall',
        'saran_materi',
        'komentar_umum',
        'will_recommend',
        'pretest_score',
        'posttest_score',
        'assignment_score',
        'final_score',
        'submitted_at',
    ];

    protected $casts = [
        'will_recommend' => 'boolean',
        'final_score'    => 'decimal:2',
        'submitted_at'   => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung rata-rata rating reaksi peserta.
     */
    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->rating_materi,
            $this->rating_instruktur,
            $this->rating_platform,
            $this->rating_relevansi,
            $this->rating_overall,
        ]);

        if (empty($ratings)) {
            return 0;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }
}
