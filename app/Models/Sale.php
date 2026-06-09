<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'customer_name',
        'customer_phone',
        'sale_date',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'note',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal'  => 'decimal:2',
        'discount'  => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate the next sequential invoice number, e.g. INV-2026-000123
     */
    public static function generateInvoiceNo(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id');
        $seq  = str_pad((string) (($last ?? 0) + 1), 6, '0', STR_PAD_LEFT);

        return "INV-{$year}-{$seq}";
    }
}
