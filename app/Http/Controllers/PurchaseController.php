<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier','items.product']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search by Purchase ID
                $q->where('id', 'like', "%{$search}%")
                  // Search by Supplier name
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }
        
        $purchases = $query->latest()->paginate(15)->withQueryString();
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create(\Illuminate\Http\Request $request)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $selectedProductId = $request->query('product_id');
        return view('admin.purchases.create', compact('suppliers','products','selectedProductId'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('admin.purchases.show', compact('purchase'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, &$purchase) {
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date ?? now(),
                'total' => 0,
                'tax_amount' => 0,
            ]);

            $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
            $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);

            $subtotalBeforeTax = 0;
            $totalTaxAmount = 0;

            foreach ($request->items as $it) {
                $product = Product::find($it['product_id']);
                $costPrice = $it['cost_price'] ?? 0;
                $quantity = $it['quantity'];
                $itemTotal = $costPrice * $quantity;

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

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $quantity,
                    'cost_price' => $costPrice,
                    'tax_rate' => $itemTaxRate,
                    'tax_amount' => $itemTaxAmount,
                ]);

                $subtotalBeforeTax += $itemSubtotal;
                $totalTaxAmount += $itemTaxAmount;

                // create stock movement IN
                StockMovement::create([
                    'product_id' => $it['product_id'],
                    'type' => 'in',
                    'quantity' => $quantity,
                    'supplier_id' => $request->supplier_id,
                    'related_id' => $purchase->id,
                    'note' => 'Purchase #' . $purchase->id,
                ]);

                // update product stock
                $product->increment('stock', $quantity);
                if ($costPrice > 0) {
                    $product->update(['cost_price' => $costPrice]);
                }
            }

            $purchase->update([
                'total' => $subtotalBeforeTax + $totalTaxAmount,
                'tax_amount' => $totalTaxAmount,
            ]);
        });

        $purchase->load('supplier');
        \App\Models\Notification::createSystemNotification(
            'purchase', 'New Purchase Recorded',
            'New purchase recorded from supplier: ' . ($purchase->supplier ? $purchase->supplier->name : 'Unknown') . '. Total: $' . number_format($purchase->total, 2),
            'truck', 'purple',
            route('admin.purchases.index')
        );

        return redirect()->route('admin.products.stock')->with('success','Purchase recorded and stock updated.');
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchase->load('items');
        return view('admin.purchases.edit', compact('purchase','suppliers','products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            // reverse previous stock
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                $product->decrement('stock', $item->quantity);
            }

            $purchase->items()->delete();
            StockMovement::where('type', 'in')->where('related_id', $purchase->id)->delete();

            $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
            $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0);

            $subtotalBeforeTax = 0;
            $totalTaxAmount = 0;

            foreach ($request->items as $it) {
                $product = Product::find($it['product_id']);
                $costPrice = $it['cost_price'] ?? 0;
                $quantity = $it['quantity'];
                $itemTotal = $costPrice * $quantity;

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

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $quantity,
                    'cost_price' => $costPrice,
                    'tax_rate' => $itemTaxRate,
                    'tax_amount' => $itemTaxAmount,
                ]);

                $subtotalBeforeTax += $itemSubtotal;
                $totalTaxAmount += $itemTaxAmount;

                StockMovement::create([
                    'product_id' => $it['product_id'],
                    'type' => 'in',
                    'quantity' => $quantity,
                    'supplier_id' => $request->supplier_id,
                    'related_id' => $purchase->id,
                    'note' => 'Purchase #' . $purchase->id,
                ]);

                $product->increment('stock', $quantity);
                if ($costPrice > 0) {
                    $product->update(['cost_price' => $costPrice]);
                }
            }

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date ?? now(),
                'total' => $subtotalBeforeTax + $totalTaxAmount,
                'tax_amount' => $totalTaxAmount,
            ]);
        });

        return redirect()->route('admin.purchases.index')->with('success','Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decrement('stock', $item->quantity);
                    $product->checkLowStockAlert();
                }
            }
            StockMovement::where('type', 'in')->where('related_id', $purchase->id)->delete();
            $purchase->items()->delete();
            $purchase->delete();
        });

        return redirect()->route('admin.purchases.index')->with('success','Purchase deleted successfully.');
    }
}
