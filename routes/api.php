<?php

use App\Http\Controllers\AuthCOntroller;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthCOntroller::class, 'Register']);
        Route::post('/login', [AuthCOntroller::class, 'login']);
        Route::post('/logout', [AuthCOntroller::class, 'logout']);
    });

    Route::middleware('auth:sanctum')->apiResource('/posts',PostController::class);
});
