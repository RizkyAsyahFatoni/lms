<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdpPlan extends Model
{
    protected $table = 'idp_plans';

    protected $fillable = [
        'nip',
        'nama_asn',
        'tahun',
        'judul_materi',
        'uploaded_by',
        'batch_id',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    /**
     * Relasi ke User yang mengupload (Admin).
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope: Filter berdasarkan NIP tertentu.
     */
    public function scopeForNip($query, string $nip)
    {
        return $query->where('nip', $nip);
    }

    /**
     * Scope: Filter berdasarkan Tahun IDP.
     */
    public function scopeForYear($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Cari kursus yang cocok dengan judul materi IDP (fuzzy match via LIKE).
     * Mengembalikan Course model jika ditemukan, null jika tidak.
     */
    public function matchedCourse(): ?Course
    {
        return Course::where('is_published', true)
            ->where(function ($q) {
                $words = collect(preg_split('/[\s,\-\/]+/', $this->judul_materi))
                    ->filter(fn($w) => mb_strlen(trim($w)) > 3)
                    ->take(5);

                foreach ($words as $word) {
                    $word = trim($word);
                    $q->orWhere('title', 'LIKE', '%' . $word . '%')
                      ->orWhere('sub_category', 'LIKE', '%' . $word . '%')
                      ->orWhere('tags', 'LIKE', '%' . $word . '%')
                      ->orWhereHas('category', function ($cq) use ($word) {
                          $cq->where('name', 'LIKE', '%' . $word . '%');
                      });
                }
            })
            ->first();
    }
}
