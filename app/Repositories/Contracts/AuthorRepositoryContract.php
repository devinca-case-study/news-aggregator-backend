<?php

namespace App\Repositories\Contracts;

use App\Models\Author;

interface AuthorRepositoryContract
{
    public function firstOrCreateByName(string $name): Author;
}