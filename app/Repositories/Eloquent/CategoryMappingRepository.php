<?php

namespace App\Repositories\Eloquent;

use App\Models\CategoryMapping;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
use Illuminate\Support\Facades\Cache;

class CategoryMappingRepository implements CategoryMappingRepositoryContract
{
    public function findCategoryMapping(string $providerName, string $rawCategory): ?CategoryMapping
    {
        return CategoryMapping::query()
            ->where('provider', $providerName)
            ->where('raw_name', $rawCategory)
            ->first();
    }

    public function getMappedCategoryCodesByProvider(string $provider): array
    {
        $cacheKey = "category_mappings:codes:{$provider}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($provider) {
            return CategoryMapping::query()
                ->select('categories.code')
                ->join('categories', 'categories.id', '=', 'category_mappings.category_id')
                ->where('category_mappings.provider', $provider)
                ->whereNotNull('category_mappings.category_id')
                ->distinct()
                ->orderBy('categories.code')
                ->pluck('categories.code')
                ->all();
        });
    }

    public function getRawNamesByProviderAndCategoryCode(string $provider, string $categoryCode): array
    {
        $cacheKey = "category_mappings:raw_names:{$provider}:{$categoryCode}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($provider, $categoryCode) {
            return CategoryMapping::query()
                ->select('category_mappings.raw_name')
                ->join('categories', 'categories.id', '=', 'category_mappings.category_id')
                ->where('category_mappings.provider', $provider)
                ->where('categories.code', $categoryCode)
                ->orderBy('category_mappings.raw_name')
                ->pluck('category_mappings.raw_name')
                ->all();
        });
    }
}