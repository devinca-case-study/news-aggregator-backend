<?php

namespace App\Services\Providers;

use App\Dto\ArticleFetchDto;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
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

    protected function firstPage(): int
    {
        return 1;
    }

    protected function lastPage(): int
    {
        return $this->firstPage() + $this->config('total_page', 1) - 1;
    }

    protected function sleepSecondsBetweenPages(): int
    {
        return 0;
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

        $results = [];
        $page = $this->firstPage();

        // Stop pagination on failure to avoid repeated errors (rate limit, API issues).
        // Return the articles successfully fetched so far.
        while (true) {
            try {
                $response = $this->makeRequest($rawNames, $category, $page);

                if ($response->failed()) {
                    Log::warning("{$this->providerName()} request failed", [
                        'page' => $page,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    break;
                }

                $payload = $response->json();
                $articles = $this->extractArticles($payload);

                foreach ($articles as $article) {
                    $mapped = $this->mapArticle($article, $category, $syncedAt);

                    if ($mapped) {
                        $results[] = $mapped;
                    }
                }

                if (count($articles) == 0 || $page >= $this->lastPage()) {
                    break;
                }

                $sleepSeconds = $this->sleepSecondsBetweenPages();
                if ($sleepSeconds > 0) {
                    sleep($sleepSeconds);
                }

                $page++;
            } catch (Throwable $th) {
                Log::error("{$this->providerName()} fetch failed", [
                    'page' => $page,
                    'message' => $th->getMessage(),
                ]);
                break;
            }
        }

        return $results;
    }
}