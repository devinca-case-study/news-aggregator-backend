<?php

namespace App\Repositories\Contracts;

use App\Dto\ArticleFetchDto;
use App\Dto\ArticleFilterDto;
use App\Models\Article;

interface ArticleRepositoryContract
{
    public function firstOrCreateFromFetchDto(ArticleFetchDto $dto, int $sourceId): Article;

    public function attachCategory(Article $article, int $categoryId): void;

    public function paginateByFilter(ArticleFilterDto $dto, ?array $preferences = null);

    public function loadDetailRelations(Article $article): Article;
}