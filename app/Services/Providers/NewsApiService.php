<?php

namespace App\Services\Providers;

use App\Dto\ArticleFetchDto;
use App\Services\Contracts\NewsProviderContract;
use App\Support\StringHelper;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class NewsApiService extends AbstractNewsProvider implements NewsProviderContract
{
    public function providerName(): string
    {
        return 'newsapi';
    }

    protected function endpoint(): string
    {
        return '/top-headlines';
    }

    protected function http(): PendingRequest
    {
        return parent::http()->withHeaders([
            'x-api-key' => $this->apiKey(),
        ]);
    }

    protected function buildRequestParams(array $rawNames, string $category, int $page): array
    {
        return [
            'category' => $rawNames[0],
            'pageSize' => $this->config('page_size'),
            'page' => $page,
        ];
    }

    protected function extractArticles(array $payload): array
    {
        if (($payload['status'] ?? null) !== 'ok') {
            throw new RuntimeException('NewsAPI response status is not ok.');
        }

        return $payload['articles'] ?? [];
    }

    protected function mapArticle(array $article, string $category, string $syncedAt): ?ArticleFetchDto
    {
        $url = StringHelper::cleanUrl(data_get($article, 'url'));

        if ($url === '') {
            Log::warning('NewsAPI article skipped due to missing URL', [
                'article' => $article
            ]);
            
            return null;
        }

        return new ArticleFetchDto(
            provider: $this->providerName(),
            externalId: md5($url), // Use MD5 hash of the URL as a unique article identifier in news api because they don't have unique id.
            sourceName: trim((string)data_get($article, 'source.name')),
            url: $url,
            imageUrl: data_get($article, 'urlToImage'),
            title: data_get($article, 'title'),
            content: data_get($article, 'content'),
            authors: $this->wrapMultipleAuthors(data_get($article, 'author')),
            publishedAt: data_get($article, 'publishedAt'),
            syncedAt: $syncedAt,
            rawCategory: $category,
            meta: [
                'sourceId' => data_get($article, 'source.id'),
                'sourceName' => data_get($article, 'source.name')
            ]
        );
    }
}