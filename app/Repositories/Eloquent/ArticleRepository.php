<?php

namespace App\Repositories\Eloquent;

use App\Dto\ArticleFetchDto;
use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryContract;

class ArticleRepository implements ArticleRepositoryContract
{
    public function firstOrCreateFromFetchDto(ArticleFetchDto $dto, int $sourceId): Article
    {
        return Article::query()->firstOrCreate([
            'provider' => $dto->provider,
            'external_id' => $dto->externalId,
        ], [
            ...$dto->toArray(),
            'source_id' => $sourceId
        ]);
    }

    public function attachCategory(Article $article, int $categoryId): void
    {
        $article->categories()->syncWithoutDetaching([$categoryId]);
    }
}