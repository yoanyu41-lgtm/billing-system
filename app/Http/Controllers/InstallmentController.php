<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InstallmentController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $installments = $query->latest('id')->paginate(10)->withQueryString();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        $suggestions = [];
        $installmentQuery = Installment::query();
        if (!in_array($user->role, ['admin', 'staff'])) {
            $installmentQuery->where('created_by', $user->id);
        }

        $customerIds = (clone $installmentQuery)->pluck('customer_id')->unique()->filter();
        $productIds = (clone $installmentQuery)->pluck('product_id')->unique()->filter();

        $customers = \App\Models\Customer::whereIn('id', $customerIds)->get(['name', 'phone']);
        foreach ($customers as $c) {
            $suggestions[] = [
                'label' => $c->name . ($c->phone ? ' (' . $c->phone . ')' : ''),
                'value' => $c->name
            ];
        }

        $products = \App\Models\Product::whereIn('id', $productIds)->get(['name', 'code']);
        foreach ($products as $p) {
            $suggestions[] = [
                'label' => $p->name . ($p->code ? ' (' . $p->code . ')' : ''),
                'value' => $p->name
            ];
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('installments.index', compact('installments', 'exchangeRate', 'suggestions'));
    }

    public function create()
    {
        $customers = Customer::where('type', 'installment')
            ->whereDoesntHave('installments')
            ->orderBy('name')
            ->get();
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
            'interest_type' => 'nullable|string|in:flat,declining,amortization,bullet,zero_interest',
            'duration_months' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return back()->withInput()->with('error', __('app.out_of_stock') ?? 'This product is out of stock.');
        }

        // Get tax settings
        $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
        $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);
        
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
        $downPayment = (float) $request->down_payment;
        $interestRate = (float) ($request->interest_rate ?? 0);
        $interestType = $request->interest_type ?: \App\Services\InstallmentCalculationService::TYPE_FLAT;
        $duration = (int) $request->duration_months;

        $principal = max($totalPrice - $downPayment, 0);
        $preview = \App\Services\InstallmentCalculationService::calculatePreview($principal, $interestRate, $duration, $interestType);

        $monthlyPayment = $preview['first_month_payment'];
        $remainingBalance = $preview['total_repayment'];

        $installment = DB::transaction(function () use ($request, $product, $totalPrice, $subtotalBeforeTax, $taxRate, $taxAmount, $downPayment, $interestRate, $interestType, $duration, $monthlyPayment, $remainingBalance) {
            $inst = Installment::create([
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'total_price' => $totalPrice,
                'subtotal_before_tax' => $subtotalBeforeTax,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'down_payment' => $downPayment,
                'interest_rate' => $interestRate,
                'interest_type' => $interestType,
                'duration_months' => $duration,
                'monthly_payment' => $monthlyPayment,
                'remaining_balance' => $remainingBalance,
                'created_by' => auth()->id(),
                'next_due_date' => now()->addMonth()->toDateString(),
            ]);

            // Deduct product stock and log stock movement
            $product->decrement('stock', 1);
            $product->checkLowStockAlert();

            StockMovement::create([
                'product_id' => $product->id,
                'type'       => 'out',
                'quantity'   => 1,
                'related_id' => $inst->id,
                'note'       => 'Installment Contract #INS-' . str_pad($inst->id, 3, '0', STR_PAD_LEFT),
            ]);

            return $inst;
        });

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

        \App\Models\Notification::createSystemNotification(
            'installment', 'New installment contract',
            'New installment contract created for customer: ' . ($customer ? $customer->name : 'Unknown'),
            'file-text', 'orange',
            route('installments.show', $installment)
        );

        return redirect()->route('installments.index')->with('success', 'Installment created successfully.');
    }

    public function show(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        $installment->load('customer', 'product');
        $schedule = $installment->getPaymentSchedule();
        $paymentMethods = PaymentMethod::getAvailable();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        return view('installments.show', compact('installment', 'schedule', 'paymentMethods', 'exchangeRate'));
    }

    public function payOffIndex(Request $request)
    {
        // Remove debug.txt if it exists
        if (file_exists(base_path('debug.txt'))) {
            @unlink(base_path('debug.txt'));
        }

        $user = auth()->user();

        // Self-healing: Automatically complete any active installments that have been fully paid off
        $activeInstallments = Installment::where('status', 'active')->get();
        foreach ($activeInstallments as $inst) {
            if ($inst->outstandingPrincipal() <= 0) {
                $inst->update([
                    'status'            => 'completed',
                    'remaining_balance' => 0,
                    'next_due_date'     => null,
                ]);
            }
        }

        $query = Installment::with('customer', 'product')->where('status', 'active');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            });
        }

        $installments = $query->latest('id')->paginate(10)->withQueryString();

        $defaultInterestMode = \App\Models\Setting::where('key', 'payoff_interest_mode')->value('value') ?? 'one_month';

        // Pre-compute payoff (outstanding principal), scheduled remaining, and interest options for each plan.
        $installments->getCollection()->transform(function ($installment) use ($defaultInterestMode) {
            $installment->payoff_amount = $installment->outstandingPrincipal();

            // Calculate exact remaining balance from payment schedule (matches schedule page)
            $schedule = $installment->getPaymentSchedule();
            $totalScheduled = round(array_sum(array_column($schedule, 'amount')), 2);
            $totalPaid = (float) $installment->payments()
                ->where('status', 'approved')
                ->sum('amount');

            $installment->scheduled_remaining = ($installment->remaining_balance <= 0 || in_array($installment->status, ['completed', 'paid']))
                ? 0.00
                : round(max($totalScheduled - $totalPaid, 0), 2);

            // Compute:
            // 1. One month interest based on current outstanding principal
            $monthlyRate = (float) ($installment->interest_rate ?? 0);
            $oneMonthInterest = round($installment->payoff_amount * $monthlyRate / 100, 2);

            // 2. Total interest for all unpaid remaining scheduled months
            $allRemainingInterest = 0.00;
            foreach ($schedule as $row) {
                if ($row['status'] !== 'paid') {
                    $allRemainingInterest += (float) ($row['interest'] ?? 0);
                }
            }
            $allRemainingInterest = round($allRemainingInterest, 2);

            $installment->interest_one_month = $oneMonthInterest;
            $installment->interest_all_remaining = $allRemainingInterest;
            $installment->default_interest_amount = ($defaultInterestMode === 'all_remaining_months')
                ? $allRemainingInterest
                : $oneMonthInterest;

            // Check minimum eligible month condition
            $minMonth = (int) (\App\Models\Setting::where('key', 'payoff_min_month')->value('value') ?? 0);
            $paidMonths = $installment->paid_months;
            $installment->is_payoff_eligible = ($minMonth <= 0) || ($paidMonths >= $minMonth);
            $installment->payoff_min_required_month = $minMonth;

            return $installment;
        });

        $paymentMethods = PaymentMethod::getAvailable();

        // Load suggestions from active installments for autocomplete dropdown
        $suggestions = [];
        $installmentQuery = Installment::where('status', 'active');
        if (!in_array($user->role, ['admin', 'staff'])) {
            $installmentQuery->where('created_by', $user->id);
        }

        $customerIds = (clone $installmentQuery)->pluck('customer_id')->unique()->filter();
        $productIds = (clone $installmentQuery)->pluck('product_id')->unique()->filter();

        $customers = \App\Models\Customer::whereIn('id', $customerIds)->get(['name', 'phone']);
        foreach ($customers as $c) {
            $suggestions[] = [
                'label' => $c->name . ($c->phone ? ' (' . $c->phone . ')' : ''),
                'value' => $c->name
            ];
        }

        $products = \App\Models\Product::whereIn('id', $productIds)->get(['name', 'code']);
        foreach ($products as $p) {
            $suggestions[] = [
                'label' => $p->name . ($p->code ? ' (' . $p->code . ')' : ''),
                'value' => $p->name
            ];
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('installments.pay-off-index', compact('installments', 'paymentMethods', 'suggestions', 'defaultInterestMode'));
    }

    public function payOff(Request $request, Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);

        if ($installment->status !== 'active') {
            return redirect()->route('installments.show', $installment)
                ->with('error', __('app.installment_not_active'));
        }

        // Validate minimum paid month rule
        $minMonth = (int) (\App\Models\Setting::where('key', 'payoff_min_month')->value('value') ?? 0);
        $paidMonths = $installment->paid_months;
        if ($minMonth > 0 && $paidMonths < $minMonth) {
            $msg = app()->getLocale() === 'km'
                ? "អតិថិជនមិនទាន់អាចបង់ផ្តាច់បានទេ! តាមគោលការណ៍ត្រូវបង់យ៉ាងតិច {$minMonth} ខែជាមុនសិន (បច្ចុប្បន្នបង់បាន {$paidMonths} ខែ)។"
                : "Not eligible for early payoff yet! Policy requires minimum {$minMonth} months paid (currently {$paidMonths} months).";
            return redirect()->back()->with('error', $msg);
        }

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_date'      => 'required|date',
            'title'             => 'nullable|string|max:255',
            'payoff_type'       => 'nullable|in:principal_only,principal_interest',
            'interest_rate'     => 'nullable|numeric|min:0|max:100',
            'interest_amount'   => 'nullable|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'late_fee'          => 'nullable|numeric|min:0',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $principal = $installment->outstandingPrincipal();

        if ($principal <= 0) {
            return redirect()->route('installments.show', $installment)
                ->with('error', __('app.nothing_to_pay_off'));
        }

        $payoffType = $request->payoff_type ?? 'principal_only';
        $interestRate = 0.0;
        $interestAmount = 0.0;

        if ($payoffType === 'principal_interest') {
            $interestRate = (float) ($request->interest_rate ?? 0);
            if ($request->filled('interest_amount')) {
                $interestAmount = (float) $request->interest_amount;
            } elseif ($interestRate > 0) {
                $interestAmount = round($principal * $interestRate / 100, 2);
            }
        }

        $discount = (float) ($request->discount ?? 0);
        $lateFee = (float) ($request->late_fee ?? 0);

        // Calculate total payoff: Principal + Interest - Discount + Late Fee
        $subtotal = $principal + $interestAmount;
        $payoffAmount = round(max($subtotal - $discount + $lateFee, 0), 2);

        // Record the settlement as an approved payment.
        $payment = Payment::create([
            'installment_id'    => $installment->id,
            'payment_method_id' => $request->payment_method_id,
            'amount'            => $payoffAmount,
            'penalty_amount'    => $lateFee,
            'payment_date'      => $request->payment_date,
            'status'            => 'approved',
            'is_settlement'     => true,
            'payoff_type'       => $payoffType,
            'principal_amount'  => $principal,
            'interest_amount'   => $interestAmount,
            'discount_amount'   => $discount,
            'late_fee'          => $lateFee,
            'payment_reference' => $request->payment_reference,
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

        \App\Models\Notification::createSystemNotification(
            'payment', 'Installment Payoff',
            'Installment contract for customer ' . $installment->customer->name . ' has been fully paid off with $' . number_format($payoffAmount, 2),
            'check-circle', 'green',
            route('installments.show', $installment)
        );

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
            'customer_id' => 'nullable|exists:customers,id',
            'product_id' => 'nullable|exists:products,id',
            'total_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0|lte:total_price',
            'interest_rate' => 'nullable|numeric|min:0',
            'interest_type' => 'nullable|string|in:flat,declining,amortization,bullet,zero_interest',
            'duration_months' => 'required|integer|min:1',
            'status' => 'required|in:active,cancelled,completed,paid',
            'first_payment_date' => 'nullable|date',
        ]);

        // Get tax settings
        $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
        $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);
        
        $product = $request->product_id ? Product::find($request->product_id) : $installment->product;
        
        // Calculate tax on total price if applicable
        $subtotalBeforeTax = (float) $request->total_price;
        $taxAmount = 0;
        $taxRate = 0;
        
        if ($taxEnabled && $product && $product->is_taxable) {
            $taxRate = $product->tax_rate > 0 ? $product->tax_rate : $defaultTaxRate;
            
            if ($product->tax_type === 'inclusive') {
                $taxAmount = $subtotalBeforeTax - ($subtotalBeforeTax / (1 + $taxRate / 100));
                $subtotalBeforeTax = $request->total_price - $taxAmount;
                $totalPrice = (float) $request->total_price;
            } else {
                $taxAmount = $subtotalBeforeTax * ($taxRate / 100);
                $totalPrice = $subtotalBeforeTax + $taxAmount;
            }
        } else {
            $totalPrice = $subtotalBeforeTax;
        }
        $downPayment = (float) $request->down_payment;
        $interestRate = (float) ($request->interest_rate ?? 0);
        $interestType = $request->interest_type ?: ($installment->interest_type ?? \App\Services\InstallmentCalculationService::TYPE_FLAT);
        $duration = (int) $request->duration_months;

        $principal = max($totalPrice - $downPayment, 0);
        $preview = \App\Services\InstallmentCalculationService::calculatePreview($principal, $interestRate, $duration, $interestType);
        $monthlyPayment = $preview['first_month_payment'];
        
        // Calculate remaining balance based on actual payments recorded or contract terms
        $totalPaid = $installment->payments()->where('status', 'approved')->sum('amount');
        $totalContractValue = round($downPayment + $preview['total_repayment'], 2);
        $remainingBalance = in_array($request->status, ['cancelled', 'completed', 'paid']) ? 0 : max(0, round($totalContractValue - $downPayment - $totalPaid, 2));

        $updateData = [
            'total_price' => $totalPrice,
            'subtotal_before_tax' => $subtotalBeforeTax,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'down_payment' => $downPayment,
            'interest_rate' => $interestRate,
            'interest_type' => $interestType,
            'duration_months' => $duration,
            'monthly_payment' => $monthlyPayment,
            'remaining_balance' => $remainingBalance,
            'status' => $request->status,
        ];

        if ($request->customer_id) {
            $updateData['customer_id'] = $request->customer_id;
        }
        if ($request->product_id) {
            $updateData['product_id'] = $request->product_id;
        }
        if ($request->first_payment_date) {
            $updateData['first_payment_date'] = $request->first_payment_date;
            $updateData['next_due_date'] = $request->first_payment_date;
        }

        DB::transaction(function () use ($installment, $updateData, $request) {
            $oldProductId = $installment->product_id;
            $newProductId = $request->product_id ? (int)$request->product_id : $oldProductId;
            $oldStatus = $installment->status;
            $newStatus = $request->status;

            // Handle product change while active
            if ($oldStatus !== 'cancelled' && $newStatus !== 'cancelled') {
                if ($oldProductId && $newProductId && $oldProductId !== $newProductId) {
                    $oldProd = Product::find($oldProductId);
                    if ($oldProd) {
                        $oldProd->increment('stock', 1);
                        StockMovement::create([
                            'product_id' => $oldProd->id,
                            'type'       => 'in',
                            'quantity'   => 1,
                            'related_id' => $installment->id,
                            'note'       => 'Product changed from Installment #INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT),
                        ]);
                    }
                    $newProd = Product::find($newProductId);
                    if ($newProd) {
                        $newProd->decrement('stock', 1);
                        $newProd->checkLowStockAlert();
                        StockMovement::create([
                            'product_id' => $newProd->id,
                            'type'       => 'out',
                            'quantity'   => 1,
                            'related_id' => $installment->id,
                            'note'       => 'Product changed to Installment #INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT),
                        ]);
                    }
                }
            }

            // Handle status change to/from cancelled
            if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
                $prod = Product::find($oldProductId);
                if ($prod) {
                    $prod->increment('stock', 1);
                    StockMovement::create([
                        'product_id' => $prod->id,
                        'type'       => 'in',
                        'quantity'   => 1,
                        'related_id' => $installment->id,
                        'note'       => 'Cancelled Installment #INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT),
                    ]);
                }
            } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                $prod = Product::find($newProductId);
                if ($prod) {
                    $prod->decrement('stock', 1);
                    $prod->checkLowStockAlert();
                    StockMovement::create([
                        'product_id' => $prod->id,
                        'type'       => 'out',
                        'quantity'   => 1,
                        'related_id' => $installment->id,
                        'note'       => 'Re-activated Installment #INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT),
                    ]);
                }
            }

            $installment->update($updateData);
        });

        return redirect()->route('installments.index')->with('success', __('app.updated_successfully') ?? 'Installment updated successfully.');
    }

    public function destroy(Installment $installment)
    {
        Gate::authorize('delete-installment');
        DB::transaction(function () use ($installment) {
            if ($installment->status !== 'cancelled' && $installment->product_id) {
                $product = Product::find($installment->product_id);
                if ($product) {
                    $product->increment('stock', 1);
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type'       => 'in',
                        'quantity'   => 1,
                        'related_id' => $installment->id,
                        'note'       => 'Deleted Installment #INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT),
                    ]);
                }
            }
            $installment->delete();
        });
        return redirect()->route('installments.index')->with('success', 'Installment deleted successfully.');
    }

    private function getInstallmentSuggestions($user)
    {
        $suggestions = [];
        $query = Installment::query();
        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $customerIds = (clone $query)->pluck('customer_id')->unique()->filter();
        $productIds = (clone $query)->pluck('product_id')->unique()->filter();

        $customers = \App\Models\Customer::whereIn('id', $customerIds)->get(['name', 'phone']);
        foreach ($customers as $c) {
            $suggestions[] = [
                'label' => $c->name . ($c->phone ? ' (' . $c->phone . ')' : ''),
                'value' => $c->name
            ];
        }

        $products = \App\Models\Product::whereIn('id', $productIds)->get(['name', 'code']);
        foreach ($products as $p) {
            $suggestions[] = [
                'label' => $p->name . ($p->code ? ' (' . $p->code . ')' : ''),
                'value' => $p->name
            ];
        }
        return collect($suggestions)->unique('label')->values()->all();
    }

    public function scheduleIndex(Request $request)
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%" . ltrim($search, '#INS-') . "%");
            });
        }

        $installments = $query->latest('id')->paginate(10)->withQueryString();
        $suggestions = $this->getInstallmentSuggestions($user);

        return view('installments.schedule-index', compact('installments', 'suggestions'));
    }

    public function contractIndex(Request $request)
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%" . ltrim($search, '#INS-') . "%");
            });
        }

        $installments = $query->latest('id')->paginate(10)->withQueryString();
        $suggestions = $this->getInstallmentSuggestions($user);

        return view('installments.contract-index', compact('installments', 'suggestions'));
    }

    public function paymentSchedule(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);

        $installment->load('customer', 'product');
        $schedule = $installment->getPaymentSchedule();
        $paymentMethods = PaymentMethod::getAvailable();

        $totalPaid = $installment->payments()
            ->where('status', 'approved')
            ->sum('amount');

        $totalScheduled = round(array_sum(array_column($schedule, 'amount')), 2);

        $summary = [
            'total_scheduled'   => $totalScheduled,
            'total_principal'   => round(array_sum(array_column($schedule, 'principal')), 2),
            'total_interest'    => round(array_sum(array_column($schedule, 'interest')), 2),
            'total_penalty'     => round(array_sum(array_column($schedule, 'penalty_amount')), 2),
            'total_paid'        => round((float) $totalPaid, 2),
            'remaining'         => ($installment->remaining_balance <= 0 || in_array($installment->status, ['completed', 'paid'])) ? 0.00 : round(max($totalScheduled - (float) $totalPaid, 0), 2),
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
        
        // Calculate payment schedule using calculation service
        $principalBase = max($installment->total_price - $installment->down_payment, 0);
        $contractSchedule = \App\Services\InstallmentCalculationService::generateSchedule(
            $principalBase,
            (float) $installment->interest_rate,
            $installment->duration_months,
            $installment->interest_type ?? \App\Services\InstallmentCalculationService::TYPE_FLAT,
            \Carbon\Carbon::parse($installment->created_at)
        );

        $paymentSchedule = [];
        foreach ($contractSchedule as $row) {
            $paymentSchedule[] = [
                'month'     => $row['month'],
                'date'      => \Carbon\Carbon::parse($row['due_date'])->format('d/m/Y'),
                'principal' => $row['principal'],
                'interest'  => $row['interest'],
                'total'     => $row['total'],
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

        // Determine which QR image to send (from request qr_key or fallback to default)
        $qrKey  = request()->input('qr_key', 'company_bank_qr');
        $bankQr = \App\Models\Setting::where('key', $qrKey)->value('value');

        // Fallback to default company QR if the selected one is missing
        if (empty($bankQr)) {
            $bankQr = \App\Models\Setting::where('key', 'company_bank_qr')->value('value');
        }

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
        
        $principalBase = max($installment->total_price - $installment->down_payment, 0);
        $contractSchedule = \App\Services\InstallmentCalculationService::generateSchedule(
            $principalBase,
            (float) $installment->interest_rate,
            $installment->duration_months,
            $installment->interest_type ?? \App\Services\InstallmentCalculationService::TYPE_FLAT,
            \Carbon\Carbon::parse($installment->created_at)
        );

        $paymentSchedule = [];
        foreach ($contractSchedule as $row) {
            $paymentSchedule[] = [
                'month'     => $row['month'],
                'date'      => \Carbon\Carbon::parse($row['due_date'])->format('d/m/Y'),
                'principal' => $row['principal'],
                'interest'  => $row['interest'],
                'total'     => $row['total'],
            ];
        }

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return view('installments.contract', compact('installment', 'customer', 'product', 'guarantor', 'paymentSchedule', 'contractTerms', 'settings'));
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

        // Check if there is an early settlement (Pay Off) payment
        $settlementPayment = $installment->payments()
            ->where('status', 'approved')
            ->where('is_settlement', true)
            ->latest('payment_date')
            ->first();

        return view('installments.clearance', compact(
            'installment',
            'customer',
            'product',
            'totalPaid',
            'totalInterest',
            'totalPrincipal',
            'settlementPayment',
            'settings',
            'exchangeRate'
        ));
    }

    public function clearanceIndex(Request $request)
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            });
        }

        $installments = $query->latest('id')->paginate(10)->withQueryString();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        // Load suggestions from completed installments for autocomplete dropdown
        $suggestions = [];
        $installmentQuery = Installment::where(function($q) {
                $q->where('status', 'completed')
                  ->orWhere('remaining_balance', '<=', 0);
            });
        if (!in_array($user->role, ['admin', 'staff'])) {
            $installmentQuery->where('created_by', $user->id);
        }

        $customerIds = (clone $installmentQuery)->pluck('customer_id')->unique()->filter();
        $productIds = (clone $installmentQuery)->pluck('product_id')->unique()->filter();

        $customers = \App\Models\Customer::whereIn('id', $customerIds)->get(['name', 'phone']);
        foreach ($customers as $c) {
            $suggestions[] = [
                'label' => $c->name . ($c->phone ? ' (' . $c->phone . ')' : ''),
                'value' => $c->name
            ];
        }

        $products = \App\Models\Product::whereIn('id', $productIds)->get(['name', 'code']);
        foreach ($products as $p) {
            $suggestions[] = [
                'label' => $p->name . ($p->code ? ' (' . $p->code . ')' : ''),
                'value' => $p->name
            ];
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('installments.clearance-index', compact('installments', 'exchangeRate', 'suggestions'));
    }

    public function restore($id)
    {
        Gate::authorize('delete-installment');
        $installment = Installment::onlyTrashed()->findOrFail($id);
        
        DB::transaction(function () use ($installment) {
            if ($installment->status !== 'cancelled' && $installment->product_id) {
                $product = Product::find($installment->product_id);
                if ($product) {
                    $product->decrement('stock', 1);
                    $product->checkLowStockAlert();
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type'       => 'out',
                        'quantity'   => 1,
                        'related_id' => $installment->id,
                        'note'       => 'Restored Installment #INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT),
                    ]);
                }
            }
            $installment->restore();
        });

        return redirect()->route('customers.trash', ['tab' => 'installments'])->with('success', 'Installment restored successfully.');
    }

    public function forceDelete($id)
    {
        Gate::authorize('delete-installment');
        $installment = Installment::onlyTrashed()->findOrFail($id);
        
        // Delete related payments and invoices permanently
        \DB::table('invoices')
            ->whereIn('payment_id', $installment->payments()->withTrashed()->pluck('id'))
            ->delete();
        $installment->payments()->withTrashed()->forceDelete();
        
        $installment->forceDelete();
        return redirect()->route('customers.trash', ['tab' => 'installments'])->with('success', 'Installment permanently deleted.');
    }
}
