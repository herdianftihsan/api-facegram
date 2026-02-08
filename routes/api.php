<?php

use App\Http\Controllers\AuthCOntroller;
use App\Http\Controllers\FollowingCOntroller;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

    Route::middleware('auth:sanctum')->prefix('/users')->group(function (){
        Route::post('{username}/follow',[FollowingCOntroller::class,'store']);
        Route::delete('{username}/unfollow',[FollowingCOntroller::class,'destroy']);
        Route::get('{username}/following',[FollowingCOntroller::class,'index']);
        Route::put('{username}/accept',[FollowingCOntroller::class,'update']);
        Route::get('{username}/followers',[FollowingCOntroller::class,'show']);
        Route::get('/',[UserController::class,'index']);
        Route::get('/{username}',[UserController::class,'show']);
        });


    Route::middleware('auth:sanctum')->apiResource('/posts',PostController::class);
});
