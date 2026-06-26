<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InstallmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $installments = $query->paginate(10);
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        return view('installments.index', compact('installments', 'exchangeRate'));
    }

    public function create()
    {
        $customers = Customer::where('type', 'installment')->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        return view('installments.create', compact('customers', 'products', 'exchangeRate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'total_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0|lte:total_price',
            'interest_rate' => 'nullable|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
        ]);

        // Get tax settings
        $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
        $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);
        
        $product = Product::find($request->product_id);
        
        // Calculate tax on total price if applicable
        $subtotalBeforeTax = $request->total_price;
        $taxAmount = 0;
        $taxRate = 0;
        
        if ($taxEnabled && $product->is_taxable) {
            // Use product-specific tax rate if set, otherwise use default
            $taxRate = $product->tax_rate > 0 ? $product->tax_rate : $defaultTaxRate;
            
            // Calculate tax based on tax type
            if ($product->tax_type === 'inclusive') {
                // Tax is already included in price, extract it
                $taxAmount = $subtotalBeforeTax - ($subtotalBeforeTax / (1 + $taxRate / 100));
                $subtotalBeforeTax = $request->total_price - $taxAmount;
                $totalPrice = $request->total_price;
            } else {
                // Tax is exclusive (default), add it on top
                $taxAmount = $subtotalBeforeTax * ($taxRate / 100);
                $totalPrice = $subtotalBeforeTax + $taxAmount;
            }
        } else {
            $totalPrice = $subtotalBeforeTax;
        }
        $downPayment = $request->down_payment;
        $interestRate = $request->interest_rate ?? 0;
        $duration = $request->duration_months;

        $principal = $totalPrice - $downPayment;
        $monthlyInterest = ($principal * $interestRate / 100) / 12;
        $monthlyPayment = round(($principal / $duration) + $monthlyInterest, 2);
        $remainingBalance = round($monthlyPayment * $duration, 2);

        $installment = Installment::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'total_price' => $totalPrice,
            'subtotal_before_tax' => $subtotalBeforeTax,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'down_payment' => $downPayment,
            'interest_rate' => $interestRate,
            'duration_months' => $duration,
            'monthly_payment' => $monthlyPayment,
            'remaining_balance' => $remainingBalance,
            'created_by' => auth()->id(),
            'next_due_date' => now()->addMonth()->toDateString(),
        ]);

        // Send welcoming Telegram message with contract if customer is linked
        $customer = Customer::find($request->customer_id);
        if ($customer && !empty($customer->telegram_id)) {
            $contractDownloadLink = route('public.installments.download-contract', $installment->id);
            $khmerAmount = number_format($installment->total_price, 2);
            $khmerMonthly = number_format($installment->monthly_payment, 2);
            
            $msg = "📄 *កិច្ចសន្យាបង់រំលស់ថ្មី / New Installment Contract*\n\n"
                . "សូមជម្រាបជូនអតិថិជន *{$customer->name}*៖\n"
                . "គម្រោងបង់រំលស់សម្រាប់ផលិតផល *{$product->name}* ត្រូវបានបង្កើតឡើងដោយជោគជ័យនៅក្នុងប្រព័ន្ធ។\n"
                . "• លេខកិច្ចសន្យា៖ *#INS-" . str_pad($installment->id, 3, '0', STR_PAD_LEFT) . "*\n"
                . "• តម្លៃសរុប៖ *$" . $khmerAmount . "*\n"
                . "• ត្រូវបង់ប្រចាំខែ៖ *$" . $khmerMonthly . "*\n"
                . "• រយៈពេលបង់រំលស់៖ *{$installment->duration_months} ខែ*\n\n"
                . "សូមលោកអ្នកទាញយកឯកសារកិច្ចសន្យាបង់រំលស់ផ្លូវការជា PDF ទីនេះ៖ [ទាញយកកិច្ចសន្យា PDF]({$contractDownloadLink})";

            app(TelegramService::class)->sendToCustomer($customer->id, $msg);
        }

        return redirect()->route('installments.index')->with('success', 'Installment created successfully.');
    }

    public function show(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        $installment->load('customer', 'product');
        $schedule = $installment->getPaymentSchedule();
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        return view('installments.show', compact('installment', 'schedule', 'paymentMethods', 'exchangeRate'));
    }

    public function payOffIndex()
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product')->where('status', 'active');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $installments = $query->latest()->paginate(10);

        // Pre-compute payoff (outstanding principal) for each plan.
        $installments->getCollection()->transform(function ($installment) {
            $installment->payoff_amount = $installment->outstandingPrincipal();
            return $installment;
        });

        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view('installments.pay-off-index', compact('installments', 'paymentMethods'));
    }

    public function payOff(Request $request, Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);

        if ($installment->status !== 'active') {
            return redirect()->route('installments.show', $installment)
                ->with('error', __('app.installment_not_active'));
        }

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_date'      => 'required|date',
            'title'             => 'nullable|string|max:255',
            'interest_rate'     => 'nullable|numeric|min:0|max:100',
        ]);

        $principal = $installment->outstandingPrincipal();

        if ($principal <= 0) {
            return redirect()->route('installments.show', $installment)
                ->with('error', __('app.nothing_to_pay_off'));
        }

        // Optional extra interest applied on the outstanding principal at settlement.
        $interestRate = (float) ($request->interest_rate ?? 0);
        $payoffAmount = round($principal + ($principal * $interestRate / 100), 2);

        // Record the settlement as an approved payment.
        $payment = Payment::create([
            'installment_id'    => $installment->id,
            'payment_method_id' => $request->payment_method_id,
            'amount'            => $payoffAmount,
            'payment_date'      => $request->payment_date,
            'status'            => 'approved',
            'is_settlement'     => true,
            'title'             => $request->title,
            'interest_rate'     => $interestRate,
            'approved_by'       => auth()->id(),
        ]);

        // Close the plan.
        $installment->update([
            'remaining_balance' => 0,
            'status'            => 'completed',
        ]);

        // Generate an invoice for the settlement.
        Invoice::create([
            'payment_id'     => $payment->id,
            'invoice_number' => 'INV-' . $payment->id,
        ]);

        return redirect()->route('installments.show', $installment)
            ->with('success', __('app.pay_off_success', ['amount' => number_format($payoffAmount, 2)]));
    }

    public function edit(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        $customers = Customer::all();
        $products = Product::where('is_active', true)
            ->orWhere('id', $installment->product_id)
            ->orderBy('name')
            ->get();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        return view('installments.edit', compact('installment', 'customers', 'products', 'exchangeRate'));
    }

    public function update(Request $request, Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);

        $request->validate([
            'total_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0|lte:total_price',
            'interest_rate' => 'nullable|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'status' => 'required|in:active,cancelled,completed,paid',
        ]);

        // Get tax settings
        $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
        $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);
        
        $product = $installment->product;
        
        // Calculate tax on total price if applicable
        $subtotalBeforeTax = $request->total_price;
        $taxAmount = 0;
        $taxRate = 0;
        
        if ($taxEnabled && $product->is_taxable) {
            // Use product-specific tax rate if set, otherwise use default
            $taxRate = $product->tax_rate > 0 ? $product->tax_rate : $defaultTaxRate;
            
            // Calculate tax based on tax type
            if ($product->tax_type === 'inclusive') {
                // Tax is already included in price, extract it
                $taxAmount = $subtotalBeforeTax - ($subtotalBeforeTax / (1 + $taxRate / 100));
                $subtotalBeforeTax = $request->total_price - $taxAmount;
                $totalPrice = $request->total_price;
            } else {
                // Tax is exclusive (default), add it on top
                $taxAmount = $subtotalBeforeTax * ($taxRate / 100);
                $totalPrice = $subtotalBeforeTax + $taxAmount;
            }
        } else {
            $totalPrice = $subtotalBeforeTax;
        }
        $downPayment = $request->down_payment;
        $interestRate = $request->interest_rate ?? 0;
        $duration = $request->duration_months;

        $principal = $totalPrice - $downPayment;
        $monthlyInterest = ($principal * $interestRate / 100) / 12;
        $monthlyPayment = round(($principal / $duration) + $monthlyInterest, 2);
        $remainingBalance = in_array($request->status, ['cancelled', 'completed', 'paid']) ? 0 : round($monthlyPayment * $duration, 2);

        $installment->update([
            'total_price' => $totalPrice,
            'subtotal_before_tax' => $subtotalBeforeTax,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'down_payment' => $downPayment,
            'interest_rate' => $interestRate,
            'duration_months' => $duration,
            'monthly_payment' => $monthlyPayment,
            'remaining_balance' => $remainingBalance,
            'status' => $request->status,
        ]);

        return redirect()->route('installments.index')->with('success', 'Installment updated successfully.');
    }

    public function destroy(Installment $installment)
    {
        Gate::authorize('delete-installment');

        // Delete related records in correct order to avoid foreign key constraints
        // 1. First delete invoices related to payments from this installment
        \DB::table('invoices')
            ->whereIn('payment_id', $installment->payments()->pluck('id'))
            ->delete();
        
        // 2. Then delete all payments
        $installment->payments()->delete();
        
        // 3. Finally delete the installment
        $installment->delete();
        
        return redirect()->route('installments.index')->with('success', 'Installment deleted successfully.');
    }

    public function scheduleIndex()
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $installments = $query->latest()->paginate(10);

        return view('installments.schedule-index', compact('installments'));
    }

    public function contractIndex()
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $installments = $query->latest()->paginate(10);

        return view('installments.contract-index', compact('installments'));
    }

    public function paymentSchedule(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);

        $installment->load('customer', 'product');
        $schedule = $installment->getPaymentSchedule();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        $totalPaid = $installment->payments()
            ->where('status', 'approved')
            ->sum('amount');

        $totalScheduled = round(array_sum(array_column($schedule, 'amount')), 2);

        $summary = [
            'total_scheduled'   => $totalScheduled,
            'total_principal'   => round(array_sum(array_column($schedule, 'principal')), 2),
            'total_interest'    => round(array_sum(array_column($schedule, 'interest')), 2),
            'total_paid'        => round((float) $totalPaid, 2),
            'remaining'         => round(max($totalScheduled - (float) $totalPaid, 0), 2),
            'paid_count'        => count(array_filter($schedule, fn ($row) => $row['status'] === 'paid')),
            'overdue_count'     => count(array_filter($schedule, fn ($row) => $row['status'] === 'overdue')),
        ];

        return view('installments.schedule', compact('installment', 'schedule', 'summary', 'paymentMethods'));
    }

    public function printContract(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        
        $customer = $installment->customer;
        $product = $installment->product;
        $guarantor = $customer->guarantors()->first(); // Get first guarantor if exists
        $contractTerms = \App\Models\ContractTerm::where('is_active', true)
            ->orderBy('sort_order')->orderBy('id')->get();
        
        // Calculate payment schedule
        $paymentSchedule = [];
        $currentDate = \Carbon\Carbon::parse($installment->created_at);
        
        for ($i = 1; $i <= $installment->duration_months; $i++) {
            $dueDate = $currentDate->copy()->addMonths($i);
            $principal = round(($installment->total_price - $installment->down_payment) / $installment->duration_months, 2);
            $interest = round((($installment->total_price - $installment->down_payment) * $installment->interest_rate / 100) / 12, 2);
            
            $paymentSchedule[] = [
                'month' => $i,
                'date' => $dueDate->format('d/m/Y'),
                'principal' => $principal,
                'interest' => $interest,
                'total' => round($principal + $interest, 2),
            ];
        }
        
        return view('installments.contract', compact('installment', 'customer', 'product', 'guarantor', 'paymentSchedule', 'contractTerms'));
    }

    public function uploadContract(Request $request, Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        
        $request->validate([
            'contract_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);
        
        // Delete old contract if exists
        if ($installment->signed_contract) {
            \Storage::disk('public')->delete($installment->signed_contract);
        }
        
        // Store new contract
        $path = $request->file('contract_file')->store('contracts', 'public');
        
        $installment->update([
            'signed_contract' => $path,
            'contract_signed_at' => now(),
            'contract_signed_by' => auth()->user()->name,
        ]);
        
        return redirect()->route('installments.show', $installment)
            ->with('success', 'Contract uploaded successfully.');
    }

    public function downloadContract(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        
        if (!$installment->signed_contract) {
            abort(404, 'Contract not found.');
        }
        
        $filePath = storage_path('app/public/' . $installment->signed_contract);
        
        if (!file_exists($filePath)) {
            abort(404, 'Contract file not found.');
        }
        
        $fileName = 'Contract-INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT) . '-' . date('Y') . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        
        return response()->download($filePath, $fileName);
    }

    public function deleteContract(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        
        if ($installment->signed_contract) {
            \Storage::disk('public')->delete($installment->signed_contract);
            
            $installment->update([
                'signed_contract' => null,
                'contract_signed_at' => null,
                'contract_signed_by' => null,
            ]);
        }
        
        return redirect()->route('installments.show', $installment)
            ->with('success', 'Contract deleted successfully.');
    }

    public function sendTelegramQr(Installment $installment, $month)
    {
        Gate::authorize('manage-installment', $installment);

        $customer = $installment->customer;
        if (!$customer || empty($customer->telegram_id)) {
            return redirect()->back()->with('error', __('app.telegram_id_missing'));
        }

        $bankQr = \App\Models\Setting::where('key', 'company_bank_qr')->value('value');
        if (empty($bankQr)) {
            return redirect()->back()->with('error', __('app.shop_qr_missing'));
        }

        // Get schedule to retrieve due date and amount
        $schedule = $installment->getPaymentSchedule();
        $targetMonth = null;
        foreach ($schedule as $row) {
            if ($row['month'] == $month) {
                $targetMonth = $row;
                break;
            }
        }

        if (!$targetMonth) {
            return redirect()->back()->with('error', 'Invalid installment month.');
        }

        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        $amount = $targetMonth['amount'];
        $amountRiel = round($amount * $exchangeRate);
        $dueDate = \Carbon\Carbon::parse($targetMonth['due_date'])->format('d-m-Y');

        $isKm = app()->getLocale() === 'km';
        if ($isKm) {
            $message = "⏰ *សេចក្តីជូនដំណឹងអំពីការទូទាត់ប្រាក់ / Payment Request Notification*\n\n"
                . "សូមជម្រាបជូនអតិថិជន *{$customer->name}*៖\n"
                . "នេះជាកាលវិភាគបង់ប្រាក់សម្រាប់គម្រោងបង់រំលស់ទំនិញ៖ *{$installment->product->name}*\n"
                . "• " . __('app.installment_month') . "៖ *{$month}*\n"
                . "• ថ្ងៃកំណត់បង់៖ *{$dueDate}*\n"
                . "• ទឹកប្រាក់ត្រូវបង់៖ *$" . number_format($amount, 2) . "* (ឬ ~ *" . number_format($amountRiel) . "* ៛)\n\n"
                . "សូមលោកអ្នកស្កេន QR Code ខាងក្រោមដើម្បីទូទាត់ប្រាក់ និងផ្ញើរូបភាពបង្កាន់ដៃ (Payment Slip) ត្រឡប់មកវិញដើម្បីបញ្ជាក់ការបង់ប្រាក់។ សូមអរគុណ! 🙏";
        } else {
            $message = "⏰ *Payment Request Notification / សេចក្តីជូនដំណឹងអំពីការទូទាត់ប្រាក់*\n\n"
                . "Dear Customer *{$customer->name}*:\n"
                . "This is the payment request for your installment plan: *{$installment->product->name}*\n"
                . "• " . __('app.installment_month') . ": *{$month}*\n"
                . "• Due Date: *{$dueDate}*\n"
                . "• Amount Due: *$" . number_format($amount, 2) . "* (or ~ *" . number_format($amountRiel) . "* KHR)\n\n"
                . "Please scan the QR Code below to make payment and reply with your transfer receipt/slip image. Thank you! 🙏";
        }

        $telegramResult = $this->telegramService->sendPhotoToCustomer($installment->customer_id, $bankQr, $message);

        if ($telegramResult['ok']) {
            return redirect()->back()->with('success', __('app.payment_request_sent'));
        } else {
            return redirect()->back()->with('error', 'Telegram error: ' . $telegramResult['reason']);
        }
    }

    public function publicDownloadContract(Installment $installment)
    {
        $customer = $installment->customer;
        $product = $installment->product;
        $guarantor = $customer->guarantors()->first();
        $contractTerms = \App\Models\ContractTerm::where('is_active', true)
            ->orderBy('sort_order')->orderBy('id')->get();
        
        $paymentSchedule = [];
        $currentDate = \Carbon\Carbon::parse($installment->created_at);
        
        for ($i = 1; $i <= $installment->duration_months; $i++) {
            $dueDate = $currentDate->copy()->addMonths($i);
            $principal = round(($installment->total_price - $installment->down_payment) / $installment->duration_months, 2);
            $interest = round((($installment->total_price - $installment->down_payment) * $installment->interest_rate / 100) / 12, 2);
            
            $paymentSchedule[] = [
                'month' => $i,
                'date' => $dueDate->format('d/m/Y'),
                'principal' => $principal,
                'interest' => $interest,
                'total' => round($principal + $interest, 2),
            ];
        }

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('installments.contract_pdf', compact('installment', 'customer', 'product', 'guarantor', 'paymentSchedule', 'contractTerms', 'settings'));
        return $pdf->download('Contract-INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function printClearance(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        
        if ($installment->status !== 'completed' && $installment->remaining_balance > 0) {
            $errorMsg = app()->getLocale() === 'km' 
                ? 'គម្រោងបង់រំលស់នេះមិនទាន់បានបញ្ចប់ការបង់ប្រាក់នៅឡើយទេ។' 
                : 'This installment plan is not completed yet.';
            return redirect()->route('installments.show', $installment)->with('error', $errorMsg);
        }

        $customer = $installment->customer;
        $product = $installment->product;
        
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $exchangeRate = (float) ($settings['exchange_rate'] ?? 4100);

        // Sum of all approved payments
        $totalPaid = $installment->payments()
            ->where('status', 'approved')
            ->sum('amount');

        // Total interest and principal paid
        $schedule = $installment->getPaymentSchedule();
        $totalInterest = array_sum(array_column($schedule, 'interest'));
        $totalPrincipal = array_sum(array_column($schedule, 'principal'));

        return view('installments.clearance', compact(
            'installment',
            'customer',
            'product',
            'totalPaid',
            'totalInterest',
            'totalPrincipal',
            'settings',
            'exchangeRate'
        ));
    }

    public function clearanceIndex()
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product')
            ->where(function($q) {
                $q->where('status', 'completed')
                  ->orWhere('remaining_balance', '<=', 0);
            });

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $installments = $query->latest()->paginate(10);
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        return view('installments.clearance-index', compact('installments', 'exchangeRate'));
    }
}
