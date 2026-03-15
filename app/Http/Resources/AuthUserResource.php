<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AuthUser",
 *     title="Auth User",
 *     description="Authenticated user resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Jane Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
 *     @OA\Property(property="is_preferences_completed", type="boolean", example=true)
 * )
 */
class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_preferences_completed' => $this->preferences_completed_at !== null,
        ];
    }
}
