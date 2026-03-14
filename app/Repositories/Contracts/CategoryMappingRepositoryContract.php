<?php

namespace App\Repositories\Contracts;

use App\Models\CategoryMapping;

interface CategoryMappingRepositoryContract
{
    public function findCategoryMapping(string $providerName, string $rawCategory): ?CategoryMapping;

    public function getMappedCategoryCodesByProvider(string $provider): array;

    public function getRawNamesByProviderAndCategoryCode(string $provider, string $categoryCode): array;
}