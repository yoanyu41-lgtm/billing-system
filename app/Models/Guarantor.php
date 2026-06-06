<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    protected $fillable = [
        'customer_id', 'name', 'phone', 'gender', 'dob', 'id_card',
        'relationship', 'address', 'occupation', 'monthly_income',
        'photo', 'id_card_photo', 'notes',
    ];

    protected $casts = ['dob' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
