<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserPreferenceRepositoryContract;

class UserPreferenceRepository implements UserPreferenceRepositoryContract
{
    public function loadPreferences(User $user): User
    {
        return $user->loadMissing(User::USER_PREFERRED_RELATIONS);
    }

    public function syncCategories(User $user, array $categoryIds): void
    {
        $user->preferredCategories()->sync($categoryIds);
    }

    public function syncSources(User $user, array $sourceIds): void
    {
        $user->preferredSources()->sync($sourceIds);
    }

    public function syncAuthors(User $user, array $authorIds): void
    {
        $user->preferredAuthors()->sync($authorIds);
    }

    public function markPreferencesCompleted(User $user): void
    {
        if ($user->preferences_completed_at == null) {
            $user->fill([
                'preferences_completed_at' => now(),
            ])->save();
        }

    }
}