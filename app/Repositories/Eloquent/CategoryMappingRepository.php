<?php

namespace App\Repositories\Eloquent;

use App\Models\CategoryMapping;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;

class CategoryMappingRepository implements CategoryMappingRepositoryContract
{
    public function resolveCategoryMapping(string $providerName, string $rawCategory): ?CategoryMapping
    {
        $data = [
            'provider' => $providerName,
            'raw_name' => $rawCategory
        ];
        return CategoryMapping::query()->firstOrCreate($data, $data);
    }
}