<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransparencyReport extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['start_date', 'end_date'];

    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }
}
