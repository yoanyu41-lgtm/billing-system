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
        return view('admin.stock_movements.index', compact('movements'));
    }
}
