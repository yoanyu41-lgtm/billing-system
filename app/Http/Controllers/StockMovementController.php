<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product','supplier']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search by Product name or code
                $q->whereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                })
                // Search by Supplier name
                ->orWhereHas('supplier', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                // Search by note
                ->orWhere('note', 'like', "%{$search}%");
            });
        }
        
        // Filter by type (in/out)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $movements = $query->orderBy('created_at','desc')->paginate(25)->withQueryString();

        $suggestions = [];
        $movementsAll = StockMovement::with(['product', 'supplier'])->get();
        foreach ($movementsAll as $m) {
            if ($m->product) {
                if ($m->product->name) $suggestions[] = ['label' => $m->product->name, 'value' => $m->product->name];
                if ($m->product->code) $suggestions[] = ['label' => $m->product->code . ' - ' . $m->product->name, 'value' => $m->product->code];
            }
            if ($m->supplier && $m->supplier->name) {
                $suggestions[] = ['label' => $m->supplier->name, 'value' => $m->supplier->name];
            }
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('admin.stock_movements.index', compact('movements', 'suggestions'));
    }

    public function destroy(StockMovement $stockMovement)
    {
        $user = auth()->user();
        $isAdmin = strtolower($user->role ?? '') === 'admin' 
            || (method_exists($user, 'hasRole') && ($user->hasRole('Admin') || $user->hasRole('admin')));

        if (!$isAdmin) {
            abort(403, 'Only administrators can delete stock movement records.');
        }

        $stockMovement->delete();

        return redirect()->back()->with('success', app()->getLocale() === 'km' 
            ? 'បានលុបប្រវត្តិស្តុកជោគជ័យ។' 
            : 'Stock movement record deleted successfully.');
    }
}
