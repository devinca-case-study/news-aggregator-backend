<?php

namespace App\Services\Providers;

use App\Dto\ArticleFetchDto;
use App\Services\Contracts\NewsProviderContract;
use Illuminate\Http\Client\PendingRequest;
use RuntimeException;

class GuardianService extends AbstractNewsProvider implements NewsProviderContract
{
    public function providerName(): string
    {
        return 'guardian';
    }

    protected function endpoint(): string
    {
        return '/search';
    }

    protected function http(): PendingRequest
    {
        return parent::http()->withQueryParameters([
            'api-key' => $this->apiKey(),
        ]);
    }

    protected function buildRequestParams(array $rawNames, string $category, int $page): array
    {
        return [
            'section' => implode('|', $rawNames),
            'page-size' => $this->config('page_size'),
            'page' => $page,
            'order-by' => 'newest',
            'show-tags' => 'contributor',
            'show-fields' => 'bodyText',
        ];
    }

    protected function extractArticles(array $payload): array
    {
        $response = data_get($payload, 'response');

        if (($response['status'] ?? null) !== 'ok') {
            throw new RuntimeException('Guardian response status is not ok.');
        }

        return $response['results'] ?? [];
    }

    protected function mapArticle(array $article, string $category, string $syncedAt): ?ArticleFetchDto
    {
        $url = trim((string)($article['webUrl'] ?? ''));

        $tags = collect($article['tags'] ?? []);
        $contributors = $tags
            ->where('type', 'contributor')
            ->pluck('webTitle')
            ->filter()
            ->values()
            ->all();

        return new ArticleFetchDto(
            provider: $this->providerName(),
            externalId: data_get($article, 'id'),
            sourceCode: 'the-guardian',
            sourceName: 'The Guardian',
            url: $url,
            title: data_get($article, 'webTitle'),
            content: data_get($article, 'fields.bodyText'),
            authorName: !empty($contributors) ? implode(', ', $contributors) : null,
            publishedAt: data_get($article, 'webPublicationDate'),
            syncedAt: $syncedAt,
            rawCategory: data_get($article, 'sectionId'),
            meta: [
                'type' => data_get($article, 'type'),
                'apiUrl' => data_get($article, 'apiUrl'),
                'pillarId' => data_get($article, 'pillarId'),
                'pillarName' => data_get($article, 'pillarName'),
                'tags' => $article['tags'] ?? [],
            ]
        );
    }
}