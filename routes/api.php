<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::put('/data', [UserController::class, 'updateData']);
});

Route::prefix('member')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::post('/', [MemberController::class, 'store']);
    Route::post('/export', [MemberController::class, 'export']);
    Route::put('/', [MemberController::class, 'update']);
    Route::delete('/{id}', [MemberController::class, 'destroy']);
});

Route::prefix('category')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('department')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::post('/', [DepartmentController::class, 'store']);
    Route::put('/', [DepartmentController::class, 'update']);
    Route::delete('/{id}', [DepartmentController::class, 'destroy']);
});

Route::prefix('alert')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AlertController::class, 'index']);
    Route::post('/', [AlertController::class, 'store']);
    Route::put('/', [AlertController::class, 'update']);
    Route::delete('/{id}', [AlertController::class, 'destroy']);
});

Route::prefix('movement')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [MovementController::class, 'index']);
    Route::get('/filter', [MovementController::class, 'filterMovements']);
    Route::get('/informations/{type}', [MovementController::class, 'getFormInformations']);
    Route::post('/export', [MovementController::class, 'export']);
    Route::post('/', [MovementController::class, 'store']);
    Route::put('/', [MovementController::class, 'update']);
    Route::delete('/{id}', [MovementController::class, 'destroy']);
});

Route::prefix('scheduling')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SchedulingController::class, 'index']);
    Route::get('/filter', [SchedulingController::class, 'filterSchedulings']);
    Route::get('/informations/{type}', [SchedulingController::class, 'getFormInformations']);
    Route::post('/', [SchedulingController::class, 'store']);
    Route::post('/export', [SchedulingController::class, 'export']);
    Route::put('/', [SchedulingController::class, 'update']);
    Route::put('/finalize/{id}', [SchedulingController::class, 'finalize']);
    Route::delete('/{id}', [SchedulingController::class, 'destroy']);
});

Route::prefix('account')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'store']);
    Route::post('/export', [AccountController::class, 'export']);
    Route::post('/transfer', [AccountController::class, 'createTransfer']);
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
