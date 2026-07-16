<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;

header('Content-Type: text/plain; charset=utf-8');
echo "=== CREATING 4 TEST LATE CUSTOMERS ===\n\n";

$userId = User::first()?->id ?? 1;
$product = Product::first();
if (!$product) {
    die("Error: No product found in database. Please create a product first.\n");
}
$productId = $product->id;

$testCustomers = [
    [
        'name' => '[Test] ស៊ិន ស៊ីសាមុត',
        'phone' => '012345678',
        'gender' => 'male',
        'occupation' => 'Singer',
        'monthly_income' => 1500.00
    ],
    [
        'name' => '[Test] រស់ សេរីសុទ្ធា',
        'phone' => '012345679',
        'gender' => 'female',
        'occupation' => 'Singer',
        'monthly_income' => 1200.00
    ],
    [
        'name' => '[Test] ប៉ែន រ៉ន',
        'phone' => '012345680',
        'gender' => 'female',
        'occupation' => 'Singer',
        'monthly_income' => 1000.00
    ],
    [
        'name' => '[Test] មាស សាម៉ុន',
        'phone' => '012345681',
        'gender' => 'male',
        'occupation' => 'Musician',
        'monthly_income' => 1100.00
    ]
];

foreach ($testCustomers as $tc) {
    // 1. Create customer
    $customer = Customer::create([
        'name' => $tc['name'],
        'phone' => $tc['phone'],
        'gender' => $tc['gender'],
        'occupation' => $tc['occupation'],
        'monthly_income' => $tc['monthly_income'],
        'created_by' => $userId,
        'type' => 'installment'
    ]);
    
    echo "Created customer: {$customer->name} (ID: {$customer->id})\n";

    // 2. Create installment plan
    $installment = Installment::create([
        'customer_id' => $customer->id,
        'product_id' => $productId,
        'total_price' => 1000.00,
        'down_payment' => 100.00,
        'interest_rate' => 12.00,
        'duration_months' => 12,
        'monthly_payment' => 84.00,
        'remaining_balance' => 900.00,
        'status' => 'active',
        'created_by' => $userId,
        'next_due_date' => now()->subDays(15)->toDateString() // Overdue
    ]);
    
    echo "Created active installment for {$customer->name} (ID: {$installment->id})\n";

    // 3. Create a payment that is 45 days ago (so no payment in last 30 days)
    Payment::create([
        'installment_id' => $installment->id,
        'amount' => 100.00, // Down payment
        'payment_date' => now()->subDays(45)->toDateString(),
        'status' => 'approved',
        'approved_by' => $userId
    ]);
    
    echo "Added payment 45 days ago for Installment ID {$installment->id}.\n";
    echo "--------------------------------------------------\n";
}

echo "Successfully added 4 test late customers!\n";
echo "Go to the Late Payments page to see them.\n";
