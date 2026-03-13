<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\UserPreferenceController;
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

Route::prefix('auth')->group(function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('auth')->group(function(){
        Route::post('/logout', [AuthController::class, 'logout']);
    });
        
    Route::prefix('me')->group(function(){
        Route::get('/', [AuthController::class, 'me']);

        Route::prefix('preferences')->group(function(){
            Route::get('/', [UserPreferenceController::class, 'show']);
            Route::put('/', [UserPreferenceController::class, 'update']);
        });
    });
});