<?php

namespace App\Repositories\Eloquent;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryContract;
use Illuminate\Support\Str;

class AuthorRepository implements AuthorRepositoryContract
{
    public function firstOrCreateByName(string $name): Author
    {
        return Author::query()->firstOrCreate([
            'normalized_name' => Str::lower($name)
        ], [
            'name' => $name
        ]);
    }
}