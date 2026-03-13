<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FilterController;
use Illuminate\Support\Facades\Route;

Route::prefix('articles')->group(function(){
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{article}', [ArticleController::class, 'show']);
});

Route::prefix('filters')->group(function(){
    Route::get('/categories', [FilterController::class, 'categories']);
    Route::get('/authors', [FilterController::class, 'authors']);
    Route::get('/sources', [FilterController::class, 'sources']);
});