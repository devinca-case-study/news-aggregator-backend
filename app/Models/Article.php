<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = [
        'provider',
        'external_id',
        'source_code',
        'source_name',
        'url',
        'title',
        'content',
        'author_name',
        'published_at',
        'synced_at',
        'meta'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'synced_at' => 'datetime',
        'meta' => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_categories');
    }

    public function unmappedCategories(): BelongsToMany
    {
        return $this->belongsToMany(UnmappedCategory::class, 'article_unmapped_categories');
    }
}
