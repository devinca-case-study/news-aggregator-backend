<?php

namespace App\Jobs;

use App\Services\NewsAggregatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchArticlesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $provider,
        public readonly array $params = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NewsAggregatorService $newsAggregatorService): void
    {
        $count = $newsAggregatorService->fetchAndStore(
            $this->provider,
            $this->params
        );

        Log::info('FetchArticlesJob completed.', [
            'provider' => $this->provider,
            'params' => $this->params,
            'count' => $count,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('FetchArticlesJob failed.', [
            'provider' => $this->provider,
            'params' => $this->params,
            'message' => $exception->getMessage(),
        ]);
    }
}
