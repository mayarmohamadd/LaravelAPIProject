<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TagController;

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\StatsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-code', [AuthController::class, 'verifyCode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tags', TagController::class);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts/{id}', [PostController::class, 'show']);
    Route::put('posts/{id}', [PostController::class, 'update']);
    Route::delete('posts/{id}', [PostController::class, 'destroy']);
    Route::get('posts/trashed', [PostController::class, 'trashed']);
    Route::post('posts/{id}/restore', [PostController::class, 'restore']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/stats', [StatsController::class, 'index']);
});

