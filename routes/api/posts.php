<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;

Route::group([
    'prefix' => 'posts',
    'as' => 'posts.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', PostsController::class
        . '@index')->name('index');
    Route::get('/{id}', PostsController::class
        . '@show')->name('show');
    Route::post('/', PostsController::class
        . '@store')->name('store');
    Route::patch('/{id}', PostsController::class
        . '@update')->name('update');
    Route::delete('/{id}', PostsController::class
        . '@delete')->name('delete');
});
