<?php

namespace App\Services\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractNewsProvider
{
    abstract public function providerName(): string;

    protected function config(string $key, mixed $default = null): mixed
    {
        return config("news.providers.{$this->providerName()}.{$key}", $default);
    }

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->config('base_url'))
            ->acceptJson()
            ->timeout(30)
            ->retry(3, 500);
    }

    protected function apiKey(): string
    {
        return $this->config('api_key');
    }

    protected function throwIfFailed(Response $response): void
    {
        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'Request failed for provider [%s]. Status: %s. Body: %s',
                $this->providerName(),
                $response->status(),
                $response->body()
            ));
        }
    }
}