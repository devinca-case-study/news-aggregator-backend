<?php

namespace App\Repositories\Eloquent;

use App\Dto\ArticleFetchDto;
use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryContract;

class ArticleRepository implements ArticleRepositoryContract
{
    public function firstOrCreateFromFetchDto(ArticleFetchDto $dto): Article
    {
        return Article::query()->firstOrCreate(
            [
                'source_code' => $dto->sourceCode,
                'external_id' => $dto->externalId,
            ],
            $dto->toArray()
        );
    }

    public function attachCategory(Article $article, int $categoryId): void
    {
        $article->categories()->syncWithoutDetaching([$categoryId]);
    }
}