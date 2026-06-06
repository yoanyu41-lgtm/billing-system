<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Product::all()]);
    }

    public function show(Product $product)
    {
        return response()->json(['data' => $product]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'nullable|string|max:100',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'nullable|string|max:100',
            'brand'       => 'nullable|string|max:100',
            'model'       => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($data);

        return response()->json(['data' => $product], 201);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'code'        => 'nullable|string|max:100',
            'name'        => 'sometimes|required|string|max:255',
            'price'       => 'sometimes|required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'category'    => 'nullable|string|max:100',
            'brand'       => 'nullable|string|max:100',
            'model'       => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $product->update($data);

        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
