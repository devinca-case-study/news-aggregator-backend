<?php

namespace App\Repositories\Contracts;

use App\Models\Source;

interface SourceRepositoryContract
{
    public function firstOrCreateByName(string $name): Source;
}