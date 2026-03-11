<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProviderContract;

class NyTimesService implements NewsProviderContract
{
    public function providerName(): string
    {
        return 'nytimes';
    }

    public function fetch(array $params = []): array
    {
        return [];
    }
}