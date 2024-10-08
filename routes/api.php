<?php

use App\Http\Controllers\Api\V1\AboutController;
use App\Http\Controllers\Api\V1\AdvertisementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\BoostController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::get('auth/verify-password-token/{token}', [AuthController::class, 'verifyResetPasswordToken']);
    Route::put('auth/reset-password', [AuthController::class, 'resetPassword']);

    // home
    Route::get('home', [HomeController::class, 'home']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    Route::delete('auth/logout', [AuthController::class, 'logout']);  

    /**
     * profile
     */
    Route::get('profile', [ProfileController::class, 'getProfile']);
    Route::put('profile', [ProfileController::class, 'updateProfile']);
    Route::put('profile/image-update', [ProfileController::class, 'updatePhoto']);

    /**
     * users
     */
    Route::get('agents', [UserController::class, 'agentIndex']);
    Route::get('agents/count', [UserController::class, 'countAgents']);
    Route::get('agents/{id}', [UserController::class, 'agentShow']);
    Route::put('agents/update-status/{id}', [UserController::class, 'updateStatus']);


    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    /**
     * posts
     */
    Route::patch('posts/decline/{post}', [PostController::class, 'decline']);
    Route::apiResource('posts', PostController::class);
    Route::get('agent-posts/{user}', [PostController::class, 'agentPosts']);
    Route::post('posts/{post}/add-photo', [PostController::class, 'addPhoto']);
    Route::delete('posts/{post}/remove-photo/{photo}', [PostController::class, 'removePhoto']);

    /**
     * boosts
     */
    Route::post('posts/{post}/add-boost', [BoostController::class, 'store']);
    Route::patch('posts/{post}/update-boost/{boost_id}', [BoostController::class, 'update']);
    Route::delete('posts/{post}/delete-boost/{boost_id}', [BoostController::class, 'destroy']);

    /**
     * blogs
     */
    Route::apiResource('blogs', BlogController::class);
    Route::apiResource('advertisements', AdvertisementController::class);

    /**
     * about
     */
    Route::apiResource('abouts', AboutController::class)->except(['store', 'update']);
    Route::post('abouts', [AboutController::class, 'upsert']);
});
