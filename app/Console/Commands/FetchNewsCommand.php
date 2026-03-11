<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provider = (string) $this->argument('provider');

        return match ($provider) {
            'newsapi' => $this->handleNewsApi(),
            'guardian' => $this->dispatchProvider('guardian'),
            'nytimes' => $this->dispatchProvider('nytimes'),
            default => $this->unsupportedProvider($provider),
        };
    }

    protected function handleNewsApi(): int
    {
        $categories = config('news.providers.newsapi.categories', []);

        if (empty($categories)) {
            $this->error('No NewsAPI categories configured.');

            return self::FAILURE;
        }

        $category = $this->resolveNewsApiRotationCategory($categories);

        Log::info('NewsAPI rotation category resolved.', [
            'category' => $category,
        ]);

        return $this->dispatchProvider('newsapi', [
            'category' => $category,
        ]);
    }

    protected function resolveNewsApiRotationCategory(array $categories): string
    {
        $minutesFromStartOfDay = now()->startOfDay()->diffInMinutes(now());
        $slotIndex = (int)($minutesFromStartOfDay / 15);
        $categoryIndex = $slotIndex % count($categories);

        return $categories[$categoryIndex];
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
