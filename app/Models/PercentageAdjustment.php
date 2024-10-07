<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageAdjustment extends Model
{ use HasFactory;

    protected $fillable = [
        'name',
        'percentage',
        'is_government_tax',
        'currency_code'
    ];

    protected $casts = [
        'percentage' => 'float',
        'is_government_tax' => 'boolean',
    ];
}
