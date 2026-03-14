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
        protected UserPreferenceService $userPreferenceService,
    ) {}

    public function getPaginatedArticles(ArticleFilterDto $dto, ?User $user)
    {
        $preferences = $user 
            ? $this->userPreferenceService->getCachedPreferenceIds($user) 
            : null;

        return $this->articleRepository->paginateByFilter($dto, $preferences);
    }

    public function getDetailArticle(Article $article): Article
    {
        return $this->articleRepository->loadDetailRelations($article);
    }
}