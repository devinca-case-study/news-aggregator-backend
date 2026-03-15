<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Article",
 *     title="Article",
 *     description="Article resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="provider", type="string", example="newsapi"),
 *     @OA\Property(property="external_id", type="string", example="12345"),
 *     @OA\Property(property="url", type="string", example="https://example.com/article"),
 *     @OA\Property(property="image_url", type="string", example="https://picsum.photos/200"),
 *     @OA\Property(property="title", type="string", example="Breaking News: Tech Advances"),
 *     @OA\Property(property="content", type="string", example="Article content here..."),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="synced_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="meta", type="object", example={}),
 *     @OA\Property(property="source", ref="#/components/schemas/Source", nullable=true),
 *     @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category"), example={}),
 *     @OA\Property(property="authors", type="array", @OA\Items(ref="#/components/schemas/Author"), example={})
 * )
 */
class ArticleResource extends JsonResource
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
            'provider' => $this->provider,
            'external_id' => $this->external_id,
            'url' => $this->url,
            'image_url' => $this->image_url,
            'title' => $this->title,
            'content' => $this->content,
            'published_at' => $this->published_at,
            'synced_at' => $this->synced_at,
            'meta' => $this->meta,
            'source' => SourceResource::make($this->whenLoaded('source')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'authors' => AuthorResource::collection($this->whenLoaded('authors'))
        ];
    }
}
