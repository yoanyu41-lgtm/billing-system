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
        $query = Sale::with(['customer', 'creator'])->withCount('items');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $sales = $query->latest()->paginate(15)->withQueryString();

        return view('admin.sales.index', compact('sales'));
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

        return view('admin.sales.create', compact('products', 'customers'));
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

        // Optionally save a walk-in buyer as a customer record (name + phone only).
        $savedNewCustomer = false;
        if (empty($validated['customer_id'])
            && $request->boolean('save_as_customer')
            && !empty($validated['customer_name'])) {

            // Avoid duplicates: reuse an existing customer with the same phone if provided.
            $existing = null;
            if (!empty($validated['customer_phone'])) {
                $existing = Customer::where('phone', $validated['customer_phone'])->first();
            }

            if ($existing) {
                $validated['customer_id'] = $existing->id;
            } else {
                $newCustomer = Customer::create([
                    'name'       => $validated['customer_name'],
                    'phone'      => $validated['customer_phone'] ?? null,
                    'created_by' => auth()->id(),
                ]);
                $validated['customer_id'] = $newCustomer->id;
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

                $subtotal = 0;
                foreach ($validated['items'] as $it) {
                    $subtotal += $it['price'] * $it['quantity'];
                }
                $discount = (float) ($validated['discount'] ?? 0);
                $total    = max($subtotal - $discount, 0);

                $sale = Sale::create([
                    'invoice_no'     => Sale::generateInvoiceNo(),
                    'customer_id'    => $validated['customer_id'] ?? null,
                    'customer_name'  => $validated['customer_name'] ?? null,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'sale_date'      => $validated['sale_date'] ?? now(),
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'total'          => $total,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'note'           => $validated['note'] ?? null,
                    'created_by'     => auth()->id(),
                ]);

                foreach ($validated['items'] as $it) {
                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $it['product_id'],
                        'quantity'   => $it['quantity'],
                        'price'      => $it['price'],
                    ]);

                    StockMovement::create([
                        'product_id' => $it['product_id'],
                        'type'       => 'out',
                        'quantity'   => $it['quantity'],
                        'related_id' => $sale->id,
                        'note'       => 'Direct Sale ' . $sale->invoice_no,
                    ]);

                    Product::where('id', $it['product_id'])->decrement('stock', $it['quantity']);
                }

                return $sale;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

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

        return view('admin.sales.show', compact('sale'));
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

            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('admin.sales.index')
            ->with('success', __('app.sale_deleted_success'));
    }
}
