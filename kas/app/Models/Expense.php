<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['expense_date'];
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receipts()
    {
        return $this->hasMany(ExpenseReceipt::class);
    }
}
