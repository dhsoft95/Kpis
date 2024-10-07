<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\PercentageAdjustment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CurrencyRatesSeeder extends Seeder
{
    private $exchangeRates = [
        'USD' => 1,
        'EUR' => 0.92,
        'GBP' => 0.79,
        'JPY' => 149.19,
        'CHF' => 0.89,
        'CAD' => 1.36,
        'AUD' => 1.53,
        'ZAR' => 18.74,
        'KES' => 135.50,
        'UGX' => 3741.76,
        'RWF' => 1187.23,
        'BIF' => 2827.90,
        'TZS' => 2732.24,
        'NGN' => 460.51,
        'GHS' => 11.84,
        'EGP' => 30.90,
        'MAD' => 10.08,
        'XOF' => 603.79,
        'XAF' => 603.79,
        'MZN' => 63.37,
        'ZMW' => 19.28,
        'BWP' => 13.64,
        'MUR' => 44.39,
    ];

    private $governmentTaxes = [
        'USD' => 0.5,  // 0.5% tax for USD transactions
        'EUR' => 0.3,  // 0.3% tax for EUR transactions
        'GBP' => 0.4,  // 0.4% tax for GBP transactions
        'JPY' => 0.6,  // 0.6% tax for JPY transactions
        'default' => 0.2,  // Default tax rate for countries not specified
    ];

    public function run(): void
    {
        $this->seedCurrencies();
        $this->seedPercentageAdjustments();
        $this->seedExchangeRates();
    }

    private function seedCurrencies(): void
    {
        foreach ($this->exchangeRates as $code => $rate) {
            Currency::firstOrCreate(['code' => $code], ['name' => $this->getCurrencyName($code)]);
        }
        Log::info('Currencies seeded successfully.');
    }

    private function seedPercentageAdjustments(): void
    {
        // Create default SML adjustment
        PercentageAdjustment::firstOrCreate(
            ['name' => 'Default SML Adjustment', 'is_government_tax' => false],
            ['percentage' => 2.00]
        );

        // Create government tax adjustments for each currency
        $currencies = Currency::all();
        foreach ($currencies as $currency) {
            PercentageAdjustment::firstOrCreate(
                ['name' => "{$currency->code} Government Tax", 'is_government_tax' => true, 'currency_code' => $currency->code],
                ['percentage' => $this->getGovernmentTax($currency->code)]
            );
        }
        Log::info('Percentage adjustments seeded successfully.');
    }

    private function seedExchangeRates()
    {
        $currencies = Currency::all();
        $defaultSmlAdjustment = PercentageAdjustment::where('name', 'Default SML Adjustment')->firstOrFail();

        foreach ($currencies as $baseCurrency) {
            foreach ($currencies as $targetCurrency) {
                if ($baseCurrency->id !== $targetCurrency->id) {
                    try {
                        $rate = $this->calculateRate($baseCurrency->code, $targetCurrency->code);
                        $governmentTax = PercentageAdjustment::where('is_government_tax', true)
                            ->where('currency_code', $targetCurrency->code)
                            ->firstOrFail();

                        $exchangeRate = ExchangeRate::updateOrCreate(
                            [
                                'base_currency_id' => $baseCurrency->id,
                                'target_currency_id' => $targetCurrency->id,
                            ],
                            [
                                'global_rate' => $rate,
                                'is_flat_adjustment' => false,
                                'sml_adjustment_id' => $defaultSmlAdjustment->id,
                                'government_tax_id' => $governmentTax->id,
                                'effective_date' => Carbon::now(),
                            ]
                        );

                        $exchangeRate->calculateSmlRate();
                        $exchangeRate->save();

                        Log::info("Created/Updated rate: {$baseCurrency->code} to {$targetCurrency->code}", [
                            'rate' => $rate,
                            'sml_rate' => $exchangeRate->sml_rate,
                            'tax_percentage' => $governmentTax->percentage,
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Failed to create/update rate for {$baseCurrency->code} to {$targetCurrency->code}: " . $e->getMessage());
                    }
                }
            }
        }
        Log::info('Exchange rates seeded successfully.');
    }

    private function calculateRate($from, $to): float|int
    {
        if (!isset($this->exchangeRates[$from]) || !isset($this->exchangeRates[$to])) {
            throw new \Exception("Exchange rate not available for {$from} or {$to}");
        }
        $usdToFrom = $this->exchangeRates[$from];
        $usdToTo = $this->exchangeRates[$to];
        return $usdToTo / $usdToFrom;
    }

    private function getGovernmentTax($currencyCode): float
    {
        return $this->governmentTaxes[$currencyCode] ?? $this->governmentTaxes['default'];
    }

    private function getCurrencyName($code): string
    {
        $names = [
            'USD' => 'United States Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound Sterling',
            'JPY' => 'Japanese Yen',
            'CHF' => 'Swiss Franc',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'ZAR' => 'South African Rand',
            'KES' => 'Kenyan Shilling',
            'UGX' => 'Ugandan Shilling',
            'RWF' => 'Rwandan Franc',
            'BIF' => 'Burundian Franc',
            'TZS' => 'Tanzanian Shilling',
            'NGN' => 'Nigerian Naira',
            'GHS' => 'Ghanaian Cedi',
            'EGP' => 'Egyptian Pound',
            'MAD' => 'Moroccan Dirham',
            'XOF' => 'West African CFA Franc',
            'XAF' => 'Central African CFA Franc',
            'MZN' => 'Mozambican Metical',
            'ZMW' => 'Zambian Kwacha',
            'BWP' => 'Botswanan Pula',
            'MUR' => 'Mauritian Rupee',
        ];

        return $names[$code] ?? "Unknown Currency ({$code})";
    }
}
