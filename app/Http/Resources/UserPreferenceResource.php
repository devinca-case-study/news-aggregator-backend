<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserPreference",
 *     title="User Preference",
 *     description="User preferences resource",
 *     @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category"), example={{"id":1,"name":"Technology"}}),
 *     @OA\Property(property="sources", type="array", @OA\Items(ref="#/components/schemas/Source"), example={{"id":1,"name":"Tech News"}}),
 *     @OA\Property(property="authors", type="array", @OA\Items(ref="#/components/schemas/Author"), example={{"id":1,"name":"John Smith"}}),
 *     @OA\Property(property="preferences_completed_at", type="string", format="date-time", nullable=true, example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="is_preferences_completed", type="boolean", example=true)
 * )
 */
class UserPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'categories' => CategoryResource::collection($this->whenLoaded('preferredCategories')),
            'sources' => SourceResource::collection($this->whenLoaded('preferredSources')),
            'authors' => AuthorResource::collection($this->whenLoaded('preferredAuthors')),
            'preferences_completed_at' => $this->preferences_completed_at,
            'is_preferences_completed' => $this->preferences_completed_at !== null
        ];
    }
}
