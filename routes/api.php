<?php

use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\UserController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// User

Route::post('users', UserController::class . '@store');
Route::post('users/login', UserController::class . '@login');


// auth
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', UserController::class . '@index');
    Route::put('user', UserController::class . '@update');
    Route::post('profiles/{user}/follow', UserController::class . '@follow');
    Route::delete('profiles/{user}/follow', UserController::class . '@unfollow');
    Route::get('profiles/{user}', UserController::class . '@show');
});


Route::delete('users/{user}', UserController::class . '@destroy');