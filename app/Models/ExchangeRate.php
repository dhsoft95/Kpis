<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExchangeRate Model
 *
 * This model represents an exchange rate between two currencies,
 * including adjustments for SML (Special Market Level) rates.
 */
class ExchangeRate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'base_currency_id',
        'target_currency_id',
        'global_rate',
        'sml_rate',
        'is_flat_adjustment',
        'sml_adjustment_id',
        'government_tax_id',
        'effective_date'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'global_rate' => 'float',
        'sml_rate' => 'float',
        'is_flat_adjustment' => 'boolean',
    ];


    /**
     * The "booted" method of the model.
     *
     * This method is called when the model is booted and sets up
     * event listeners for the creating and updating events.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($exchangeRate) {
            $exchangeRate->calculateSmlRate();
        });

        static::updating(function ($exchangeRate) {
            $exchangeRate->calculateSmlRate();
        });
    }


    /**
     * Calculate the SML rate based on the adjustment type and value.
     *
     * If it's a flat adjustment, SML rate equals the global rate.
     * If it's a percentage adjustment, SML rate is calculated using
     * the associated percentage adjustment.
     *
     * @return void
     */

    public function calculateSmlRate()
    {
        if ($this->is_flat_adjustment) {
            $this->sml_rate = $this->global_rate;
        } else {
            $smlPercentage = $this->smlAdjustment->percentage ?? 0;
            $this->sml_rate = $this->global_rate * (1 + $smlPercentage / 100);
        }

        // Apply government tax
        $taxPercentage = $this->governmentTax->percentage ?? 0;
        $this->sml_rate *= (1 + $taxPercentage / 100);
    }


    /**
     * Get the base currency associated with the exchange rate.
     *
     * @return BelongsTo
     */
    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    /**
     * Get the target currency associated with the exchange rate.
     *
     * @return BelongsTo
     */
    public function targetCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'target_currency_id');
    }

    /**
     * Get the percentage adjustment associated with the exchange rate.
     *
     * @return BelongsTo
     */


    public function smlAdjustment(): BelongsTo
    {
        return $this->belongsTo(PercentageAdjustment::class, 'sml_adjustment_id');
    }

    public function governmentTax(): BelongsTo
    {
        return $this->belongsTo(PercentageAdjustment::class, 'government_tax_id');
    }
}
