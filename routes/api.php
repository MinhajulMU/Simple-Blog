<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Helpers\Route as RecursiveRoute;

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

Route::get('/', [ApiController::class, 'index'])->name('api/index');
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('auth/me', 'me')->name('auth.me');
        Route::post('logout', 'logout')->name('auth.logout');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('auth.register');
    Route::post('login', 'login')->name('auth.login');
});

RecursiveRoute::loadFromDir(__DIR__ . '/api');
