<?php

namespace App\Services;

use App\Dto\ArticleFilterDto;
use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryContract;

class ArticleService
{
    public function __construct(
        protected ArticleRepositoryContract $articleRepository,
    ) {}

    public function getPaginatedArticles(ArticleFilterDto $dto)
    {
        return $this->articleRepository->paginateByFilter($dto);
    }

    public function getDetailArticle(Article $article): Article
    {
        return $this->articleRepository->loadDetailRelations($article);
    }
}