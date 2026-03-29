<?php

use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LatePaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Installments
    Route::resource('installments', InstallmentController::class);

    // Payments
    Route::resource('payments', PaymentController::class)->except(['edit', 'update']);
    Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

    // Invoices
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Late Payments
    Route::get('late-payments', [LatePaymentController::class, 'index'])->name('late-payments.index');
    Route::post('late-payments/{installment}/remind', [LatePaymentController::class, 'sendReminder'])->name('late-payments.remind');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::resource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Products
        Route::resource('products', ProductController::class);

        // Reports
        Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/customer', [ReportController::class, 'customer'])->name('reports.customer');
        Route::get('reports/income', [ReportController::class, 'income'])->name('reports.income');
        Route::get('reports/{type}/export', [ReportController::class, 'exportPdf'])->name('reports.export');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

        // Backups
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{filename}', [BackupController::class, 'download'])->name('backups.download');
        Route::post('backups/restore', [BackupController::class, 'restore'])->name('backups.restore');
    });
});

// API routes
Route::post('/api/telegram/webhook', [TelegramController::class, 'webhook']);

Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('customers', [CustomerController::class, 'index']);
    Route::get('customers/{customer}', [CustomerController::class, 'show']);
    Route::post('customers', [CustomerController::class, 'store']);
    Route::put('customers/{customer}', [CustomerController::class, 'update']);
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy']);

    Route::get('installments', [InstallmentController::class, 'index']);
    Route::get('installments/{installment}', [InstallmentController::class, 'show']);
    Route::post('installments', [InstallmentController::class, 'store']);
    Route::put('installments/{installment}', [InstallmentController::class, 'update']);
    Route::delete('installments/{installment}', [InstallmentController::class, 'destroy']);

    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    Route::get('reports/revenue', [ReportController::class, 'income']);
    Route::get('reports/{type}/export', [ReportController::class, 'exportPdf']);
});

require __DIR__.'/auth.php';
