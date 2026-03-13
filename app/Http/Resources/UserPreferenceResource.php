<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
