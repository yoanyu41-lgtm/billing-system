<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'payment_id',
        'invoice_number',
        'pdf_path',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
