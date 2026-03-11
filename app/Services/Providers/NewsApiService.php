<?php

namespace App\Services\Providers;

use App\Dto\ArticleFetchDto;
use App\Services\Contracts\NewsProviderContract;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class NewsApiService extends AbstractNewsProvider implements NewsProviderContract
{
    public function providerName(): string
    {
        return 'newsapi';
    }

    protected function http(): PendingRequest
    {
        return parent::http()->withHeaders([
            'x-api-key' => $this->apiKey(),
        ]);
    }

    public function fetch(array $params = []): array
    {
        $category = $params['category'] ?? null;

        if (!$category) {
            throw new RuntimeException('NewsAPI category is required.');
        }

        // NewsAPI has a rate limit of 100 requests per day on the free plan
        // To stay within this limit, we only fetch the first page with pageSize=100
        // The sync job runs every 15 minutes and rotates across 7 categories
        // ensuring fresh news without exceeding the daily API quota
        $response = $this->http()->get('/top-headlines', [
            'category' => $category,
            'pageSize' => $this->config('page_size'),
            'page' => 1
        ]);

        $this->throwIfFailed($response);

        $payload = $response->json();

        if (($payload['status'] ?? null) !== 'ok') {
            throw new RuntimeException('NewsAPI response status is not ok.');
        }

        $articles = $payload['articles'] ?? [];
        $syncedAt = now()->toDateTimeString();

        return collect($articles)
            ->map(fn ($article) => $this->mapArticle($article, $category, $syncedAt))
            ->filter()
            ->values()
            ->all();
    }

    protected function mapArticle(array $article, string $category, string $syncedAt): ?ArticleFetchDto
    {
        $url = trim((string)($article['url'] ?? ''));

        if ($url === '') {
            Log::warning('NewsAPI article skipped due to missing URL', [
                'article' => $article
            ]);
            
            return null;
        }

        $sourceName = data_get($article, 'source.name');

        return new ArticleFetchDto(
            provider: $this->providerName(),
            externalId: md5($url), // Use MD5 hash of the URL as a unique article identifier in news api because they don't have unique id.
            sourceCode: data_get($article, 'source.id') ?: $sourceName,
            sourceName: $sourceName,
            url: $url,
            title: data_get($article, 'title'),
            content: data_get($article, 'content'),
            authorName: data_get($article, 'author'),
            publishedAt: data_get($article, 'publishedAt'),
            syncedAt: $syncedAt,
            rawCategory: $category,
            meta: [
                'urlToImage' => data_get($article, 'urlToImage')
            ]
        );
    }
}