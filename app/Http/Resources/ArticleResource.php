<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
