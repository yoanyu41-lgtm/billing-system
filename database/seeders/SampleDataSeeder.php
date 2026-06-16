<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        $firstProduct = Product::first();
        $productDellId = $firstProduct ? $firstProduct->id : 1;
        $productPcId = $firstProduct ? $firstProduct->id : 1;

        // 1. Seed Mock Customers
        $customerData = [
            ['name' => 'Sok Dara', 'phone' => '012345678', 'gender' => 'male', 'address' => 'Phnom Penh'],
            ['name' => 'Chan Navy', 'phone' => '098765432', 'gender' => 'female', 'address' => 'Siem Reap'],
        ];

        $customers = [];
        foreach ($customerData as $c) {
            $customer = Customer::where('name', $c['name'])->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $c['name'],
                    'phone' => $c['phone'],
                    'gender' => $c['gender'],
                    'address' => $c['address'],
                    'created_by' => $adminId,
                ]);
            }
            $customers[$c['name']] = $customer;
        }

        // 2. Seed Installments and Payments
        // Sok Dara: Installment starting Jan 2026
        $instDara = Installment::where('customer_id', $customers['Sok Dara']->id)->first();
        if (!$instDara) {
            $instDara = Installment::create([
                'customer_id' => $customers['Sok Dara']->id,
                'product_id' => $productDellId,
                'total_price' => 1200.00,
                'subtotal_before_tax' => 1090.91,
                'tax_rate' => 10.00,
                'tax_amount' => 109.09,
                'down_payment' => 200.00,
                'interest_rate' => 0.00,
                'duration_months' => 6,
                'monthly_payment' => 166.67,
                'remaining_balance' => 0.00,
                'status' => 'completed',
                'created_by' => $adminId,
                'created_at' => Carbon::create(2026, 1, 5, 10, 0, 0),
            ]);

            // Add 6 monthly payments for Dara (Jan to Jun)
            for ($m = 1; $m <= 6; $m++) {
                Payment::create([
                    'installment_id' => $instDara->id,
                    'amount' => 166.67,
                    'payment_date' => Carbon::create(2026, $m, 15),
                    'payment_method_id' => 12, // Cash
                    'status' => 'approved',
                    'approved_by' => $adminId,
                    'created_at' => Carbon::create(2026, $m, 15, 14, 0, 0),
                ]);
            }
        }

        // Chan Navy: Installment starting Feb 2026
        $instNavy = Installment::where('customer_id', $customers['Chan Navy']->id)->first();
        if (!$instNavy) {
            $instNavy = Installment::create([
                'customer_id' => $customers['Chan Navy']->id,
                'product_id' => $productPcId,
                'total_price' => 900.00,
                'subtotal_before_tax' => 818.18,
                'tax_rate' => 10.00,
                'tax_amount' => 81.82,
                'down_payment' => 100.00,
                'interest_rate' => 0.00,
                'duration_months' => 6,
                'monthly_payment' => 133.33,
                'remaining_balance' => 400.01,
                'status' => 'active',
                'next_due_date' => Carbon::create(2026, 8, 20),
                'created_by' => $adminId,
                'created_at' => Carbon::create(2026, 2, 10, 11, 0, 0),
            ]);

            // Add 5 monthly payments for Navy (Feb to Jun)
            for ($m = 2; $m <= 6; $m++) {
                Payment::create([
                    'installment_id' => $instNavy->id,
                    'amount' => 133.33,
                    'payment_date' => Carbon::create(2026, $m, 20),
                    'payment_method_id' => 1, // QR
                    'status' => 'approved',
                    'approved_by' => $adminId,
                    'created_at' => Carbon::create(2026, $m, 20, 15, 0, 0),
                ]);
            }
        }

        // 3. Seed Direct Sales
        if (Sale::count() == 0) {
            $salesData = [
                ['customer' => 'Oun Vong', 'date' => '2026-01-10', 'total' => 800.00],
                ['customer' => 'Som Ath', 'date' => '2026-02-12', 'total' => 1600.00],
                ['customer' => 'Lim Heng', 'date' => '2026-03-05', 'total' => 800.00],
                ['customer' => 'Yen Sophea', 'date' => '2026-04-18', 'total' => 2400.00],
                ['customer' => 'Srun Sok', 'date' => '2026-05-22', 'total' => 1600.00],
                ['customer' => 'Koy Vanna', 'date' => '2026-06-12', 'total' => 3200.00],
            ];

            foreach ($salesData as $index => $sd) {
                $subtotal_before_tax = round($sd['total'] / 1.1, 2);
                $tax_amount = round($sd['total'] - $subtotal_before_tax, 2);

                // Create or find direct customer record
                $customer = Customer::where('name', $sd['customer'])
                    ->where('type', 'direct')
                    ->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'name' => $sd['customer'],
                        'type' => 'direct',
                        'created_by' => $adminId,
                    ]);
                }

                Sale::create([
                    'invoice_no' => 'INV-2026-' . str_pad((string)($index + 1), 6, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'customer_name' => $sd['customer'],
                    'sale_date' => $sd['date'],
                    'subtotal_before_tax' => $subtotal_before_tax,
                    'tax_amount' => $tax_amount,
                    'subtotal' => $subtotal_before_tax,
                    'total' => $sd['total'],
                    'payment_method' => 'cash',
                    'created_by' => $adminId,
                    'created_at' => Carbon::parse($sd['date'])->addHours(12),
                ]);
            }
        }
    }
}
