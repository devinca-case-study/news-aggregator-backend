<?php

namespace App\Repositories\Contracts;

use App\Models\Author;
use Illuminate\Support\Collection;

interface AuthorRepositoryContract
{
    public function firstOrCreateByName(string $name): Author;

    public function searchForSelect(?string $search = null, int $limit = 10): Collection;
}