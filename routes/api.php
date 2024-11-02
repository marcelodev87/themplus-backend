<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\FinancialMovementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::put('/data', [UserController::class, 'updateData']);
});

Route::prefix('category')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('movement')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FinancialMovementController::class, 'index']);
    Route::get('/informations', [FinancialMovementController::class, 'getFormInformations']);
    Route::post('/', [FinancialMovementController::class, 'store']);
    Route::put('/', [FinancialMovementController::class, 'update']);
    Route::delete('/{id}', [FinancialMovementController::class, 'destroy']);
});

Route::prefix('account')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'store']);
    Route::put('/', [AccountController::class, 'update']);
    Route::delete('/{id}', [AccountController::class, 'destroy']);
});

Route::prefix('enterprise')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EnterpriseController::class, 'show']);
    // TODO: Veriicar depois a criação da orgnização por rota controller
    // Route::post('/', [EnterpriseController::class, 'store']);
    Route::put('/', [EnterpriseController::class, 'update']);
    // TODO: Veriicar depois a logica de exclusão da orgnização
    // Route::delete('/{id}', [EnterpriseController::class, 'destroy']);
});
