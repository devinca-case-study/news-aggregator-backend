<?php

namespace App\Repositories\Contracts;

use App\Models\CategoryMapping;

interface CategoryMappingRepositoryContract
{
    public function resolveCategoryMapping(string $providerName, string $rawCategory): ?CategoryMapping;
}