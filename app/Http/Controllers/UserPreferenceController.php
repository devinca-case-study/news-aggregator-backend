<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceUpdateRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreferenceService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
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
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserPreference"),
     *             @OA\Property(property="meta", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */    
    public function show(Request $request): JsonResponse
    {
        $user = $this->userPreferenceService->getUserPreferences($request->user());
        return ApiResponse::success(UserPreferenceResource::make($user));
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
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserPreference"),
     *             @OA\Property(property="meta", type="object", example={})
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
    public function update(UserPreferenceUpdateRequest $request): JsonResponse
    {
        $user = $this->userPreferenceService->saveUserPreferences(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success(UserPreferenceResource::make($user));
    }
}
