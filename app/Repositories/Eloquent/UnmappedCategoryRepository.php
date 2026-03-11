<?php

namespace App\Repositories\Eloquent;

use App\Models\UnmappedCategory;
use App\Repositories\Contracts\UnmappedCategoryRepositoryContract;

class UnmappedCategoryRepository implements UnmappedCategoryRepositoryContract
{
    public function firstOrCreateAndMarkSeen(string $rawCategory): UnmappedCategory
    {
        $now = now();

        $unmappedCategory = UnmappedCategory::query()->firstOrCreate([
            'raw_name' => $rawCategory
        ], [
            'first_seen_at' => $now,
            'last_seen_at' => $now
        ]);

        $unmappedCategory->update([
            'last_seen_at' => $now,
        ]);

        return $unmappedCategory;
    }
}