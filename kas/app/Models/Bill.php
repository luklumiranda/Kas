<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['due_date', 'paid_date'];
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function scopePending($query)
    {
        return $query->whereNull('paid_date');
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('paid_date');
    }

    public function scopeOverdue($query)
    {
        return $query->pending()->where('due_date', '<', now());
    }

    public function isPaid()
    {
        return !is_null($this->paid_date);
    }

    public function isOverdue()
    {
        return !$this->isPaid() && $this->due_date < now();
    }

    public function getRemainingAmount()
    {
        return $this->amount - ($this->paid_amount ?? 0);
    }
}
