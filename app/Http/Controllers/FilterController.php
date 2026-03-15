<?php

namespace App\Http\Controllers;

use App\Http\Requests\OptionSearchRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SourceResource;
use App\Services\FilterService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Filters",
 *     description="Filter options for articles"
 * )
 */
class FilterController extends Controller
{
    public function __construct(
        protected FilterService $filterService,
    ) {}

    /**
     * @OA\Get(
     *     path="/filters/categories",
     *     summary="Get all categories",
     *     description="Returns all available categories for article filtering. Categories are returned in full because the dataset is small and rarely changes, allowing clients to load them once and reuse them for filter dropdowns without additional requests.",
     *     tags={"Filters"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Technology")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object", example={})
     *         )
     *     )
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = $this->filterService->getCategories();
        return ApiResponse::success(CategoryResource::collection($categories));
    }

    /**
     * @OA\Get(
     *     path="/filters/authors",
     *     summary="Search authors",
     *     description="
     Returns author suggestions for article filtering.
 
     Results are only returned when a search query is provided.
 
     If limit is not specified, a default of 10 results will be returned.
     ",
     *     tags={"Filters"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for author names",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit number of results (default: 10)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Smith")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object", example={})
     *         )
     *     )
     * )
     */
    public function authors(OptionSearchRequest $request): JsonResponse
    {
        $authors = $this->filterService->searchAuthorsForSelect(
            search: $request->validated('search'),
            limit: $request->validated('limit', 10),
        );

        return ApiResponse::success(AuthorResource::collection($authors));
    }

    /**
     * @OA\Get(
     *     path="/filters/sources",
     *     summary="Search sources",
     *     description="Returns source options for article filtering. Since the number of sources is relatively moderate, all sources are returned by default to simplify frontend filtering. Optional search and limit parameters can be used to narrow or limit the results.",
     *     tags={"Filters"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for source names",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit number of results",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Tech News")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object", example={})
     *         )
     *     )
     * )
     */
    public function sources(OptionSearchRequest $request): JsonResponse
    {
        $sources = $this->filterService->searchSourcesForSelect(
            search: $request->validated('search'),
            limit: $request->validated('limit'),
        );

        return ApiResponse::success(SourceResource::collection($sources));
    }
}