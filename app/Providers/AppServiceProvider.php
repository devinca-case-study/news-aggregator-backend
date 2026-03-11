<?php

namespace App\Providers;

use App\Repositories\Contracts\ArticleRepositoryContract;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
use App\Repositories\Contracts\UnmappedCategoryRepositoryContract;
use App\Repositories\Eloquent\ArticleRepository;
use App\Repositories\Eloquent\CategoryMappingRepository;
use App\Repositories\Eloquent\UnmappedCategoryRepository;
use App\Services\NewsAggregatorService;
use App\Services\Providers\GuardianService;
use App\Services\Providers\NewsApiService;
use App\Services\Providers\NyTimesService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ArticleRepositoryContract::class, ArticleRepository::class);
        $this->app->bind(CategoryMappingRepositoryContract::class, CategoryMappingRepository::class);
        $this->app->bind(UnmappedCategoryRepositoryContract::class, UnmappedCategoryRepository::class);
        
        $this->app->when(NewsAggregatorService::class)
            ->needs('$providers')
            ->give(function ($app) {
                return [
                    'newsapi' => $app->make(NewsApiService::class),
                    'guardian' => $app->make(GuardianService::class),
                    'nytimes' => $app->make(NyTimesService::class),
                ];
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
