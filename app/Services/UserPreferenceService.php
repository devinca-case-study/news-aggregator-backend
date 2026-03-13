<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserPreferenceRepositoryContract;
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

    public function saveUserPreferences(User $user, array $data): User
    {
        DB::transaction(function () use ($user, $data) {
            $this->userPreferenceRepository->syncCategories($user, $data['category_ids'] ?? []);
            $this->userPreferenceRepository->syncSources($user, $data['source_ids'] ?? []);
            $this->userPreferenceRepository->syncAuthors($user, $data['author_ids'] ?? []);
            $this->userPreferenceRepository->markPreferencesCompleted($user);
        });

        return $this->getUserPreferences($user->fresh());
    }
}