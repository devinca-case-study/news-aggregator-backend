<?php

namespace App\Providers;

use App\Repositories\Contracts\ArticleRepositoryContract;
use App\Repositories\Contracts\AuthorRepositoryContract;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
use App\Repositories\Contracts\CategoryRepositoryContract;
use App\Repositories\Contracts\SourceRepositoryContract;
use App\Repositories\Eloquent\ArticleRepository;
use App\Repositories\Eloquent\AuthorRepository;
use App\Repositories\Eloquent\CategoryMappingRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\SourceRepository;
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
        $this->app->bind(AuthorRepositoryContract::class, AuthorRepository::class);
        $this->app->bind(SourceRepositoryContract::class, SourceRepository::class);
        $this->app->bind(CategoryRepositoryContract::class, CategoryRepository::class);
        $this->app->bind(CategoryMappingRepositoryContract::class, CategoryMappingRepository::class);
        
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
