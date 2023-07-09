<?php

use App\Http\Controllers\API\ArticleCommentController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\UserController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// default
Route::get('tags', TagController::class . '@index');

// Auth
Route::post('users', UserController::class . '@store');
Route::post('users/login', UserController::class . '@login');
Route::get('profiles/{user}', UserController::class . '@show')->middleware('accessible');

Route::get('articles', ArticleController::class . '@index')->middleware('accessible');
Route::get('articles/{article}', ArticleController::class . '@show')->middleware('accessible');;

// get comments
Route::get('articles/{slug}/comments', ArticleCommentController::class . '@index')->middleware('accessible');;

// protected router
Route::middleware('auth:sanctum')->group(function () {
    // auth
    Route::get('user', UserController::class . '@index');
    Route::put('user', UserController::class . '@update');
    Route::post('profiles/{user}/follow', UserController::class . '@follow');
    Route::delete('profiles/{user}/follow', UserController::class . '@unfollow');
    // articles
    Route::post('articles', ArticleController::class . '@store');
    Route::put('articles/{slug}', ArticleController::class . '@update');
    Route::delete('articles/{slug}', ArticleController::class . '@destroy');

    // favorite
    Route::post('articles/{slug}/favorite', ArticleController::class . '@favorite');
    Route::delete('articles/{slug}/favorite', ArticleController::class . '@unfavorite');

    // comments
    Route::post('articles/{slug}/comments', ArticleCommentController::class . '@store');
    Route::delete('articles/{slug}/comments/{comment_id}', ArticleCommentController::class . '@destroy');
});


