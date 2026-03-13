<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserPreferenceRepositoryContract
{
    public function loadPreferences(User $user): User;

    public function syncCategories(User $user, array $categoryIds): void;

    public function syncSources(User $user, array $sourceIds): void;

    public function syncAuthors(User $user, array $authorIds): void;

    public function markPreferencesCompleted(User $user): void;
}