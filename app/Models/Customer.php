<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'gender',
        'dob',
        'id_card',
        'photo',
        'id_card_photo',
        'family_photo',
        'income_proof',
        'guarantor_doc',
        'telegram_id',
        'address',
        'created_by',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function guarantors()
    {
        return $this->hasMany(Guarantor::class);
    }

    public function creditChecks()
    {
        return $this->hasMany(CustomerCreditCheck::class);
    }

    public function latestCreditCheck()
    {
        return $this->hasOne(CustomerCreditCheck::class)->latestOfMany();
    }

    public function telegramLogs()
    {
        return $this->hasMany(TelegramLog::class);
    }

    public function getAgeAttribute()
    {
        return $this->dob ? $this->dob->age : null;
    }
}
