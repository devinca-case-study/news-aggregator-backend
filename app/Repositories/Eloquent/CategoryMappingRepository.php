<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\CategoryMapping;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;

class CategoryMappingRepository implements CategoryMappingRepositoryContract
{
    public function resolveCategory(string $rawCategory): ?Category
    {
        $mapping = CategoryMapping::query()
            ->with('category')
            ->where('raw_name', $rawCategory)
            ->first();

        return $mapping?->category;
    }
}