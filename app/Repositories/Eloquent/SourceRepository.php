<?php

namespace App\Repositories\Eloquent;

use App\Models\Source;
use App\Repositories\Contracts\SourceRepositoryContract;
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
}