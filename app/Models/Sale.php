<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['customer_name', 'sale_date', 'total'];

    protected $casts = [
        'sale_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
