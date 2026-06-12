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
        'tax_rate',
        'tax_amount',
        'subtotal_before_tax',
        'duration_months',
        'monthly_payment',
        'remaining_balance',
        'status',
        'created_by',
        'next_due_date',
        'last_reminder_sent_at',
        'signed_contract',
        'contract_signed_at',
        'contract_signed_by',
    ];

    protected $casts = [
        'contract_signed_at' => 'datetime',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal_before_tax' => 'decimal:2',
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

    /**
     * Outstanding principal balance (excludes future interest).
     * Used for early payoff (settlement) calculations.
     */
    public function outstandingPrincipal(): float
    {
        $principalBase = max($this->total_price - $this->down_payment, 0);
        $duration = max($this->duration_months, 1);
        $monthlyPrincipal = $principalBase / $duration;
        $monthlyInterest = ($principalBase * $this->interest_rate / 100) / 12;
        $amountDue = $monthlyPrincipal + $monthlyInterest;

        // Total of normal approved payments already made (exclude settlement payments).
        $paid = $this->payments()
            ->where('status', 'approved')
            ->where('is_settlement', false)
            ->sum('amount');

        // How many monthly instalments those payments fully cover.
        $paidMonths = $amountDue > 0 ? floor($paid / $amountDue) : 0;
        $paidMonths = min($paidMonths, $duration);

        $principalPaid = $monthlyPrincipal * $paidMonths;

        return round(max($principalBase - $principalPaid, 0), 2);
    }
}
