<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryContract;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
use App\Repositories\Contracts\UnmappedCategoryRepositoryContract;
use App\Services\Contracts\NewsProviderContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class NewsAggregatorService
{
    public function __construct(
        protected ArticleRepositoryContract $articleRepository,
        protected CategoryMappingRepositoryContract $categoryMappingRepository,
        protected UnmappedCategoryRepositoryContract $unmappedCategoryRepository,
        protected array $providers = [],
    ) {}

    public function fetchAndStore(string $providerName, array $params = [])
    {
        $providerService = $this->getProviderService($providerName);

        $articles = $providerService->fetch($params);

        $count = 0;

        foreach ($articles as $articleData) {
            DB::transaction(function () use ($articleData) {
                $article = $this->articleRepository->firstOrCreateFromFetchDto($articleData);

                $this->attachCategory($article, $articleData->rawCategory);
            });

            $count++;
        }

        Log::info('Articles fetched and stored successfully.', [
            'provider' => $providerName,
            'params' => $params,
            'count' => $count,
        ]);

        return $count;
    }

    protected function attachCategory(Article $article, ?string $rawCategory): void
    {
        if (blank($rawCategory)) {
            return;
        }

        $rawCategory = Str::slug($rawCategory);

        $category = $this->categoryMappingRepository->resolveCategory($rawCategory);

        if ($category) {
            $this->articleRepository->attachCategory($article, $category->id);
            return;
        } 

        $unmappedCategory = $this->unmappedCategoryRepository->firstOrCreateAndMarkSeen($rawCategory);

        $this->articleRepository->attachUnmappedCategory($article, $unmappedCategory->id);

        Log::warning('Unmapped category encountered', [
            'raw_category' => $rawCategory,
            'article_id' => $article->id,
            'unmapped_category_id' => $unmappedCategory->id,
        ]);
    }

    protected function getProviderService(string $providerName): NewsProviderContract
    {
        if (!isset($this->providers[$providerName])) {
            throw new RuntimeException("Unsupported provider: {$providerName}");
        }

        return $this->providers[$providerName];
    }
}