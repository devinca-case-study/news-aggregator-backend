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

    public function getMappedCategoryCodesByProvider(string $provider): array
    {
        return CategoryMapping::query()
            ->select('categories.code')
            ->join('categories', 'categories.id', '=', 'category_mappings.category_id')
            ->where('category_mappings.provider', $provider)
            ->whereNotNull('category_mappings.category_id')
            ->distinct()
            ->orderBy('categories.code')
            ->pluck('categories.code')
            ->all();
    }

    public function getRawNamesByProviderAndCategoryCode(string $provider, string $categoryCode): array
    {
        return CategoryMapping::query()
            ->select('category_mappings.raw_name')
            ->join('categories', 'categories.id', '=', 'category_mappings.category_id')
            ->where('category_mappings.provider', $provider)
            ->where('categories.code', $categoryCode)
            ->orderBy('category_mappings.raw_name')
            ->pluck('category_mappings.raw_name')
            ->all();
    }
}