<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock',
        'description',
    ];

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
