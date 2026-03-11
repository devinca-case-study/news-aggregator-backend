<?php

namespace App\Repositories\Contracts;

use App\Models\UnmappedCategory;

interface UnmappedCategoryRepositoryContract
{
    public function firstOrCreateAndMarkSeen(string $rawCategory): UnmappedCategory;
}