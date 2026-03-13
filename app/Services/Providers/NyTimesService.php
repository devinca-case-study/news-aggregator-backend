<?php

namespace App\Services\Providers;

use App\Dto\ArticleFetchDto;
use App\Services\Contracts\NewsProviderContract;
use App\Support\StringHelper;
use Illuminate\Http\Client\PendingRequest;

class NyTimesService extends AbstractNewsProvider implements NewsProviderContract
{
    public function providerName(): string
    {
        return 'nytimes';
    }

    protected function endpoint(): string
    {
        return '/articlesearch.json';
    }

    protected function http(): PendingRequest
    {
        return parent::http()->withQueryParameters([
            'api-key' => $this->apiKey(),
        ]);
    }

    protected function firstPage(): int
    {
        return 0;
    }

    protected function sleepSecondsBetweenPages(): int
    {
        return 12;
    }

    protected function buildSectionFilterQuery(array $sections): string
    {
        $quoted = collect($sections)
            ->map(fn (string $section) => '"' . $section . '"')
            ->implode(',');

        return "section.name:({$quoted})";
    }

    protected function buildRequestParams(array $rawNames, string $category, int $page): array
    {
        return [
            'fq' => $this->buildSectionFilterQuery($rawNames),
            'sort' => 'newest',
            'page' => $page,
        ];
    }

    protected function extractArticles(array $payload): array
    {
        return data_get($payload, 'response.docs', []);
    }

    protected function mapArticle(array $article, string $category, string $syncedAt): ?ArticleFetchDto
    {
        return new ArticleFetchDto(
            provider: $this->providerName(),
            externalId: data_get($article, '_id'),
            sourceName: 'The New York Times',
            url: StringHelper::cleanUrl(data_get($article, 'web_url')),
            title: data_get($article, 'headline.main'),
            content: data_get($article, 'abstract'),
            authors: $this->wrapSingleAuthor(data_get($article, 'byline.original')),
            publishedAt: data_get($article, 'pub_date'),
            syncedAt: $syncedAt,
            rawCategory: data_get($article, 'section_name'),
            meta: [
                'document_type' => data_get($article, 'document_type'),
                'keywords' => $article['keywords'] ?? [],
                'multimedia' => data_get($article, 'multimedia'),
                'news_desk' => data_get($article, 'news_desk'),
                'snippet' => data_get($article, 'snippet'),
                'subsection_name' => data_get($article, 'subsection_name'),
                'type_of_material' => data_get($article, 'type_of_material'),
                'uri' => data_get($article, 'uri'),
            ]
        );
    }
}