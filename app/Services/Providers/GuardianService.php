<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProviderContract;

class GuardianService implements NewsProviderContract
{
    public function providerName(): string
    {
        return 'guardian';
    }

    public function fetch(array $params = []): array
    {
        return [];
    }
}