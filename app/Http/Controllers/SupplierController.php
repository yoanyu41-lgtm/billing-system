<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        $suppliers = $query->paginate(15)->withQueryString();

        $suggestions = [];
        $allSuppliers = Supplier::select('name', 'email', 'address')->get();
        foreach ($allSuppliers as $s) {
            if ($s->name) $suggestions[] = ['label' => $s->name, 'value' => $s->name];
            if ($s->email) $suggestions[] = ['label' => $s->email, 'value' => $s->email];
            if ($s->address) $suggestions[] = ['label' => $s->address, 'value' => $s->address];
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('admin.suppliers.index', compact('suppliers', 'suggestions'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        Supplier::create($request->only(['name','phone','email','address']));

        return redirect()->route('admin.suppliers.index')->with('success','Supplier created.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $supplier->update($request->only(['name','phone','email','address']));

        return redirect()->route('admin.suppliers.index')->with('success','Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success','Supplier deleted successfully.');
    }

    public function restore($id)
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($id);
        $supplier->restore();
        return redirect()->route('customers.trash', ['tab' => 'suppliers'])->with('success', 'Supplier restored successfully.');
    }

    public function forceDelete($id)
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($id);
        $supplier->forceDelete();
        return redirect()->route('customers.trash', ['tab' => 'suppliers'])->with('success', 'Supplier permanently deleted.');
    }
}
