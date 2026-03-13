<?php

namespace App\Repositories\Contracts;

use App\Models\Source;
use Illuminate\Support\Collection;

interface SourceRepositoryContract
{
    public function firstOrCreateByName(string $name): Source;

    public function searchForSelect(?string $search = null, ?int $limit = null): Collection;
}