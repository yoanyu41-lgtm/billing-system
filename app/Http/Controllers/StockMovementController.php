<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['product','supplier'])->orderBy('created_at','desc')->paginate(25);
        return view('admin.stock_movements.index', compact('movements'));
    }
}
