<?php

namespace App\Repositories\Eloquent;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AuthorRepository implements AuthorRepositoryContract
{
    public function firstOrCreateByName(string $name): Author
    {
        return Author::query()->firstOrCreate([
            'code' => Str::slug($name)
        ], [
            'name' => $name
        ]);
    }

    public function searchForSelect(?string $search = null, int $limit = 10): Collection
    {
        if (empty($search)) {
            return collect();
        }

        return Author::query()
            ->select(['id', 'name'])
            ->where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }
}