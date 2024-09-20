<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::group([
    'prefix' => 'users',
    'as' => 'users.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', UsersController::class
    . '@index')->name('index');
    Route::get('/{id}', UsersController::class
        . '@show')->name('show');
});
