<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
use App\Repositories\Contracts\CategoryMappingRepositoryContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {provider : Provider name (newsapi|guardian|nytimes)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch news fetching jobs for a specific provider';

    public function __construct(
        protected CategoryMappingRepositoryContract $categoryMappingRepository
    ) {
        return parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provider = (string) $this->argument('provider');

        if (!in_array($provider, ['newsapi', 'guardian', 'nytimes'], true)) {
            return $this->unsupportedProvider($provider);
        }

        $category = $this->resolveRotationCategory($provider);

        if (!$category) {
            $this->error("No mapped categories configured for provider [{$provider}].");

            return self::FAILURE;
        }
        
        Log::info("{$provider} rotation category resolved.", [
            'category' => $category,
        ]);

        return $this->dispatchProvider($provider, [
            'category' => $category,
        ]);
    }

    protected function resolveRotationCategory(string $provider): ?string
    {
        $categories = $this->categoryMappingRepository->getMappedCategoryCodesByProvider($provider);

        if (empty($categories)) {
            return null;
        }

        $rotationMinutes = $this->getRotationMinutes($provider);

        return $this->resolveRotationValue($categories, $rotationMinutes);
    }

    protected function resolveRotationValue(array $categories, int $rotationMinutes): string
    {
        $minutesFromStartOfDay = now()->startOfDay()->diffInMinutes(now());
        $slotIndex = (int)($minutesFromStartOfDay / $rotationMinutes);
        $categoryIndex = $slotIndex % count($categories);

        return $categories[$categoryIndex];
    }

    protected function getRotationMinutes(string $provider): int
    {
        return (int) config("news.providers.{$provider}.rotation_minutes");
    }

    protected function dispatchProvider(string $provider, array $params = []): int
    {
        FetchArticlesJob::dispatch($provider, $params);
        
        return self::SUCCESS;
    }

    protected function unsupportedProvider(string $provider): int
    {
        $this->error("Unsupported provider: {$provider}");

        return self::FAILURE;
    }
}
