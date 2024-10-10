<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TransactionCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'charge_type',
        'fixed_amount',
        'percentage',
        'currency_id',
        'is_active'
    ];

    protected $casts = [
        'fixed_amount' => 'decimal:4',
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
