<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function collaterals()
    {
        return $this->hasMany(Collateral::class);
    }

    public function foreclosures()
    {
        return $this->hasMany(Foreclosure::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function telegramUser()
    {
        return $this->hasOne(TelegramUser::class);
    }
}
