<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArticleAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'title',
        'attachment_type',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'external_url',
        'downloads_count',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'downloads_count' => 'integer',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * URL untuk download atau akses file
     */
    public function getDownloadUrlAttribute(): string
    {
        if ($this->external_url) {
            return $this->external_url;
        }

        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return '#';
    }

    /**
     * Format ukuran file manusiawi (KB / MB)
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) {
            return $this->external_url ? 'Tautan Luar' : '-';
        }

        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Badge visual tipe attachment
     */
    public function getTypeBadgeAttribute(): array
    {
        return match ($this->attachment_type) {
            'regulasi'   => ['label' => 'Regulasi / Hukum', 'bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => '⚖️'],
            'panduan'    => ['label' => 'Panduan / SOP', 'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => '📘'],
            'presentasi' => ['label' => 'Materi Presentasi', 'bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => '📊'],
            'modul'      => ['label' => 'Modul Belajar', 'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => '📑'],
            default      => ['label' => 'Dokumen Lampiran', 'bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'icon' => '📎'],
        };
    }

    /**
     * Icon dan warna berdasarkan ekstensi file
     */
    public function getExtensionStyleAttribute(): array
    {
        if ($this->external_url) {
            $isGdrive = str_contains($this->external_url, 'drive.google.com');
            return [
                'name'  => $isGdrive ? 'GDRIVE' : 'LINK',
                'bg'    => 'bg-emerald-600',
                'text'  => 'text-white',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'
            ];
        }

        $ext = strtolower($this->file_extension ?? '');

        return match ($ext) {
            'pdf' => [
                'name' => 'PDF',
                'bg' => 'bg-red-500',
                'text' => 'text-white',
                'badge' => 'bg-red-50 text-red-700 border-red-200'
            ],
            'ppt', 'pptx' => [
                'name' => 'PPT',
                'bg' => 'bg-orange-500',
                'text' => 'text-white',
                'badge' => 'bg-orange-50 text-orange-700 border-orange-200'
            ],
            'doc', 'docx' => [
                'name' => 'DOC',
                'bg' => 'bg-blue-600',
                'text' => 'text-white',
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200'
            ],
            'xls', 'xlsx' => [
                'name' => 'XLS',
                'bg' => 'bg-emerald-600',
                'text' => 'text-white',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'
            ],
            'zip', 'rar' => [
                'name' => 'ZIP',
                'bg' => 'bg-purple-600',
                'text' => 'text-white',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200'
            ],
            default => [
                'name' => strtoupper($ext ?: 'FILE'),
                'bg' => 'bg-slate-600',
                'text' => 'text-white',
                'badge' => 'bg-slate-50 text-slate-700 border-slate-200'
            ]
        };
    }
}
