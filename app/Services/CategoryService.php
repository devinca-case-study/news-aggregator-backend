<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryContract;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryContract $categoryRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->categoryRepository->all();
    }
}