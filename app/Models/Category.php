<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function mappings(): HasMany
    {
        return $this->hasMany(CategoryMapping::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_categories');
    }
}
