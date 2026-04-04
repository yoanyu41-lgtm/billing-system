<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramLog extends Model
{
    protected $fillable = [
        'customer_id',
        'message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
