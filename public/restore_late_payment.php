<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Installment;
use App\Models\Payment;

header('Content-Type: text/plain; charset=utf-8');
echo "=== RESTORING INSTALLMENT ID 6 ===\n\n";

$inst = Installment::find(6);
if (!$inst) {
    die("Error: Installment ID 6 not found.\n");
}

// Restore payment date of payment ID 16
$payment = Payment::find(16);
if ($payment) {
    $payment->update([
        'payment_date' => '2026-06-28' // original date
    ]);
    echo "Restored Payment ID 16 date to 2026-06-28.\n";
} else {
    echo "Warning: Payment ID 16 not found.\n";
}

// Restore next due date of installment to the future
$inst->update([
    'next_due_date' => '2026-08-28' // original date
]);
echo "Restored Installment ID 6 next_due_date to 2026-08-28.\n\n";

echo "Now refresh your 'Late Payments' (ការទូទាត់យឺត) page in the browser to verify it is cleared!\n";
