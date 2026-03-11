<?php

namespace App\Repositories\Contracts;

use App\Models\Category;

interface CategoryMappingRepositoryContract
{
    public function resolveCategory(string $rawCategory): ?Category;
}