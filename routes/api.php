<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CellController;
use App\Http\Controllers\CellMemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\FinancialReceiptController;
use App\Http\Controllers\MemberChurchController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MinistryController;
// use App\Http\Controllers\MovementAnalyzeController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PreRegistrationConfigController;
use App\Http\Controllers\PreRegistrationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\SettingsCounterController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/reset', [UserController::class, 'reset']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('/newPassword', [UserController::class, 'resetPassword']);
Route::get('/active-pre-registration/{id}', [PreRegistrationConfigController::class, 'check']);
Route::post('/create-pre-registration', [PreRegistrationController::class, 'store']);

// ENDPOINTS PARA INTERGRAÇÃO COM WHATSAPP
// Route::prefix('external')->group(function () {
//     Route::post('/check-phone', [MovementAnalyzeController::class, 'checkPhone']);
//     Route::post('/informations', [MovementAnalyzeController::class, 'informations']);
//     Route::prefix('movement-analyze')->group(function () {
//         Route::post('/', [MovementAnalyzeController::class, 'store']);
//     });
// });

Route::prefix('webhook-asaas')->middleware(['webhook.token'])->group(function () {
    Route::post('/', [AsaasWebhookController::class, 'webhook']);
});

Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::put('/data', [UserController::class, 'updateData']);
});

// ENDPOINTS PARA INTERGRAÇÃO COM WHATSAPP
// Route::prefix('movement-analyze')->middleware(['auth:sanctum'])->group(function () {
//     Route::get('/', [MovementAnalyzeController::class, 'index']);
//     Route::post('/finalize', [MovementAnalyzeController::class, 'finalize']);
//     Route::put('/', [MovementAnalyzeController::class, 'update']);
//     Route::delete('/{id}', [MovementAnalyzeController::class, 'destroy']);
// });

Route::prefix('member')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::get('/profile', [MemberController::class, 'getProfile']);
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

Route::prefix('category')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/panel', [CategoryController::class, 'categoryPanel']);
    Route::get('/filter', [CategoryController::class, 'filterCategories']);
    Route::post('/export', [CategoryController::class, 'export']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/active/{id}', [CategoryController::class, 'active']);
    Route::put('/code', [CategoryController::class, 'updateCategoryCode']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('department')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::post('/', [DepartmentController::class, 'store'])->middleware('admin');
    Route::put('/', [DepartmentController::class, 'update'])->middleware('admin');
    Route::delete('/{id}', [DepartmentController::class, 'destroy'])->middleware('admin');
});

Route::prefix('relationship')->middleware(['auth:sanctum', 'not.free'])->group(function () {
    Route::get('/', [RelationshipController::class, 'index']);
    Route::post('/', [RelationshipController::class, 'store'])->middleware('admin');
    Route::put('/', [RelationshipController::class, 'update'])->middleware('admin');
    Route::delete('/{id}', [RelationshipController::class, 'destroy'])->middleware('admin');
});

Route::prefix('alert')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/{id}', [AlertController::class, 'index']);
    Route::post('/', [AlertController::class, 'store']);
    Route::put('/', [AlertController::class, 'update']);
    Route::delete('/{id}', [AlertController::class, 'destroy']);
});

Route::prefix('role')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store'])->middleware('not.free');
    Route::put('/', [RoleController::class, 'update'])->middleware('not.free');
    Route::delete('/{id}', [RoleController::class, 'destroy']);
});

Route::prefix('network')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [NetworkController::class, 'index']);
    Route::post('/', [NetworkController::class, 'store'])->middleware('not.free');
    Route::put('/', [NetworkController::class, 'update'])->middleware('not.free');
    Route::delete('/{id}', [NetworkController::class, 'destroy']);
});

Route::prefix('member-church')->middleware(['auth:sanctum'])->group(function () {

    Route::prefix('pre-registration')->middleware('not.free')->group(function () {
        Route::prefix('config')->group(function () {
            Route::get('', [PreRegistrationConfigController::class, 'index']);
            Route::put('', [PreRegistrationConfigController::class, 'update']);
        });

        Route::get('', [PreRegistrationController::class, 'index']);
        Route::delete('{id}', [PreRegistrationController::class, 'destroy']);
    });

    Route::get('/', [MemberChurchController::class, 'index']);
    Route::post('/', [MemberChurchController::class, 'store'])->middleware('not.free');
    Route::put('/', [MemberChurchController::class, 'update'])->middleware('not.free');
    Route::put('/active', [MemberChurchController::class, 'active'])->middleware('not.free');
    Route::delete('/{id}', [MemberChurchController::class, 'destroy']);
});

Route::prefix('ministry')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [MinistryController::class, 'index']);
    Route::post('/', [MinistryController::class, 'store'])->middleware('not.free');
    Route::put('/', [MinistryController::class, 'update'])->middleware('not.free');
    Route::delete('/{id}', [MinistryController::class, 'destroy']);
});

Route::prefix('cell')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('member')->middleware('not.free')->group(function () {
        Route::post('', [CellMemberController::class, 'store']);
        Route::delete('', [CellMemberController::class, 'destroy']);
    });

    Route::get('/', [CellController::class, 'index']);
    Route::post('/', [CellController::class, 'store'])->middleware('not.free');
    Route::put('/', [CellController::class, 'update'])->middleware('not.free');
    Route::delete('/{id}', [CellController::class, 'destroy']);

});

