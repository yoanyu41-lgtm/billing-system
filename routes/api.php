<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\PurchaseApiController;
use App\Http\Controllers\Api\SaleApiController;
use App\Http\Controllers\Api\SupplierApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  (prefix: /api/v1, middleware: auth:sanctum)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Telegram webhook (no auth)
    Route::post('telegram/webhook', [\App\Http\Controllers\Api\TelegramController::class, 'webhook'])
        ->name('telegram.webhook');

    Route::middleware('auth:sanctum')->group(function () {

        // Authenticated user profile
        Route::get('user',        [UserApiController::class, 'me'])->name('user.me');

        // Users (admin only — enforced inside controller)
        Route::get('users',        [UserApiController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserApiController::class, 'show'])->name('users.show');

        // Customers
        Route::apiResource('customers', CustomerApiController::class);

        // Products
        Route::apiResource('products', ProductApiController::class);

        // Categories
        Route::apiResource('categories', CategoryApiController::class);

        // Suppliers
        Route::apiResource('suppliers', SupplierApiController::class);

        // Sales
        Route::get   ('sales',          [SaleApiController::class, 'index']);
        Route::get   ('sales/{sale}',   [SaleApiController::class, 'show']);
        Route::post  ('sales',          [SaleApiController::class, 'store']);
        Route::delete('sales/{sale}',   [SaleApiController::class, 'destroy']);

        // Purchases
        Route::get   ('purchases',              [PurchaseApiController::class, 'index']);
        Route::get   ('purchases/{purchase}',   [PurchaseApiController::class, 'show']);
        Route::post  ('purchases',              [PurchaseApiController::class, 'store']);
        Route::delete('purchases/{purchase}',   [PurchaseApiController::class, 'destroy']);

        // Reports
        Route::get('reports/revenue',       [ReportController::class, 'income']);
        Route::get('reports/{type}/export', [ReportController::class, 'exportPdf']);
    });
});
