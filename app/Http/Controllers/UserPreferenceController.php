<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceUpdateRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function __construct(
        protected UserPreferenceService $userPreferenceService,
    ) {}

    public function show(Request $request): UserPreferenceResource
    {
        $user = $this->userPreferenceService->getUserPreferences($request->user());
        return UserPreferenceResource::make($user);
    }

    public function update(UserPreferenceUpdateRequest $request): UserPreferenceResource
    {
        $user = $this->userPreferenceService->saveUserPreferences(
            $request->user(),
            $request->validated()
        );

        return UserPreferenceResource::make($user);
    }
}
