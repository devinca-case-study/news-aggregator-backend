<?php

namespace App\Http\Controllers;

use App\Dto\ArticleFilterDto;
use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService
    ) {}

    public function index(ArticleIndexRequest $request)
    {
        $dto = ArticleFilterDto::fromArray($request->validated());
        $articles = $this->articleService->getPaginatedArticles($dto);
        return ArticleResource::collection($articles);
    }

    public function show(Article $article): ArticleResource
    {
        $article = $this->articleService->getDetailArticle($article);
        return ArticleResource::make($article);
    }
}
