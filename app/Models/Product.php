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
        'is_taxable',
        'tax_rate',
        'tax_type',
        'image',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_taxable' => 'boolean',
        'tax_rate' => 'decimal:2',
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

    public function checkLowStockAlert()
    {
        $threshold = $this->low_stock_threshold ?? 5;
        if ($this->stock <= $threshold) {
            $alreadyNotified = Notification::where('type', 'low_stock')
                ->where('link', route('admin.products.show', $this->id))
                ->where('is_read', false)
                ->exists();

            if (!$alreadyNotified) {
                Notification::createSystemNotification(
                    'low_stock',
                    'Low Stock Alert',
                    'Product ' . $this->name . ' is running low on stock. Current stock: ' . $this->stock,
                    'archive',
                    'orange',
                    route('admin.products.show', $this->id)
                );
            }
        }
    }
}
