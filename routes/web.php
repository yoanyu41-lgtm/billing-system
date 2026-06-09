<?php

use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerCreditCheckController;
use App\Http\Controllers\GuarantorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LatePaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TelegramLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Redirect to dashboard if authenticated, otherwise to login
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'km'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard API (year filter for charts)
    Route::get('/dashboard/monthly-revenue',    [DashboardController::class, 'monthlyRevenue'])->name('dashboard.monthly-revenue');
    Route::get('/dashboard/monthly-collection', [DashboardController::class, 'monthlyCollection'])->name('dashboard.monthly-collection');

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/guarantors', [GuarantorController::class, 'store'])->name('guarantors.store');
    Route::put('customers/{customer}/guarantors/{guarantor}', [GuarantorController::class, 'update'])->name('guarantors.update');
    Route::delete('customers/{customer}/guarantors/{guarantor}', [GuarantorController::class, 'destroy'])->name('guarantors.destroy');
    Route::post('customers/{customer}/credit-checks', [CustomerCreditCheckController::class, 'store'])->name('credit-checks.store');
    Route::put('customers/{customer}/credit-checks/{creditCheck}', [CustomerCreditCheckController::class, 'update'])->name('credit-checks.update');
    Route::delete('customers/{customer}/credit-checks/{creditCheck}', [CustomerCreditCheckController::class, 'destroy'])->name('credit-checks.destroy');

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
    Route::post('late-payments/due-reminders', [LatePaymentController::class, 'sendDueDateReminders'])->name('late-payments.due-reminders');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::resource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Products
        Route::get('products/stock', [ProductController::class, 'stockIndex'])->name('products.stock');
        Route::post('products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.stock.update');
        Route::resource('products', ProductController::class);

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Payment Methods
        Route::resource('payment-methods', PaymentMethodController::class)->except(['create', 'show', 'edit']);

        // Reports
        Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/customer', [ReportController::class, 'customer'])->name('reports.customer');
        Route::get('reports/income', [ReportController::class, 'income'])->name('reports.income');
        Route::get('reports/{type}/export', [ReportController::class, 'exportPdf'])->name('reports.export');

        // Suppliers, Purchases, Sales
        Route::resource('suppliers', App\Http\Controllers\SupplierController::class)->except(['show']);
        Route::get('purchases', [App\Http\Controllers\PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [App\Http\Controllers\PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [App\Http\Controllers\PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('purchases/{purchase}/edit', [App\Http\Controllers\PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::put('purchases/{purchase}', [App\Http\Controllers\PurchaseController::class, 'update'])->name('purchases.update');
        Route::delete('purchases/{purchase}', [App\Http\Controllers\PurchaseController::class, 'destroy'])->name('purchases.destroy');
        Route::get('sales/create', [App\Http\Controllers\SaleController::class, 'create'])->name('sales.create');
        Route::post('sales', [App\Http\Controllers\SaleController::class, 'store'])->name('sales.store');

        // Stock movements
        Route::get('stock-movements', [App\Http\Controllers\StockMovementController::class, 'index'])->name('stock-movements.index');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

        // Backups
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{filename}', [BackupController::class, 'download'])->name('backups.download');
        Route::post('backups/restore', [BackupController::class, 'restore'])->name('backups.restore');
    });

    // Telegram Center
    Route::get('telegram-logs', [TelegramLogController::class, 'index'])->name('telegram-logs.index');
    Route::post('telegram-logs/set-webhook', [TelegramLogController::class, 'setWebhook'])->name('telegram-logs.set-webhook');
    Route::post('telegram-logs/send-test', [TelegramLogController::class, 'sendTestMessage'])->name('telegram-logs.send-test');
});

require __DIR__.'/auth.php';
