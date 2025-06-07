<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function() {
    
    Route::prefix('auth')->group(function() {
        Route::post("register", [AuthController::class, 'register']);
        Route::post("login", [AuthController::class, 'login']);
        Route::post("logout", [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::prefix("posts")->middleware("auth:sanctum")->group(function() {
        Route::post('', [PostController::class, "create"]);
        Route::delete("{id}", [PostController::class, "delete"]);
        Route::get('', [PostController::class, 'get']);
    });

    Route::prefix('users')->middleware("auth:sanctum")->group(function() {
        Route::post('{username}/follow', [UserController::class, 'follow']);
        Route::delete('{username}/unfollow', [UserController::class, 'unfollow']);
        Route::get('{username}/following', [UserController::class, 'getFollowing']);
    });



});