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
 */
final class OpenApi
{
}
