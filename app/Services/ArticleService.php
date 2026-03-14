<?php

namespace App\Services;

use App\Dto\ArticleFilterDto;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Contracts\ArticleRepositoryContract;

class ArticleService
{
    public function __construct(
        protected ArticleRepositoryContract $articleRepository,
    ) {}

    public function getPaginatedArticles(ArticleFilterDto $dto, ?User $user)
    {
        return $this->articleRepository->paginateByFilter($dto, $user);
    }

    public function getDetailArticle(Article $article): Article
    {
        return $this->articleRepository->loadDetailRelations($article);
    }
}