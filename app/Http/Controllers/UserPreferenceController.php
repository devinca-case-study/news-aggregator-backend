<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceUpdateRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="User Preferences",
 *     description="Endpoints for managing user preferences (categories, sources, and authors). These preferences influence article ranking by increasing the weight of preferred content"
 * )
 */
class UserPreferenceController extends Controller
{
    public function __construct(
        protected UserPreferenceService $userPreferenceService,
    ) {}

    /**
     * @OA\Get(
     *     path="/me/preferences",
     *     summary="Get user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="categories", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Technology")
     *             )),
     *             @OA\Property(property="sources", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Tech News")
     *             )),
     *             @OA\Property(property="authors", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Smith")
     *             )),
     *             @OA\Property(property="preferences_completed_at", type="string", format="date-time", nullable=true, example="2024-01-01T00:00:00Z"),
     *             @OA\Property(property="is_preferences_completed", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */    
    public function show(Request $request): UserPreferenceResource
    {
        $user = $this->userPreferenceService->getUserPreferences($request->user());
        return UserPreferenceResource::make($user);
    }

    /**
     * @OA\Put(
     *     path="/me/preferences",
     *     summary="Update user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="category_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             ),
     *             @OA\Property(
     *                 property="source_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2}
     *             ),
     *             @OA\Property(
     *                 property="author_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3, 4}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="categories", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Technology")
     *             )),
     *             @OA\Property(property="sources", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Tech News")
     *             )),
     *             @OA\Property(property="authors", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Smith")
     *             )),
     *             @OA\Property(property="preferences_completed_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
     *             @OA\Property(property="is_preferences_completed", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */    
    public function update(UserPreferenceUpdateRequest $request): UserPreferenceResource
    {
        $user = $this->userPreferenceService->saveUserPreferences(
            $request->user(),
            $request->validated()
        );

        return UserPreferenceResource::make($user);
    }
}
