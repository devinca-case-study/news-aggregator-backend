<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'preferences_completed_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'preferences_completed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public const USER_PREFERRED_RELATIONS = [
        'preferredSources:id,name',
        'preferredCategories:id,name',
        'preferredAuthors:id,name',
    ];

    public function preferredSources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class, 'user_preferred_sources')->withTimestamps();
    }

    public function preferredCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'user_preferred_categories')->withTimestamps();
    }

    public function preferredAuthors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'user_preferred_authors')->withTimestamps();
    }
}
