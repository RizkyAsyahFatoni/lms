<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'training_name',
        'institution_name',
        'training_date',
        'training_end_date',
        'jp_count',
        'certificate_file',
        'supporting_file',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'year',
    ];

    protected $casts = [
        'training_date' => 'date',
        'training_end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Auto-set year from training_date when creating.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->year) && $model->training_date) {
                $model->year = $model->training_date->year;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status badge color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'emerald',
            'rejected' => 'red',
            default => 'amber',
        };
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu Review',
        };
    }
}
