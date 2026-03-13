<?php

namespace App\Services\Providers;

use App\Dto\ArticleFetchDto;
use App\Services\Contracts\NewsProviderContract;
use App\Support\StringHelper;
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

    protected function extractGuardianAuthors(array $tags): array
    {
        return collect($tags)
            ->filter(fn (array $tag) => data_get($tag, 'type') === 'contributor')
            ->map(fn (array $tag) => StringHelper::clean(data_get($tag, 'webTitle')))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function mapArticle(array $article, string $category, string $syncedAt): ?ArticleFetchDto
    {
        return new ArticleFetchDto(
            provider: $this->providerName(),
            externalId: data_get($article, 'id'),
            sourceName: 'The Guardian',
            url: StringHelper::cleanUrl(data_get($article, 'webUrl')),
            title: data_get($article, 'webTitle'),
            content: data_get($article, 'fields.bodyText'),
            authors: $this->extractGuardianAuthors($article['tags'] ?? []),
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