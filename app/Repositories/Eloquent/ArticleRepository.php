<?php

namespace App\Repositories\Eloquent;

use App\Dto\ArticleFetchDto;
use App\Dto\ArticleFilterDto;
use App\Models\Article;
use App\Models\User;
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

    public function paginateByFilter(ArticleFilterDto $dto, ?User $user = null)
    {
        $query = Article::query()->with(Article::DETAIL_RELATIONS);

        $this->applySearch($query, $dto);
        $this->applyDateFilter($query, $dto);
        $this->applySourceFilter($query, $dto);
        $this->applyCategoryFilter($query, $dto);
        $this->applyAuthorFilter($query, $dto);

        if ($user) {
            $this->applyPreferenceRanking($query, $user);
            $query->orderByDesc('preference_score');
        }

        $query->orderBy($dto->sortBy, $dto->sortDirection);

        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }

    public function loadDetailRelations(Article $article): Article
    {
        return $article->loadMissing(Article::DETAIL_RELATIONS);
    }

    protected function applySearch($query, ArticleFilterDto $dto): void
    {
        if (empty($dto->search)) {
            return;
        }

        $search = $dto->search;

        $query->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhereHas('source', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('authors', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('categories', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
        });
    }

    protected function applyDateFilter($query, ArticleFilterDto $dto): void
    {
        if ($dto->dateFrom) {
            $query->whereDate('published_at', '>=', $dto->dateFrom);
        }

        if ($dto->dateTo) {
            $query->whereDate('published_at', '<=', $dto->dateTo);
        }
    }

    protected function applySourceFilter($query, ArticleFilterDto $dto): void
    {
        if (empty($dto->sourceIds)) {
            return;
        }

        $query->whereIn('source_id', $dto->sourceIds);
    }

    protected function applyCategoryFilter($query, ArticleFilterDto $dto): void
    {
        $categoryIds = $dto->categoryIds;

        if (empty($categoryIds)) {
            return;
        }

        $query->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        });
    }

    protected function applyAuthorFilter($query, ArticleFilterDto $dto): void
    {
        $authorIds = $dto->authorIds;

        if (empty($authorIds)) {
            return;
        }

        $query->whereHas('authors', function ($query) use ($authorIds) {
            $query->whereIn('authors.id', $authorIds);
        });
    }

    protected function applyPreferenceRanking($query, User $user): void
    {
        $sourceIds = $user->preferredSources()->pluck('sources.id')->all();
        $categoryIds = $user->preferredCategories()->pluck('categories.id')->all();
        $authorIds = $user->preferredAuthors()->pluck('authors.id')->all();

        $scoreParts = [];

        if (!empty($sourceIds)) {
            $ids = implode(',', $sourceIds);
            $scoreParts[] = "CASE WHEN articles.source_id IN ($ids) THEN 1 ELSE 0 END";
        }

        if (!empty($categoryIds)) {
            $ids = implode(',', $categoryIds);
            $scoreParts[] = "
                CASE WHEN EXISTS (
                    SELECT 1 FROM article_categories
                    WHERE article_categories.article_id = articles.id
                    AND article_categories.category_id IN ($ids)
                ) THEN 1 ELSE 0 END
            ";
        }

        if (!empty($authorIds)) {
            $ids = implode(',', $authorIds);
            $scoreParts[] = "
                CASE WHEN EXISTS (
                    SELECT 1 FROM article_authors
                    WHERE article_authors.article_id = articles.id
                    AND article_authors.author_id IN ($ids)
                ) THEN 1 ELSE 0 END
            ";
        }

        if (empty($scoreParts)) {
            $query->selectRaw("0 as preference_score");
            return;
        }

        $scoreExpression = implode(' + ', $scoreParts);

        $query->selectRaw("articles.*, ($scoreExpression) as preference_score");
    }
}