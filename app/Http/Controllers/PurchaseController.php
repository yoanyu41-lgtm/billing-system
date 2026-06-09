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

        DB::transaction(function () use ($request, &$purchase, &$total) {
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date ?? now(),
                'total' => 0,
            ]);

            $total = 0;
            foreach ($request->items as $it) {
                $pi = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $it['quantity'],
                    'cost_price' => $it['cost_price'] ?? null,
                ]);

            $total += ($it['cost_price'] ?? 0) * $it['quantity'];

            // create stock movement IN
            StockMovement::create([
                'product_id' => $it['product_id'],
                'type' => 'in',
                'quantity' => $it['quantity'],
                'supplier_id' => $request->supplier_id,
                'related_id' => $purchase->id,
                'note' => 'Purchase #' . $purchase->id,
            ]);

            // update product stock
            $product = Product::find($it['product_id']);
            $product->increment('stock', $it['quantity']);
            if ($it['cost_price']) {
                $product->update(['cost_price' => $it['cost_price']]);
            }
        }

            $purchase->update(['total' => $total]);
        });

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

        DB::transaction(function () use ($request, $purchase, &$total) {
            $total = 0;

            // reverse previous stock
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                $product->decrement('stock', $item->quantity);
            }

            $purchase->items()->delete();
            StockMovement::where('type', 'in')->where('related_id', $purchase->id)->delete();

            foreach ($request->items as $it) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $it['quantity'],
                    'cost_price' => $it['cost_price'] ?? null,
                ]);

                $total += ($it['cost_price'] ?? 0) * $it['quantity'];

                StockMovement::create([
                    'product_id' => $it['product_id'],
                    'type' => 'in',
                    'quantity' => $it['quantity'],
                    'supplier_id' => $request->supplier_id,
                    'related_id' => $purchase->id,
                    'note' => 'Purchase #' . $purchase->id,
                ]);

                $product = Product::find($it['product_id']);
                $product->increment('stock', $it['quantity']);
                if ($it['cost_price']) {
                    $product->update(['cost_price' => $it['cost_price']]);
                }
            }

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date ?? now(),
                'total' => $total,
            ]);
        });

        return redirect()->route('admin.purchases.index')->with('success','Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                $product->decrement('stock', $item->quantity);
            }
            StockMovement::where('type', 'in')->where('related_id', $purchase->id)->delete();
            $purchase->items()->delete();
            $purchase->delete();
        });

        return redirect()->route('admin.purchases.index')->with('success','Purchase deleted successfully.');
    }
}
