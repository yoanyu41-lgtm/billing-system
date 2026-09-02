<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * List all direct sales.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'creator', 'items.product'])->withCount('items');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $sales = $query->latest()->paginate(15)->withQueryString();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        // Suggestions for autocomplete
        $suggestions = [];
        $saleCustomers = Sale::whereNotNull('customer_name')->get(['customer_name', 'customer_phone']);
        foreach ($saleCustomers as $sc) {
            if ($sc->customer_name) {
                $suggestions[] = [
                    'label' => $sc->customer_name . ($sc->customer_phone ? ' (' . $sc->customer_phone . ')' : ''),
                    'value' => $sc->customer_name
                ];
            }
        }
        $saleInvoices = Sale::whereNotNull('invoice_no')->pluck('invoice_no');
        foreach ($saleInvoices as $inv) {
            $suggestions[] = [
                'label' => $inv,
                'value' => $inv
            ];
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('admin.sales.index', compact('sales', 'exchangeRate', 'suggestions'));
    }

    /**
     * Show the direct-sale (point-of-sale) form.
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
        $customers = Customer::orderBy('name')->get();
        $paymentMethods = \App\Models\PaymentMethod::getAvailable();

        return view('admin.sales.create', compact('products', 'customers', 'paymentMethods'));
    }

    /**
     * Persist a new direct sale, decrement stock, and record movements.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'customer_name'      => 'nullable|string|max:255',
            'customer_phone'     => 'nullable|string|max:50',
            'sale_date'          => 'nullable|date',
            'discount'           => 'nullable|numeric|min:0',
            'payment_method'     => 'nullable|string|max:50',
            'note'               => 'nullable|string|max:1000',
            'save_as_customer'   => 'nullable|boolean',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        // If a registered customer was chosen, take their name/phone.
        if (!empty($validated['customer_id'])) {
            $customer = Customer::find($validated['customer_id']);
            $validated['customer_name']  = $validated['customer_name'] ?: $customer->name;
            $validated['customer_phone'] = $validated['customer_phone'] ?: $customer->phone;
        }

        // Automatically save the buyer as a direct-sale customer.
        // If no name is given, use a generic "Walk-in customer" record.
        $savedNewCustomer = false;
        if (empty($validated['customer_id'])) {

            $buyerName = $validated['customer_name'] ?: __('app.walk_in_customer');

            // Reuse an existing customer with the same phone (when phone is provided).
            $existing = null;
            if (!empty($validated['customer_phone'])) {
                $existing = Customer::where('phone', $validated['customer_phone'])->first();
            }

            if ($existing) {
                $validated['customer_id'] = $existing->id;
            } else {
                $newCustomer = Customer::create([
                    'name'       => $buyerName,
                    'phone'      => $validated['customer_phone'] ?? null,
                    'type'       => 'direct',
                    'created_by' => auth()->id(),
                ]);
                $validated['customer_id']   = $newCustomer->id;
                $validated['customer_name'] = $buyerName;
                $savedNewCustomer = true;
            }
        }

        try {
            $sale = DB::transaction(function () use ($validated, $request) {
                // Verify stock availability first.
                foreach ($validated['items'] as $it) {
                    $product = Product::lockForUpdate()->find($it['product_id']);
                    if ($product->stock < $it['quantity']) {
                        throw new \RuntimeException(
                            __('app.insufficient_stock_for', ['product' => $product->name, 'stock' => $product->stock])
                        );
                    }
                }

                // Get tax settings
                $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
                $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);

                $originalSubtotal = 0;
                $subtotalBeforeTax = 0;
                $totalTaxAmount = 0;

                // Calculate subtotal and tax for each item
                $itemsWithTax = [];
                foreach ($validated['items'] as $it) {
                    $product = Product::find($it['product_id']);
                    $itemTotal = $it['price'] * $it['quantity'];
                    $originalSubtotal += $itemTotal;
                    
                    // Determine if this item is taxable and what rate to use
                    $itemTaxRate = 0;
                    $itemTaxAmount = 0;
                    $itemSubtotal = $itemTotal;
                    
                    if ($taxEnabled && $product->is_taxable) {
                        // Use product-specific tax rate if set, otherwise use default
                        $itemTaxRate = $product->tax_rate > 0 ? $product->tax_rate : $defaultTaxRate;
                        
                        // Calculate tax based on tax type
                        if ($product->tax_type === 'inclusive') {
                            // Tax is already included in price, extract it
                            $itemTaxAmount = $itemTotal - ($itemTotal / (1 + $itemTaxRate / 100));
                            $itemSubtotal = $itemTotal - $itemTaxAmount;
                        } else {
                            // Tax is exclusive (default), add it on top
                            $itemTaxAmount = $itemTotal * ($itemTaxRate / 100);
                            $itemSubtotal = $itemTotal;
                        }
                    }
                    
                    $subtotalBeforeTax += $itemSubtotal;
                    $totalTaxAmount += $itemTaxAmount;
                    
                    $itemsWithTax[] = [
                        'data' => $it,
                        'tax_rate' => $itemTaxRate,
                        'tax_amount' => $itemTaxAmount,
                    ];
                }

                // Calculate final totals
                $discount = (float) ($validated['discount'] ?? 0);
                $totalBeforeDiscount = $subtotalBeforeTax + $totalTaxAmount;
                $total = max($totalBeforeDiscount - $discount, 0);
                
                // Apply discount proportionally to tax if discount exists
                $finalTaxAmount = $totalTaxAmount;
                if ($discount > 0 && $totalBeforeDiscount > 0) {
                    $discountRatio = $total / $totalBeforeDiscount;
                    $finalTaxAmount = $totalTaxAmount * $discountRatio;
                }

                $sale = Sale::create([
                    'invoice_no'         => Sale::generateInvoiceNo(),
                    'customer_id'        => $validated['customer_id'] ?? null,
                    'customer_name'      => $validated['customer_name'] ?? null,
                    'customer_phone'     => $validated['customer_phone'] ?? null,
                    'sale_date'          => $validated['sale_date'] ?? now(),
                    'subtotal'           => $originalSubtotal,
                    'subtotal_before_tax' => $subtotalBeforeTax,
                    'discount'           => $discount,
                    'tax_amount'         => $finalTaxAmount,
                    'total'              => $total,
                    'payment_method'     => $validated['payment_method'] ?? 'cash',
                    'note'               => $validated['note'] ?? null,
                    'created_by'         => auth()->id(),
                ]);

                foreach ($itemsWithTax as $item) {
                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $item['data']['product_id'],
                        'quantity'   => $item['data']['quantity'],
                        'price'      => $item['data']['price'],
                        'tax_rate'   => $item['tax_rate'],
                        'tax_amount' => $item['tax_amount'],
                    ]);

                    StockMovement::create([
                        'product_id' => $item['data']['product_id'],
                        'type'       => 'out',
                        'quantity'   => $item['data']['quantity'],
                        'related_id' => $sale->id,
                        'note'       => 'Direct Sale ' . $sale->invoice_no,
                    ]);

                    $prod = Product::find($item['data']['product_id']);
                    if ($prod) {
                        $prod->decrement('stock', $item['data']['quantity']);
                        $prod->checkLowStockAlert();
                    }
                }

                return $sale;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        \App\Models\Notification::createSystemNotification(
            'sale', 'New sale recorded',
            'New sale: Invoice #' . $sale->invoice_no . ' for $' . number_format($sale->total, 2),
            'shopping-cart', 'blue',
            route('admin.sales.show', $sale)
        );

        return redirect()->route('admin.sales.show', $sale)
            ->with('success', $savedNewCustomer
                ? __('app.customer_saved_success')
                : __('app.sale_recorded_success'));
    }

    /**
     * Show a single sale / printable receipt.
     */
    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'creator']);
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        return view('admin.sales.show', compact('sale', 'exchangeRate'));
    }

    /**
     * Show the direct-sale edit form.
     */
    public function edit(Sale $sale)
    {
        $sale->load(['items.product', 'customer']);
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();
        $customers = Customer::orderBy('name')->get();
        $paymentMethods = \App\Models\PaymentMethod::getAvailable();

        return view('admin.sales.edit', compact('sale', 'products', 'customers', 'paymentMethods'));
    }

    /**
     * Update an existing sale, adjust stock, and update records.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'customer_name'      => 'nullable|string|max:255',
            'customer_phone'     => 'nullable|string|max:50',
            'sale_date'          => 'nullable|date',
            'discount'           => 'nullable|numeric|min:0',
            'payment_method'     => 'nullable|string|max:50',
            'note'               => 'nullable|string|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        if (!empty($validated['customer_id'])) {
            $customer = Customer::find($validated['customer_id']);
            $validated['customer_name']  = $validated['customer_name'] ?: $customer->name;
            $validated['customer_phone'] = $validated['customer_phone'] ?: $customer->phone;
        }

        try {
            DB::transaction(function () use ($validated, $request, $sale) {
                // 1. Revert previous stock changes
                foreach ($sale->items as $oldItem) {
                    $prod = Product::find($oldItem->product_id);
                    if ($prod) {
                        $prod->increment('stock', $oldItem->quantity);
                    }
                }

                // 2. Verify stock availability for new items
                foreach ($validated['items'] as $it) {
                    $product = Product::lockForUpdate()->find($it['product_id']);
                    if ($product->stock < $it['quantity']) {
                        throw new \RuntimeException(
                            __('app.insufficient_stock_for', ['product' => $product->name, 'stock' => $product->stock])
                        );
                    }
                }

                // 3. Clear old items & movements
                $sale->items()->delete();
                StockMovement::where('related_id', $sale->id)->where('type', 'out')->delete();

                // 4. Calculate tax & totals
                $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
                $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);

                $originalSubtotal = 0;
                $subtotalBeforeTax = 0;
                $totalTaxAmount = 0;
                $itemsWithTax = [];

                foreach ($validated['items'] as $it) {
                    $product = Product::find($it['product_id']);
                    $itemTotal = $it['price'] * $it['quantity'];
                    $originalSubtotal += $itemTotal;

                    $itemTaxRate = 0;
                    $itemTaxAmount = 0;
                    $itemSubtotal = $itemTotal;

                    if ($taxEnabled && $product->is_taxable) {
                        $itemTaxRate = $product->tax_rate > 0 ? $product->tax_rate : $defaultTaxRate;

                        if ($product->tax_type === 'inclusive') {
                            $itemTaxAmount = $itemTotal - ($itemTotal / (1 + $itemTaxRate / 100));
                            $itemSubtotal = $itemTotal - $itemTaxAmount;
                        } else {
                            $itemTaxAmount = $itemTotal * ($itemTaxRate / 100);
                            $itemSubtotal = $itemTotal;
                        }
                    }

                    $subtotalBeforeTax += $itemSubtotal;
                    $totalTaxAmount += $itemTaxAmount;

                    $itemsWithTax[] = [
                        'data'       => $it,
                        'tax_rate'   => $itemTaxRate,
                        'tax_amount' => $itemTaxAmount,
                    ];
                }

                $discount = (float) ($validated['discount'] ?? 0);
                $totalBeforeDiscount = $subtotalBeforeTax + $totalTaxAmount;
                $total = max($totalBeforeDiscount - $discount, 0);

                $finalTaxAmount = $totalTaxAmount;
                if ($discount > 0 && $totalBeforeDiscount > 0) {
                    $discountRatio = $total / $totalBeforeDiscount;
                    $finalTaxAmount = $totalTaxAmount * $discountRatio;
                }

                // 5. Update Sale record
                $sale->update([
                    'customer_id'         => $validated['customer_id'] ?? null,
                    'customer_name'       => $validated['customer_name'] ?? null,
                    'customer_phone'      => $validated['customer_phone'] ?? null,
                    'sale_date'           => $validated['sale_date'] ?? now(),
                    'subtotal'            => $originalSubtotal,
                    'subtotal_before_tax' => $subtotalBeforeTax,
                    'discount'            => $discount,
                    'tax_amount'          => $finalTaxAmount,
                    'total'               => $total,
                    'payment_method'      => $validated['payment_method'] ?? 'cash',
                    'note'                => $validated['note'] ?? null,
                ]);

                // 6. Create new items and apply stock changes
                foreach ($itemsWithTax as $item) {
                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $item['data']['product_id'],
                        'quantity'   => $item['data']['quantity'],
                        'price'      => $item['data']['price'],
                        'tax_rate'   => $item['tax_rate'],
                        'tax_amount' => $item['tax_amount'],
                    ]);

                    StockMovement::create([
                        'product_id' => $item['data']['product_id'],
                        'type'       => 'out',
                        'quantity'   => $item['data']['quantity'],
                        'related_id' => $sale->id,
                        'note'       => 'Direct Sale (Updated) ' . $sale->invoice_no,
                    ]);

                    $prod = Product::find($item['data']['product_id']);
                    if ($prod) {
                        $prod->decrement('stock', $item['data']['quantity']);
                        $prod->checkLowStockAlert();
                    }
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.sales.show', $sale)
            ->with('success', __('app.sale_updated_success') ?? 'Sale updated successfully.');
    }

    /**
     * Download the sale receipt as a PDF.
     */
    public function download(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'creator']);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.sales.pdf', compact('sale', 'settings'));
        
        // Configure for Unicode support
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        
        return $pdf->download('receipt-' . ($sale->invoice_no ?? $sale->id) . '.pdf');
    }

    /**
     * Cancel a sale and restore stock.
     */
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'type'       => 'in',
                    'quantity'   => $item->quantity,
                    'related_id' => $sale->id,
                    'note'       => 'Reversal of sale ' . ($sale->invoice_no ?? ('#' . $sale->id)),
                ]);
            }

            $sale->delete();
        });

        return redirect()->route('admin.sales.index')
            ->with('success', __('app.sale_deleted_success'));
    }

    public function restore($id)
    {
        $sale = Sale::onlyTrashed()->with('items')->findOrFail($id);
        
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)->decrement('stock', $item->quantity);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'type'       => 'out',
                    'quantity'   => $item->quantity,
                    'related_id' => $sale->id,
                    'note'       => 'Restoration of sale ' . ($sale->invoice_no ?? ('#' . $sale->id)),
                ]);
            }
            $sale->restore();
        });

        return redirect()->route('customers.trash', ['tab' => 'sales'])->with('success', 'Sale restored successfully.');
    }

    public function forceDelete($id)
    {
        $sale = Sale::onlyTrashed()->findOrFail($id);
        
        DB::transaction(function () use ($sale) {
            $sale->items()->delete();
            $sale->forceDelete();
        });

        return redirect()->route('customers.trash', ['tab' => 'sales'])->with('success', 'Sale permanently deleted.');
    }
}
