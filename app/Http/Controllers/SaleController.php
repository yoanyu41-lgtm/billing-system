<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('admin.sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'nullable|numeric',
        ]);

        $sale = Sale::create([
            'customer_name' => $request->customer_name,
            'sale_date' => $request->sale_date ?? now(),
            'total' => 0,
        ]);

        $total = 0;
        foreach ($request->items as $it) {
            $si = SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $it['product_id'],
                'quantity' => $it['quantity'],
                'price' => $it['price'] ?? null,
            ]);

            $total += ($it['price'] ?? 0) * $it['quantity'];

            // create stock movement OUT
            StockMovement::create([
                'product_id' => $it['product_id'],
                'type' => 'out',
                'quantity' => $it['quantity'],
                'related_id' => $sale->id,
                'note' => 'Sale #' . $sale->id,
            ]);

            // update product stock (decrement)
            $product = Product::find($it['product_id']);
            $product->decrement('stock', $it['quantity']);
        }

        $sale->update(['total' => $total]);

        return redirect()->route('admin.products.stock')->with('success','Sale recorded and stock updated.');
    }
}
