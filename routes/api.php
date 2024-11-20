<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\BannerController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('update-avatar', [AuthController::class, 'updateAvatar'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('anime')->group(function () {
    Route::get('/', [AnimeController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/detail/{id}', [AnimeController::class, 'show']);
    Route::get('/ongoing', [AnimeController::class, 'getOngoingAnime'])->middleware('auth:sanctum');
    Route::get('/popular', [AnimeController::class, 'getPopularAnime'])->middleware('auth:sanctum');
    Route::get('/watchlist', [AnimeController::class, 'getFavoriteAnime'])->middleware('auth:sanctum');
    Route::post('/add-watchlist/{id}', [AnimeController::class, 'addToFavorite'])->middleware('auth:sanctum');
    Route::post('/', [AnimeController::class, 'store'])->middleware('auth:sanctum', 'admin');
    Route::post('/update/{id}', [AnimeController::class, 'update'])->middleware('auth:sanctum', 'admin');
    Route::delete('/{id}', [AnimeController::class, 'destroy'])->middleware('auth:sanctum', 'admin');
});

Route::prefix('banner')->group(function () {
    Route::get('/', [BannerController::class, 'index']);
    Route::get('/detail/{id}', [BannerController::class, 'show']);
    Route::post('/', [BannerController::class, 'store'])->middleware('auth:sanctum', 'admin');
    Route::post('/update/{id}', [BannerController::class, 'update'])->middleware('auth:sanctum', 'admin');
    Route::delete('/{id}', [BannerController::class, 'destroy'])->middleware('auth:sanctum', 'admin');
});
