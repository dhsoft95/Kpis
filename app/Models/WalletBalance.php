<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletBalance extends Model
{
    protected $connection ='mysql_second';

    use HasFactory;
    protected $fillable = [
        'partner',
        'balance',
        'available_balance',
        'currency',
        'status',
    ];
}
