<?php

namespace App\Repositories\Contracts;

use App\Dto\ArticleFetchDto;
use App\Dto\ArticleFilterDto;
use App\Models\Article;
use App\Models\User;

interface ArticleRepositoryContract
{
    public function firstOrCreateFromFetchDto(ArticleFetchDto $dto, int $sourceId): Article;

    public function attachCategory(Article $article, int $categoryId): void;

    public function paginateByFilter(ArticleFilterDto $dto, ?User $user);

    public function loadDetailRelations(Article $article): Article;
}