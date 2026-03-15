<?php

namespace App\Repositories\Eloquent;

use App\Models\Source;
use App\Repositories\Contracts\SourceRepositoryContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SourceRepository implements SourceRepositoryContract
{
    public function firstOrCreateByName(string $name): Source
    {
        return Source::query()->firstOrCreate([
            'code' => Str::slug($name)
        ], [
            'name' => $name
        ]);
    }

    public function searchForSelect(?string $search = null, ?int $limit = null, ?array $excludeIds = null): Collection
    {
        $query = Source::query()
            ->select(['id', 'name'])
            ->orderBy('name');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        if (!empty($limit)) {
            $query->limit($limit);
        }

        return $query->get();
    }
}