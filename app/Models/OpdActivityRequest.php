<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpdActivityRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'submitted_by',
        'course_id',
        'title',
        'description',
        'opd_name',
        'learning_method',
        'course_type',
        'jp',
        'target_audience',
        'objectives',
        'speaker_name',
        'proposed_start_date',
        'proposed_end_date',
        'proposed_location',
        'estimated_participants',
        'budget_source',
        'supporting_notes',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'revision_request',
    ];

    protected $casts = [
        'proposed_start_date' => 'date',
        'proposed_end_date'   => 'date',
        'reviewed_at'         => 'datetime',
        'estimated_participants' => 'integer',
        'jp'                  => 'integer',
    ];

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'         => 'Menunggu Review',
            'under_review'    => 'Sedang Ditinjau',
            'approved'        => 'Disetujui',
            'rejected'        => 'Ditolak',
            'revision_needed' => 'Perlu Revisi',
            default           => 'Draft',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'         => 'amber',
            'under_review'    => 'blue',
            'approved'        => 'emerald',
            'rejected'        => 'rose',
            'revision_needed' => 'orange',
            default           => 'slate',
        };
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
