<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProviderContract;
use Illuminate\Http\Client\PendingRequest;

class GuardianService extends AbstractNewsProvider implements NewsProviderContract
{
    public function providerName(): string
    {
        return 'guardian';
    }

    protected function http(): PendingRequest
    {
        return parent::http()->withQueryParameters([
            'api-key' => $this->apiKey(),
        ]);
    }

    public function fetch(array $params = []): array
    {
        return [];
    }
}