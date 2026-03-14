<?php

namespace App\Repositories\Eloquent;

use App\Dto\ArticleFetchDto;
use App\Dto\ArticleFilterDto;
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

    public function paginateByFilter(ArticleFilterDto $dto, ?array $preferences = null)
    {
        $query = Article::query()->with(Article::DETAIL_RELATIONS);

        $this->applySearch($query, $dto);
        $this->applyDateFilter($query, $dto);
        $this->applySourceFilter($query, $dto);
        $this->applyCategoryFilter($query, $dto);
        $this->applyAuthorFilter($query, $dto);

        if (!empty($preferences)) {
            $this->applyPreferenceRanking($query, $preferences);
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

    protected function applyPreferenceRanking($query, array $preferences): void
    {
        $sourceIds = $preferences['source_ids'] ?? [];
        $categoryIds = $preferences['category_ids'] ?? [];
        $authorIds = $preferences['author_ids'] ?? [];

        $scoreParts = [];
        $bindings = [];

        if (!empty($sourceIds)) {
            $placeholders = $this->makePlaceholders($sourceIds);
            $scoreParts[] = "CASE WHEN articles.source_id IN ($placeholders) THEN 1 ELSE 0 END";
            $bindings = array_merge($bindings, $sourceIds);
        }

        if (!empty($categoryIds)) {
            $placeholders = $this->makePlaceholders($categoryIds);
            $scoreParts[] = "
                CASE WHEN EXISTS (
                    SELECT 1 FROM article_categories
                    WHERE article_categories.article_id = articles.id
                    AND article_categories.category_id IN ($placeholders)
                ) THEN 1 ELSE 0 END
            ";
            $bindings = array_merge($bindings, $categoryIds);
        }

        if (!empty($authorIds)) {
            $placeholders = $this->makePlaceholders($authorIds);
            $scoreParts[] = "
                CASE WHEN EXISTS (
                    SELECT 1 FROM article_authors
                    WHERE article_authors.article_id = articles.id
                    AND article_authors.author_id IN ($placeholders)
                ) THEN 1 ELSE 0 END
            ";
            $bindings = array_merge($bindings, $authorIds);
        }

        if (empty($scoreParts)) {
            $query->selectRaw('articles.*, 0 as preference_score');
            return;
        }

        $scoreExpression = implode(' + ', $scoreParts);

        $query->selectRaw("articles.*, ($scoreExpression) as preference_score", $bindings);
    }

    protected function makePlaceholders(array $values): string
    {
        return implode(',', array_fill(0, count($values), '?'));
    }
}