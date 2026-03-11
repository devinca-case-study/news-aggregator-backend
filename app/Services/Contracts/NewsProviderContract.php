<?php

namespace App\Services\Contracts;

interface NewsProviderContract
{
    public function providerName(): string;
    public function fetch(array $params = []): array;
}