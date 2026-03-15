<?php

namespace App\Services;

use App\Repositories\Contracts\AuthorRepositoryContract;
use App\Repositories\Contracts\CategoryRepositoryContract;
use App\Repositories\Contracts\SourceRepositoryContract;
use Illuminate\Support\Collection;

class FilterService
{
    public function __construct(
        protected CategoryRepositoryContract $categoryRepository,
        protected AuthorRepositoryContract $authorRepository,
        protected SourceRepositoryContract $sourceRepository,
    ) {}

    public function getCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function searchAuthorsForSelect(?string $search = null, int $limit = 10, ?array $excludeIds = null): Collection
    {
        return $this->authorRepository->searchForSelect($search, $limit, $excludeIds);
    }

    public function searchSourcesForSelect(?string $search = null, ?int $limit = null, ?array $excludeIds = null): Collection
    {
        return $this->sourceRepository->searchForSelect($search, $limit, $excludeIds);
    }
}