<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_title',
        'title',
        'slug',
        'order',
        'type', // 'video', 'pdf', 'link', 'pre_test', 'post_test', 'assignment', 'reading'
        'video_url',
        'pdf_url',
        'external_url',
        'content',
        'duration_minutes',
        'is_free_preview',
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function quiz(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function assignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function isCompletedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->progress()->where('user_id', $user->id)->where('is_completed', true)->exists();
    }
}
