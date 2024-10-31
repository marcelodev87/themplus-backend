<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::prefix('category')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('account')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EnterpriseController::class, 'index']);
    Route::post('/', [EnterpriseController::class, 'store']);
    Route::put('/', [EnterpriseController::class, 'update']);
    Route::delete('/{id}', [EnterpriseController::class, 'destroy']);
});

Route::prefix('enterprise')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EnterpriseController::class, 'show']);
    // TODO: Veriicar depois a criação da orgnização por rota controller
    // Route::post('/', [EnterpriseController::class, 'store']);
    Route::put('/', [EnterpriseController::class, 'update']);
    // TODO: Veriicar depois a logica de exclusão da orgnização
    // Route::delete('/{id}', [EnterpriseController::class, 'destroy']);
});
