<?php

namespace App\Services\Providers;

use App\Dto\ArticleFetchDto;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
use App\Support\StringHelper;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

abstract class AbstractNewsProvider
{
    public function __construct(
        protected CategoryMappingRepositoryContract $categoryMappingRepository
    ) {}

    abstract public function providerName(): string;

    abstract protected function endpoint(): string;

    abstract protected function buildRequestParams(array $rawNames, string $category, int $page): array;

    abstract protected function extractArticles(array $payload): array;

    abstract protected function mapArticle(array $article, string $category, string $syncedAt): ?ArticleFetchDto;

    protected function config(string $key, mixed $default = null): mixed
    {
        return config("news.providers.{$this->providerName()}.{$key}", $default);
    }

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->config('base_url'))
            ->acceptJson();
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

    protected function requireCategory(array $params): string
    {
        $category = $params['category'] ?? null;

        if (!$category) {
            throw new RuntimeException("{$this->providerName()} category is required.");
        }

        return $category;
    }

    protected function getRawNamesByCategory(string $category): array
    {
        $rawNames = $this->categoryMappingRepository->getRawNamesByProviderAndCategoryCode($this->providerName(), $category);

        if (empty($rawNames)) {
            throw new RuntimeException("No {$this->providerName()} category mapping found for [{$category}].");
        }

        return $rawNames;
    }

    protected function makeRequest(array $rawNames, string $category, int $page): Response
    {
        return $this->http()->get(
            $this->endpoint(),
            $this->buildRequestParams($rawNames, $category, $page)
        );
    }

    public function fetch(array $params = []): array
    {
        $category = $this->requireCategory($params);
        $rawNames = $this->getRawNamesByCategory($category);
        $syncedAt = now()->toDateTimeString();

        $page = $params['page'] ?? 1;

        $response = $this->makeRequest($rawNames, $category, $page);

        $this->throwIfFailed($response);

        $payload = $response->json();
        $articles = $this->extractArticles($payload);

        foreach ($articles as $article) {
            $mapped = $this->mapArticle($article, $category, $syncedAt);

            if ($mapped) {
                $results[] = $mapped;
            }
        }

        return $results;
    }

    protected function wrapSingleAuthor(?string $author): array
    {
        $author = StringHelper::clean($author);
        return $author !== '' ? [$author] : [];
    }
}