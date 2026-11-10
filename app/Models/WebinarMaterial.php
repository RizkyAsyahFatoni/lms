<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarMaterial extends Model
{
    protected $fillable = [
        'webinar_id', 'title', 'type', 'file_url', 'external_url', 'order', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'order'     => 'integer',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'video'        => '📹',
            'link'         => '🔗',
            'presentation' => '📊',
            default        => '📄',
        };
    }

    public function getDownloadUrlAttribute(): ?string
    {
        return $this->file_url ?? $this->external_url;
    }
}
