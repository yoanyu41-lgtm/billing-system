<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;

header('Content-Type: text/plain; charset=utf-8');
echo "=== TESTING PAYMENT SEARCH ===\n";

$search = "Yu Yoan";
echo "Search term: '$search'\n";

$query = Payment::with('installment.customer', 'paymentMethod', 'user');

$query->where(function ($q) use ($search) {
    $q->whereHas('installment.customer', function ($sub) use ($search) {
        $sub->where('name', 'like', "%{$search}%");
    })
    ->orWhereHas('paymentMethod', function ($sub) use ($search) {
        $sub->where('name', 'like', "%{$search}%");
    })
    ->orWhere('title', 'like', "%{$search}%")
    ->orWhere('amount', 'like', "%{$search}%")
    ->orWhere('payment_date', 'like', "%{$search}%");
});

$sql = $query->toSql();
$bindings = $query->getBindings();
echo "SQL: $sql\n";
echo "Bindings: " . json_encode($bindings) . "\n";

$results = $query->get();
echo "Count: " . $results->count() . "\n";
foreach ($results as $p) {
    echo "- ID: {$p->id}, Customer: " . ($p->installment?->customer?->name ?? 'N/A') . ", Amount: {$p->amount}, Method: " . ($p->paymentMethod?->name ?? 'N/A') . "\n";
}
