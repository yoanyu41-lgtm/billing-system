<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Installment;

header('Content-Type: text/plain; charset=utf-8');
echo "=== ACTIVE INSTALLMENTS ===\n\n";

$installments = Installment::with('customer', 'payments')
    ->where('status', 'active')
    ->where('remaining_balance', '>', 0)
    ->get();

echo "Total active installments: " . $installments->count() . "\n\n";

foreach ($installments as $inst) {
    echo "ID: {$inst->id}\n";
    echo "Customer: {$inst->customer->name} (ID: {$inst->customer_id})\n";
    echo "Remaining Balance: {$inst->remaining_balance}\n";
    echo "Next Due Date: {$inst->next_due_date}\n";
    echo "Payments count: " . $inst->payments->count() . "\n";
    foreach ($inst->payments as $p) {
        echo "  - Payment ID: {$p->id}, Date: {$p->payment_date}, Status: {$p->status}, Amount: {$p->amount}\n";
    }
    echo "--------------------\n";
}
