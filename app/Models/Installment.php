<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'total_price',
        'down_payment',
        'interest_rate',
        'duration_months',
        'monthly_payment',
        'remaining_balance',
        'status',
        'created_by',
        'next_due_date',
        'last_reminder_sent_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
