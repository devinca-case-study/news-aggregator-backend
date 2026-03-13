<?php

namespace App\Dto;

final class ArticleFilterDto
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_BY = 'published_at';
    private const DEFAULT_SORT_DIRECTION = 'desc';

    public function __construct(
        public readonly ?string $search,
        public readonly ?string $dateFrom,
        public readonly ?string $dateTo,
        public readonly array $categoryIds,
        public readonly array $sourceIds,
        public readonly array $authorIds,
        public readonly int $page,
        public readonly int $perPage,
        public readonly string $sortBy,
        public readonly string $sortDirection,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            dateFrom: $data['date_from'] ?? null,
            dateTo: $data['date_to'] ?? null,
            categoryIds: $data['category_ids'] ?? [],
            sourceIds: $data['source_ids'] ?? [],
            authorIds: $data['author_ids'] ?? [],
            page: $data['page'] ?? self::DEFAULT_PAGE,
            perPage: $data['per_page'] ?? self::DEFAULT_PER_PAGE,
            sortBy: $data['sort_by'] ?? self::DEFAULT_SORT_BY,
            sortDirection: $data['author_ids'] ?? self::DEFAULT_SORT_DIRECTION,
        );
    }
}