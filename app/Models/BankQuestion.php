<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'difficulty',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'points'  => 'integer',
        'order'   => 'integer',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty) {
            'easy'   => 'Mudah',
            'hard'   => 'Sulit',
            default  => 'Sedang',
        };
    }
}
