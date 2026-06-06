<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleApiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Sale::with('items')->get()]);
    }

    public function show(Sale $sale)
    {
        return response()->json(['data' => $sale->load('items')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'sale_date'     => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        $sale = DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum(fn($i) => $i['qty'] * $i['price']);

            $sale = Sale::create([
                'customer_name' => $data['customer_name'],
                'sale_date'     => $data['sale_date'],
                'total'         => $total,
            ]);

            foreach ($data['items'] as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'price'      => $item['price'],
                ]);
            }

            return $sale;
        });

        return response()->json(['data' => $sale->load('items')], 201);
    }

    public function destroy(Sale $sale)
    {
        $sale->items()->delete();
        $sale->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
