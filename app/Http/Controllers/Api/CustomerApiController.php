<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Customer::all()]);
    }

    public function show(Customer $customer)
    {
        return response()->json(['data' => $customer]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'telegram_id' => 'nullable|string|max:100',
            'address'     => 'nullable|string',
        ]);

        $data['created_by'] = $request->user()->id;
        $customer = Customer::create($data);

        return response()->json(['data' => $customer], 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'telegram_id' => 'nullable|string|max:100',
            'address'     => 'nullable|string',
        ]);

        $customer->update($data);

        return response()->json(['data' => $customer]);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
