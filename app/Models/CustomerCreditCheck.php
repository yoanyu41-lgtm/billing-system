<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCreditCheck extends Model
{
    protected $fillable = [
        'customer_id', 'checked_by', 'employment_status', 'monthly_income',
        'existing_debt', 'credit_score', 'risk_level', 'status', 'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function getRiskColorAttribute(): string
    {
        return match($this->risk_level) {
            'low'    => 'emerald',
            'medium' => 'amber',
            'high'   => 'red',
            default  => 'gray',
        };
    }
}
