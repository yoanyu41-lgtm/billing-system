<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'price',
        'cost_price',
        'stock',
        'low_stock_threshold',
        'category',
        'brand',
        'supplier_id',
        'model',
        'cpu',
        'ram',
        'storage',
        'graphics_card',
        'color',
        'warranty',
        'condition',
        'is_active',
        'image',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Get the purchase history for this product (each purchase with supplier and cost price).
     */
    public function purchaseHistory()
    {
        return $this->purchaseItems()
            ->whereHas('purchase')
            ->with('purchase.supplier')
            ->get()
            ->sortByDesc(fn ($item) => optional($item->purchase)->purchase_date)
            ->values();
    }

    /**
     * Get all suppliers that have supplied this product through stock-in movements.
     */
    public function suppliers()
    {
        return Supplier::whereIn('id', function ($query) {
            $query->select('supplier_id')
                ->from('stock_movements')
                ->where('product_id', $this->id)
                ->where('type', 'in')
                ->whereNotNull('supplier_id');
        })->orderBy('name')->get();
    }
}
