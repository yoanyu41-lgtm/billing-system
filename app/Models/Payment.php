<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'installment_id',
        'amount',
        'penalty_amount',
        'payment_date',
        'payment_method_id',
        'status',
        'is_settlement',
        'payoff_type',
        'principal_amount',
        'interest_amount',
        'discount_amount',
        'late_fee',
        'payment_reference',
        'title',
        'interest_rate',
        'qr_image',
        'approved_by',
    ];

    protected $casts = [
        'is_settlement'    => 'boolean',
        'penalty_amount'   => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount'  => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'late_fee'         => 'decimal:2',
        'interest_rate'    => 'decimal:2',
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
