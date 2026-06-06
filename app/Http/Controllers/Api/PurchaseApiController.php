<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseApiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Purchase::with(['supplier', 'items'])->get()]);
    }

    public function show(Purchase $purchase)
    {
        return response()->json(['data' => $purchase->load(['supplier', 'items'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.cost'       => 'required|numeric|min:0',
        ]);

        $purchase = DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum(fn($i) => $i['qty'] * $i['cost']);

            $purchase = Purchase::create([
                'supplier_id'   => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'total'         => $total,
            ]);

            foreach ($data['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $item['product_id'],
                    'qty'         => $item['qty'],
                    'cost'        => $item['cost'],
                ]);
            }

            return $purchase;
        });

        return response()->json(['data' => $purchase->load(['supplier', 'items'])], 201);
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->items()->delete();
        $purchase->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
