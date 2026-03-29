<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'telegram_id',
        'address',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function telegramLogs()
    {
        return $this->hasMany(TelegramLog::class);
    }
}
