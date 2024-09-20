<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentsController;

Route::group([
    'prefix' => 'comments',
    'as' => 'comments.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/view-by-post/{post_id}', CommentsController::class
        . '@viewByPost')->name('viewByPost');
    Route::get('/{id}', CommentsController::class
        . '@show')->name('show');
    Route::post('/', CommentsController::class
        . '@store')->name('store');
    Route::patch('/{id}', CommentsController::class
        . '@update')->name('update');
    Route::delete('/{id}', CommentsController::class
        . '@delete')->name('delete');
});
