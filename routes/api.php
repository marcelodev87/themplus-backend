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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\SettingsCounterController;
use App\Http\Controllers\MovementAnalyzeController;
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

Route::prefix('external')->group(function () {
    Route::post('/check-phone', [MovementAnalyzeController::class, 'checkPhone']);
    Route::prefix('movement-analyze')->group(function () {
        Route::post('/', [MovementAnalyzeController::class, 'store']);
    });
});

Route::prefix('movement-analyze')->group(function () {
    Route::get('/', [MovementAnalyzeController::class, 'index'])->middleware('auth:sanctum');
    Route::post('/finalize', [MovementAnalyzeController::class, 'finalize'])->middleware('auth:sanctum');
    Route::put('/', [MovementAnalyzeController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [MovementAnalyzeController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('member')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::get('/inbox', [MemberController::class, 'inbox']);
    Route::get('/{id}', [MemberController::class, 'indexByEnterprise']);
    Route::get('/find/{id}', [MemberController::class, 'show']);
    Route::post('/', [MemberController::class, 'store'])->middleware('admin');
    Route::post('/member-counter', [MemberController::class, 'storeByCounter']);
    Route::post('/export', [MemberController::class, 'export']);
    Route::post('/start-office', [MemberController::class, 'startOfficeNewUser'])->middleware('admin');
    Route::put('/member-counter', [MemberController::class, 'updateByCounter']);
    Route::put('/inbox', [MemberController::class, 'readNotification']);
    Route::put('/inbox-all', [MemberController::class, 'readAllNotification']);
    Route::put('/', [MemberController::class, 'update'])->middleware('admin');
    Route::put('/active', [MemberController::class, 'active'])->middleware('admin');
    Route::delete('/{id}', [MemberController::class, 'destroy'])->middleware('admin');
    Route::delete('/{id}/counter', [MemberController::class, 'destroyByCounter']);
    Route::delete('/inbox/{id}', [MemberController::class, 'destroyNotification']);
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
    Route::get('/{id}', [AlertController::class, 'index']);
    Route::post('/', [AlertController::class, 'store']);
    Route::put('/', [AlertController::class, 'update']);
    Route::delete('/{id}', [AlertController::class, 'destroy']);
});

Route::prefix('movement')->middleware('auth:sanctum')->group(function () {
    Route::get('/{date}', [MovementController::class, 'index']);
    Route::get('/filter/{date}', [MovementController::class, 'filterMovements']);
    Route::get('/informations/{type}', [MovementController::class, 'getFormInformations']);
    Route::get('/download/{file}', [MovementController::class, 'downloadFile']);
    Route::post('/observations', [MovementController::class, 'saveObservations']);
    Route::post('/insert-example', [MovementController::class, 'insertExample']);
    Route::post('/insert', [MovementController::class, 'insert']);
    Route::post('/export/excel/{date}', [MovementController::class, 'exportExcel']);
    Route::post('/export/pdf/{date}', [MovementController::class, 'exportPDF']);
    Route::post('/', [MovementController::class, 'store']);
    Route::post('/update', [MovementController::class, 'update']);
    Route::delete('/{id}', [MovementController::class, 'destroy']);
});

Route::prefix('scheduling')->middleware('auth:sanctum')->group(function () {
    Route::get('/{date}', [SchedulingController::class, 'index']);
    Route::get('/filter/{date}', [SchedulingController::class, 'filterSchedulings']);
    Route::get('/informations/{type}', [SchedulingController::class, 'getFormInformations']);
    Route::post('/', [SchedulingController::class, 'store']);
    Route::post('/export/excel/{date}', [SchedulingController::class, 'exportExcel']);
    Route::post('/export/pdf/{date}', [SchedulingController::class, 'exportPDF']);
    Route::post('/update', [SchedulingController::class, 'update']);
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
    Route::delete('/bond/{id}', [OrderController::class, 'destroyBond']);
});

Route::prefix('financial')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FinancialController::class, 'index']);
    Route::get('/movements-observations/{date}', [FinancialController::class, 'indexObservations']);
    Route::post('/', [FinancialController::class, 'finalize'])->middleware('admin');
    Route::get('/settings-counter', [SettingsCounterController::class, 'index'])->middleware('admin');
    Route::put('/settings-counter', [SettingsCounterController::class, 'update'])->middleware('admin');
});

Route::prefix('report')->middleware('auth:sanctum')->group(function () {
    Route::get('/{id}', [ReportController::class, 'index']);
    Route::get('/details/{id}', [ReportController::class, 'details']);
    Route::post('/undo/{id}', [ReportController::class, 'undo']);
    Route::post('/finalize/{id}', [ReportController::class, 'finalize']);
    Route::post('/movement/update/', [ReportController::class, 'updateMovementByCounter']);
    Route::delete('/movement/{id}', [ReportController::class, 'destroyMovement']);
    Route::delete('/reopen/{id}', [ReportController::class, 'reopen']);
});

Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [DashboardController::class, 'index']);
    Route::post('/export', [DashboardController::class, 'export']);
});

Route::prefix('feed')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FeedController::class, 'index']);
});

Route::prefix('register')->middleware('auth:sanctum')->group(function () {
    Route::get('/{period}', [RegisterController::class, 'index'])->middleware('admin');
    Route::get('/details/{id}', [RegisterController::class, 'show'])->middleware('admin');
});

Route::prefix('feedback')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [FeedbackController::class, 'store']);
});

Route::prefix('enterprise')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EnterpriseController::class, 'show']);
    Route::get('/view', [EnterpriseController::class, 'showViewEnterprises'])->middleware('admin');
    Route::get('/search/{text}', [EnterpriseController::class, 'search']);
    Route::get('/show/{id}', [EnterpriseController::class, 'filter']);
    Route::post('/view', [EnterpriseController::class, 'saveViewEnterprise'])->middleware('admin');
    Route::post('/enterprise-counter', [EnterpriseController::class, 'storeByCounter']);
    Route::put('/', [EnterpriseController::class, 'update'])->middleware('admin');
    Route::put('/unlink', [EnterpriseController::class, 'unlink'])->middleware('admin');
    Route::put('/code-financial', [EnterpriseController::class, 'updateCodeFinancial'])->middleware('admin');
    Route::delete('/{id}', [EnterpriseController::class, 'destroy'])->middleware('admin');
});

Route::prefix('office')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EnterpriseController::class, 'indexOffices']);
    Route::post('/', [EnterpriseController::class, 'storeOffice'])->middleware('admin');
    Route::delete('/{id}', [EnterpriseController::class, 'destroyOffice'])->middleware('admin');
});
