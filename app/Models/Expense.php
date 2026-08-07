<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category',
        'description',
        'amount',
        'expense_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