Route::prefix('movement')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/member', [MovementController::class, 'indexMovementMember']);
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

Route::prefix('scheduling')->middleware(['auth:sanctum'])->group(function () {
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

Route::prefix('account')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'store'])->middleware('admin');
    Route::post('/export', [AccountController::class, 'export']);
    Route::post('/transfer', [AccountController::class, 'createTransfer']);
    Route::put('/', [AccountController::class, 'update'])->middleware('admin');
    Route::put('/active/{id}', [AccountController::class, 'active']);
    Route::delete('/{id}', [AccountController::class, 'destroy'])->middleware('admin');
});

Route::prefix('order')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/client', [OrderController::class, 'indexViewClient']);
    Route::get('/counter', [OrderController::class, 'indexViewCounter']);
    Route::get('/bonds', [OrderController::class, 'indexBonds']);
    Route::post('/sendRequest', [OrderController::class, 'store']);
    Route::post('/responseClient', [OrderController::class, 'actionClient']);
    Route::put('/', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
    Route::delete('/bond/{id}', [OrderController::class, 'destroyBond']);
});

Route::prefix('financial')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [FinancialController::class, 'index']);
    Route::get('/movements-observations/{date}', [FinancialController::class, 'indexObservations'])->middleware(['not.free']);
    Route::post('/', [FinancialController::class, 'finalize'])->middleware(['admin', 'not.free']);
    Route::get('/settings-counter', [SettingsCounterController::class, 'index'])->middleware(['admin', 'not.free']);
    Route::put('/settings-counter', [SettingsCounterController::class, 'update'])->middleware(['admin', 'not.free']);

    Route::prefix('file-financial')->group(function () {
        Route::get('/{monthYear}', [FinancialReceiptController::class, 'index']);
        Route::post('/{monthYear}', [FinancialReceiptController::class, 'store'])->middleware(['not.free']);
        Route::delete('/{id}', [FinancialReceiptController::class, 'destroy'])->middleware(['not.free']);
    });
});

Route::prefix('report')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/{id}', [ReportController::class, 'index']);
    Route::get('/details/{id}', [ReportController::class, 'details']);
    Route::post('/undo/{id}', [ReportController::class, 'undo']);
    Route::post('/finalize/{id}', [ReportController::class, 'finalize']);
    Route::post('/export/{reportId}', [ReportController::class, 'downloadReport']);
    Route::post('/movement/update/', [ReportController::class, 'updateMovementByCounter']);
    Route::delete('/movement/{id}', [ReportController::class, 'destroyMovement']);
    Route::delete('/reopen/{id}', [ReportController::class, 'reopen']);
});

Route::prefix('dashboard')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [DashboardController::class, 'index']);
    Route::post('/export', [DashboardController::class, 'export']);
});

Route::prefix('feed')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [FeedController::class, 'index']);
});

Route::prefix('register')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/{period}', [RegisterController::class, 'index'])->middleware('admin');
    Route::get('/details/{id}', [RegisterController::class, 'show'])->middleware('admin');
});

Route::prefix('feedback')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [FeedbackController::class, 'store']);
});

Route::prefix('enterprise')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [EnterpriseController::class, 'show']);
    Route::get('/view', [EnterpriseController::class, 'showViewEnterprises'])->middleware('admin');
    Route::get('/search/{text}', [EnterpriseController::class, 'search']);
    Route::get('/show/{id}', [EnterpriseController::class, 'filter']);
    Route::post('/view', [EnterpriseController::class, 'saveViewEnterprise'])->middleware('admin');
    Route::post('/enterprise-counter', [EnterpriseController::class, 'storeByCounter']);
    Route::put('/', [EnterpriseController::class, 'update'])->middleware('admin');
    Route::put('/viewByCounter', [EnterpriseController::class, 'setViewByCounter'])->middleware('admin');
    Route::put('/unlink', [EnterpriseController::class, 'unlink'])->middleware('admin');
    Route::put('/code-financial', [EnterpriseController::class, 'updateCodeFinancial'])->middleware('admin');
    Route::delete('/{id}', [EnterpriseController::class, 'destroy'])->middleware('admin');

    Route::prefix('coupon')->group(function () {
        Route::get('/', [EnterpriseController::class, 'getCoupons']);
        Route::post('/{name}', [EnterpriseController::class, 'setCoupon']);
        Route::delete('/{id}', [EnterpriseController::class, 'removeCoupon']);
    });
});

Route::prefix('office')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [EnterpriseController::class, 'indexOffices']);
    Route::post('/', [EnterpriseController::class, 'storeOffice'])->middleware(['admin', 'not.free']);
    Route::delete('/{id}', [EnterpriseController::class, 'destroyOffice'])->middleware('admin');
});

// Route::prefix('resource')->middleware(['auth:sanctum'])->group(function () {
//     Route::get('/subscription', [EnterpriseController::class, 'mySubscription']);
//     Route::get('/coupons', [EnterpriseController::class, 'myCoupons']);
// });

Route::prefix('subscription')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [SubscriptionController::class, 'index']);
    Route::prefix('payment')->group(function () {
        Route::post('/credit-card', [SubscriptionController::class, 'paymentCreditCard']);
        Route::post('/pix', [SubscriptionController::class, 'paymentPix']);
        // Route::post('/free', [SubscriptionController::class, 'updateFreeSubscription']);
    });
});
