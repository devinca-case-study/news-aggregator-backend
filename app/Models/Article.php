<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = [
        'provider',
        'external_id',
        'source_id',
        'url',
        'title',
        'content',
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
        return $this->belongsToMany(Category::class, 'article_categories')->withTimestamps();
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'article_authors')->withTimestamps();
    }
}
