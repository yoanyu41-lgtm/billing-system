<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'installment_id',
        'amount',
        'payment_date',
        'status',
        'qr_image',
        'approved_by',
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
