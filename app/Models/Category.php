<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'badge_color',
        'description',
        'tags',
    ];

    /**
     * Kategori Induk (Parent Category).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Sub Kategori (Children Categories).
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name', 'asc');
    }

    /**
     * Alias untuk subCategories.
     */
    public function children(): HasMany
    {
        return $this->subCategories();
    }

    /**
     * Kursus dengan kategori ini sebagai Kategori Utama.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /**
     * Kursus dengan kategori ini sebagai Sub Kategori.
     */
    public function subCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'sub_category_id');
    }

    /**
     * Total semua kursus (baik sebagai main maupun sub).
     */
    public function getTotalCoursesCountAttribute(): int
    {
        if ($this->parent_id === null) {
            // Main category: count direct courses + courses in subcategories
            $subIds = $this->subCategories()->pluck('id');
            return Course::where('category_id', $this->id)
                ->orWhereIn('sub_category_id', $subIds)
                ->count();
        }
        return Course::where('sub_category_id', $this->id)->count();
    }

    /**
     * Scope: Kategori Utama Saja (Parent is null).
     */
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Sub Kategori Saja (Parent is not null).
     */
    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Helper: Parse tags menjadi array bersih.
     */
    public function getTagsArrayAttribute(): array
    {
        if (empty($this->tags)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->tags))));
    }
}
