<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/reset', [UserController::class, 'reset']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('/newPassword', [UserController::class, 'resetPassword']);

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::put('/data', [UserController::class, 'updateData']);
});

Route::prefix('member')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::post('/', [MemberController::class, 'store'])->middleware('admin');
    Route::post('/start-office', [MemberController::class, 'startOfficeNewUser'])->middleware('admin');
    Route::post('/export', [MemberController::class, 'export']);
    Route::put('/', [MemberController::class, 'update'])->middleware('admin');
    Route::delete('/{id}', [MemberController::class, 'destroy'])->middleware('admin');
});

Route::prefix('category')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/filter', [CategoryController::class, 'filterCategories']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/active/{id}', [CategoryController::class, 'active']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('department')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::post('/', [DepartmentController::class, 'store'])->middleware('admin');
    Route::put('/', [DepartmentController::class, 'update'])->middleware('admin');
    Route::delete('/{id}', [DepartmentController::class, 'destroy'])->middleware('admin');
});

Route::prefix('alert')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AlertController::class, 'index']);
    Route::post('/', [AlertController::class, 'store']);
    Route::put('/', [AlertController::class, 'update']);
    Route::delete('/{id}', [AlertController::class, 'destroy']);
});

Route::prefix('movement')->middleware('auth:sanctum')->group(function () {
    Route::get('/{date}', [MovementController::class, 'index']);
    Route::get('/filter/{date}', [MovementController::class, 'filterMovements']);
    Route::get('/informations/{type}', [MovementController::class, 'getFormInformations']);
    Route::get('/download/{file}', [MovementController::class, 'downloadFile']);
    Route::post('/insert-example', [MovementController::class, 'insertExample']);
    Route::post('/insert', [MovementController::class, 'insert']);
    Route::post('/export/{date}', [MovementController::class, 'export']);
    Route::post('/', [MovementController::class, 'store']);
    Route::put('/', [MovementController::class, 'update']);
    Route::delete('/{id}', [MovementController::class, 'destroy']);
});

Route::prefix('scheduling')->middleware('auth:sanctum')->group(function () {
    Route::get('/{date}', [SchedulingController::class, 'index']);
    Route::get('/filter/{date}', [SchedulingController::class, 'filterSchedulings']);
    Route::get('/informations/{type}', [SchedulingController::class, 'getFormInformations']);
    Route::post('/', [SchedulingController::class, 'store']);
    Route::post('/export/{date}', [SchedulingController::class, 'export']);
    Route::put('/', [SchedulingController::class, 'update']);
    Route::put('/finalize/{id}', [SchedulingController::class, 'finalize']);
    Route::delete('/{id}', [SchedulingController::class, 'destroy']);
});

Route::prefix('account')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'store'])->middleware('admin');
    Route::post('/export', [AccountController::class, 'export']);
    Route::post('/transfer', [AccountController::class, 'createTransfer']);
    Route::put('/', [AccountController::class, 'update'])->middleware('admin');
    Route::put('/active/{id}', [AccountController::class, 'active']);
    Route::delete('/{id}', [AccountController::class, 'destroy'])->middleware('admin');
});

Route::prefix('order')->middleware('auth:sanctum')->group(function () {
    Route::get('/client', [OrderController::class, 'indexViewClient']);
    Route::get('/counter', [OrderController::class, 'indexViewCounter']);
    Route::get('/bonds', [OrderController::class, 'indexBonds']);
    Route::post('/sendRequest', [OrderController::class, 'store']);
    Route::post('/responseClient', [OrderController::class, 'actionClient']);
    Route::put('/', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

Route::prefix('financial')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FinancialController::class, 'index']);
    Route::post('/', [FinancialController::class, 'finalize'])->middleware('admin');
});

Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
    Route::get('/{date}', [DashboardController::class, 'index']);
});

Route::prefix('feed')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FeedController::class, 'index']);
});

Route::prefix('register')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RegisterController::class, 'index'])->middleware('admin');
    Route::get('/{id}', [RegisterController::class, 'show'])->middleware('admin');
});

Route::prefix('feedback')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [FeedbackController::class, 'store']);
});

Route::prefix('enterprise')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EnterpriseController::class, 'show']);
    Route::get('/search/{text}', [EnterpriseController::class, 'search']);
    Route::get('/show/{id}', [EnterpriseController::class, 'filter']);
    // TODO: Veriicar depois a criação da orgnização por rota controller
    // Route::post('/', [EnterpriseController::class, 'store']);
    Route::put('/', [EnterpriseController::class, 'update'])->middleware('admin');
    Route::put('/unlink', [EnterpriseController::class, 'unlink'])->middleware('admin');
    // TODO: Veriicar depois a logica de exclusão da orgnização
    // Route::delete('/{id}', [EnterpriseController::class, 'destroy']);
});
Route::prefix('office')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EnterpriseController::class, 'indexOffices']);
    Route::post('/', [EnterpriseController::class, 'storeOffice'])->middleware('admin');
});
