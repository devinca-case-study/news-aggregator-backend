<?php

namespace App\Dto;

final class ArticleFetchDto
{
    public function __construct(
        public readonly string $provider,
        public readonly string $externalId,
        public readonly string $sourceName,
        public readonly string $url,
        public readonly ?string $imageUrl,
        public readonly string $title,
        public readonly ?string $content,
        public readonly array $authors,
        public readonly string $publishedAt,
        public readonly string $syncedAt,
        public readonly ?string $rawCategory,
        public readonly array $meta = []
    ) {}

    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'external_id' => $this->externalId,
            'url' => $this->url,
            'image_url' => $this->imageUrl,
            'title' => $this->title,
            'content' => $this->content,
            'published_at' => $this->publishedAt,
            'synced_at' => $this->syncedAt,
            'meta' => $this->meta
        ];
    }
}