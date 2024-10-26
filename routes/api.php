<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::prefix('category')->group(function () {
    Route::get('/', 'CategoryController@index');
    Route::get('/{id}', 'CategoryController@show');
    Route::post('/', 'CategoryController@store');
    Route::put('/{id}', 'CategoryController@update');
    Route::delete('/{id}', 'CategoryController@destroy');
});
