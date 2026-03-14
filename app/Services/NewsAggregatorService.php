<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryContract;
use App\Repositories\Contracts\AuthorRepositoryContract;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
use App\Repositories\Contracts\SourceRepositoryContract;
use App\Services\Contracts\NewsProviderContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class NewsAggregatorService
{
    public function __construct(
        protected ArticleRepositoryContract $articleRepository,
        protected AuthorRepositoryContract $authorRepository,
        protected SourceRepositoryContract $sourceRepository,
        protected CategoryMappingRepositoryContract $categoryMappingRepository,
        protected array $providers = [],
    ) {}

    public function fetchAndStore(string $providerName, array $params = [])
    {
        $providerService = $this->getProviderService($providerName);

        $articles = $providerService->fetch($params);

        $count = 0;

        foreach ($articles as $articleData) {
            $source = $this->sourceRepository->firstOrCreateByName($articleData->sourceName);
            $article = $this->articleRepository->updateOrCreateFromFetchDto($articleData, $source->id);

            $this->attachCategory($article, $providerName, $articleData->rawCategory);
            $this->syncAuthors($article, $articleData->authors);

            $count++;
        }

        Log::info('Articles fetched and stored successfully.', [
            'provider' => $providerName,
            'params' => $params,
            'count' => $count,
        ]);

        return $count;
    }

    protected function syncAuthors(Article $article, array $authors): void
    {
        $authorIds = [];
        foreach ($authors as $authorName) {
            if ($authorName !== '') {
                $authorIds[] = $this->authorRepository->firstOrCreateByName($authorName)->id;
            }
        }

        $article->authors()->sync($authorIds);
    }

    protected function attachCategory(Article $article, string $providerName, ?string $rawCategory): void
    {
        if (blank($rawCategory)) {
            return;
        }

        $rawCategory = Str::slug($rawCategory);

        $categoryMapping = $this->categoryMappingRepository->findCategoryMapping($providerName, $rawCategory);

        if (!$categoryMapping?->category_id) {
            return;
        }

        $this->articleRepository->attachCategory($article, $categoryMapping->category_id);
    }

    protected function getProviderService(string $providerName): NewsProviderContract
    {
        if (!isset($this->providers[$providerName])) {
            throw new RuntimeException("Unsupported provider: {$providerName}");
        }

        return $this->providers[$providerName];
    }
}
