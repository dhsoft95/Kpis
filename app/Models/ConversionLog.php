<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversionLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_currency_id', 'to_currency_id', 'amount', 'converted_amount',
        'used_rate', 'rate_type', 'conversion_date', 'user_id'
    ];

    protected $casts = [
        'conversion_date' => 'datetime',
        'amount' => 'float',
        'converted_amount' => 'float',
        'used_rate' => 'float',
    ];

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
