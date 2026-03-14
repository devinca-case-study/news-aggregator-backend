<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserPreferenceRepositoryContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserPreferenceService
{
    public function __construct(
        protected UserPreferenceRepositoryContract $userPreferenceRepository,
    ) {}

    public function getUserPreferences(User $user): User
    {
        return $this->userPreferenceRepository->loadPreferences($user);
    }

    public function getCachedPreferenceIds(User $user): array
    {
        $cacheKey = $this->getCacheKey($user);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($user) {
            $user = $this->userPreferenceRepository->loadPreferences($user);

            return [
                'source_ids' => $user->preferredSources->pluck('id')->all(),
                'category_ids' => $user->preferredCategories->pluck('id')->all(),
                'author_ids' => $user->preferredAuthors->pluck('id')->all(),
            ];
        });
    }

    public function saveUserPreferences(User $user, array $data): User
    {
        DB::transaction(function () use ($user, $data) {
            $this->userPreferenceRepository->syncCategories($user, $data['category_ids'] ?? []);
            $this->userPreferenceRepository->syncSources($user, $data['source_ids'] ?? []);
            $this->userPreferenceRepository->syncAuthors($user, $data['author_ids'] ?? []);
            $this->userPreferenceRepository->markPreferencesCompleted($user);
        });

        $this->clearUserPreferenceCache($user);

        return $this->getUserPreferences($user->fresh());
    }

    protected function clearUserPreferenceCache(User $user): void
    {
        $cacheKey = $this->getCacheKey($user);
        Cache::forget($cacheKey);
    }

    protected function getCacheKey(User $user): string
    {
        return "user_preferences:{$user->id}";
    }
}