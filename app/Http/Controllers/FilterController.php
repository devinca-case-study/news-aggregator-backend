<?php

namespace App\Http\Controllers;

use App\Http\Requests\OptionSearchRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SourceResource;
use App\Services\FilterService;

class FilterController extends Controller
{
    public function __construct(
        protected FilterService $filterService,
    ) {}

    public function categories()
    {
        return CategoryResource::collection($this->filterService->getCategories());
    }

    // Returns author suggestions for article filtering.
    // Designed for searchable select inputs to avoid loading all authors since the author dataset can be large.
    public function authors(OptionSearchRequest $request)
    {
        return AuthorResource::collection(
            $this->filterService->searchAuthorsForSelect(
                search: $request->validated('search'),
                limit: $request->validated('limit', 10),
            )
        );
    }

    // Returns source options for article filtering.
    // Supports optional search and limit parameters.
    // If limit is not provided, all sources will be returned.
    public function sources(OptionSearchRequest $request)
    {
        return SourceResource::collection(
            $this->filterService->searchSourcesForSelect(
                search: $request->validated('search'),
                limit: $request->validated('limit'),
            )
        );
    }
}