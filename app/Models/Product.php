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
        'category',
        'brand',
        'model',
        'image',
        'description',
    ];

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
