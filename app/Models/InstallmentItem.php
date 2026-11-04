<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentItem extends Model
{
    protected $fillable = [
        'installment_id',
        'product_id',
        'quantity',
        'price',
        'tax_rate',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'price'      => 'decimal:2',
        'tax_rate'   => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}

