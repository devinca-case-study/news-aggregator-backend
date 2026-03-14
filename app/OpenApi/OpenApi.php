<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="API documentation for the News Aggregator application",
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 *     description="Enter token in format: Bearer {token}"
 * )
 * @OA\Components(
 *     schemas={
 *         @OA\Schema(
 *             schema="Article",
 *             type="object",
 *             required={"id", "provider", "external_id", "url", "title", "content", "published_at", "synced_at"},
 *             @OA\Property(property="id", type="integer", example=1),      
 *             @OA\Property(property="provider", type="string", example="newsapi"),
 *             @OA\Property(property="external_id", type="string", example="article-123"),
 *             @OA\Property(property="url", type="string", format="uri", example="https://example.com/article"),
 *             @OA\Property(property="title", type="string", example="Article Title"),
 *             @OA\Property(property="content", type="string", example="Article content..."),
 *             @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *             @OA\Property(property="synced_at", type="string", format="date-time", example="2024-01-01T01:00:00Z"),
 *             @OA\Property(property="meta", type="object", example={}),    
 *             @OA\Property(property="source", ref="#/components/schemas/Source"),
 *             @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category")),
 *             @OA\Property(property="authors", type="array", @OA\Items(ref="#/components/schemas/Author"))
 *         ),
 *         @OA\Schema(
 *             schema="AuthUser",
 *             type="object",
 *             required={"id", "name", "email", "is_preferences_completed"},   
 *             @OA\Property(property="id", type="integer", example=1),      
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="is_preferences_completed", type="boolean", example=true)
 *         ),
 *         @OA\Schema(
 *             schema="Source",
 *             type="object",
 *             required={"id", "name"},
 *             @OA\Property(property="id", type="integer", example=1),      
 *             @OA\Property(property="name", type="string", example="Tech News")
 *         ),
 *         @OA\Schema(
 *             schema="Category",
 *             type="object",
 *             required={"id", "name"},
 *             @OA\Property(property="id", type="integer", example=1),      
 *             @OA\Property(property="name", type="string", example="Technology")
 *         ),
 *         @OA\Schema(
 *             schema="Author",
 *             type="object",
 *             required={"id", "name"},
 *             @OA\Property(property="id", type="integer", example=1),      
 *             @OA\Property(property="name", type="string", example="John Smith")
 *         )
 *     }
 * )
 */
final class OpenApi
{
}
