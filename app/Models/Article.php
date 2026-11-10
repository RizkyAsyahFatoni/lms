<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'opd_name',
        'author_name',
        'excerpt',
        'content',
        'cover_image',
        'is_published',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'views_count'  => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Auto generate slug on creating/saving if empty
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            if (empty($article->slug)) {
                $baseSlug = Str::slug($article->title);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $article->id ?? 0)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                $article->slug = $slug;
            }

            if ($article->is_published && empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(ArticleAttachment::class);
    }

    /**
     * Scope artikel yang aktif / terpublikasi
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope pencarian kata kunci pada judul, excerpt, content, dan attachment
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        $term = '%' . trim($keyword) . '%';

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', $term)
              ->orWhere('excerpt', 'like', $term)
              ->orWhere('content', 'like', $term)
              ->orWhere('author_name', 'like', $term)
              ->orWhere('opd_name', 'like', $term)
              ->orWhereHas('attachments', function ($sub) use ($term) {
                  $sub->where('title', 'like', $term)
                      ->orWhere('file_name', 'like', $term);
              });
        });
    }

    /**
     * Scope filter kategori
     */
    public function scopeFilterCategory(Builder $query, ?string $category): Builder
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }

        return $query;
    }

    /**
     * Scope filter tipe lampiran
     */
    public function scopeFilterAttachmentType(Builder $query, ?string $type): Builder
    {
        if ($type && $type !== 'all') {
            return $query->whereHas('attachments', function ($q) use ($type) {
                $q->where('attachment_type', $type);
            });
        }

        return $query;
    }

    /**
     * Label dan styling kategori
     */
    public function getCategoryMetaAttribute(): array
    {
        return match ($this->category) {
            'regulasi'      => ['label' => 'Regulasi & Kebijakan', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => '⚖️'],
            'panduan'       => ['label' => 'Panduan & SOP', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => '📘'],
            'presentasi'    => ['label' => 'Materi Presentasi', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => '📊'],
            'best_practice' => ['label' => 'Best Practice & Inovasi', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200', 'icon' => '💡'],
            'materi_sharing'=> ['label' => 'Knowledge Sharing', 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => '🤝'],
            default         => ['label' => 'Artikel & Informasi', 'badge' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => '📰'],
        };
    }

    /**
     * Konten yang sudah disanitasi HTMLPurifier agar aman dari XSS
     * namun tetap mendukung heading, list, gambar, table, dan pemformatan teks kaya
     */
    public function getCleanContentAttribute(): string
    {
        if (class_exists(\HTMLPurifier::class)) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('HTML.SafeIframe', true);
            $config->set('HTML.Allowed', 'p,b,strong,i,em,u,s,strike,h1,h2,h3,h4,h5,h6,ul,ol,li,blockquote,code,pre,img[src|alt|title|width|height|class],a[href|target|title],table,thead,tbody,tr,th,td,span[style],br,hr');
            $config->set('Attr.EnableID', true);
            $purifier = new \HTMLPurifier($config);
            return $purifier->purify($this->content);
        }

        return $this->content;
    }

    /**
     * Ringkasan tipe-tipe lampiran yang tersedia
     */
    public function getAttachmentTypesSummaryAttribute(): array
    {
        return $this->attachments->pluck('attachment_type')->unique()->values()->all();
    }
}
