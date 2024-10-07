<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\ConversionLog;
use App\Models\PercentageAdjustment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CurrencyExchangeService
{
    private const BASE_CURRENCY = 'TZS';
    private const INTERMEDIATE_CURRENCY = 'USD';

    public function convert(float $amount, string $toCurrency): array
    {
        try {
            // Get the government tax for TZS
            $tzsGovTax = $this->getGovernmentTax(self::BASE_CURRENCY);

            // Calculate total amount with tax
            $totalAmountWithTax = $amount * (1 + $tzsGovTax / 100);

            $tzsToUsdRate = $this->getExchangeRate(self::BASE_CURRENCY, self::INTERMEDIATE_CURRENCY);
            $tzsToUsdRate['rate'] = $tzsToUsdRate['rate'] < 1 ? 1 / $tzsToUsdRate['rate'] : $tzsToUsdRate['rate'];
            $usdAmount = $totalAmountWithTax / $tzsToUsdRate['rate'];

            if ($toCurrency !== self::INTERMEDIATE_CURRENCY) {
                $usdToTargetRate = $this->getExchangeRate(self::INTERMEDIATE_CURRENCY, $toCurrency);
                $usdToTargetRate['rate'] = $usdToTargetRate['rate'] < 1 ? 1 / $usdToTargetRate['rate'] : $usdToTargetRate['rate'];
                $targetAmount = $usdAmount * $usdToTargetRate['rate'];
                $effectiveRate = $tzsToUsdRate['rate'] / $usdToTargetRate['rate'];
            } else {
                $targetAmount = $usdAmount;
                $effectiveRate = $tzsToUsdRate['rate'];
            }

            $this->logConversion($totalAmountWithTax, self::BASE_CURRENCY, $toCurrency, $targetAmount, 1 / $effectiveRate);

            $response = [
                "status" => "success",
                "data" => [
                    "entered_amount" => $amount,
                    "currency" => self::BASE_CURRENCY,
                    "government_tax_percentage" => round($tzsGovTax, 2),
                    "total_amount_with_tax" => round($totalAmountWithTax, 2),
                    "results" => [
                        "change_to_usd" => [
                            "amount" => round($usdAmount, 2),
                            "currency" => "USD"
                        ],
                        "final_conversion" => [
                            "amount" => round($targetAmount, 2),
                            "currency" => $toCurrency
                        ]
                    ],
                    "exchange_rates" => [
                        self::BASE_CURRENCY . "_USD" => [
                            "TZS_per_1_USD" => round($tzsToUsdRate['rate'], 4),
                            "USD_per_1_TZS" => round(1 / $tzsToUsdRate['rate'], 6),
                        ],
                    ],
                    "metadata" => [
                        "timestamp" => Carbon::now()->toIso8601String(),
                        "source" => "Simba Money Limited"
                    ]
                ]
            ];

            if ($toCurrency !== self::INTERMEDIATE_CURRENCY) {
                $response['data']['exchange_rates'][self::BASE_CURRENCY . "_{$toCurrency}"] = [
                    self::BASE_CURRENCY . "_per_1_{$toCurrency}" => round($effectiveRate, 4),
                    "{$toCurrency}_per_1_" . self::BASE_CURRENCY => round(1 / $effectiveRate, 6),
                ];
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Conversion failed: ' . $e->getMessage());
            return [
                "status" => "error",
                "message" => "Currency conversion failed: " . $e->getMessage()
            ];
        }
    }

    private function getExchangeRate(string $fromCurrency, string $toCurrency): array
    {
        return Cache::remember("{$fromCurrency}_to_{$toCurrency}_rate", 3600, function () use ($fromCurrency, $toCurrency) {
            $baseCurrency = Currency::where('code', $fromCurrency)->firstOrFail();
            $targetCurrency = Currency::where('code', $toCurrency)->firstOrFail();

            $rate = ExchangeRate::where('base_currency_id', $baseCurrency->id)
                ->where('target_currency_id', $targetCurrency->id)
                ->where('effective_date', '<=', Carbon::now())
                ->orderBy('effective_date', 'desc')
                ->firstOrFail();

            return [
                'rate' => $rate->sml_rate,
                'tax' => 0  // We're not applying tax here anymore
            ];
        });
    }

    private function getGovernmentTax(string $currencyCode): float
    {
        return Cache::remember("government_tax_{$currencyCode}", 3600, function () use ($currencyCode) {
            $taxAdjustment = PercentageAdjustment::where('is_government_tax', true)
                ->where('currency_code', $currencyCode)
                ->first();

            return $taxAdjustment ? $taxAdjustment->percentage : 0;
        });
    }

    private function logConversion(float $amount, string $fromCurrency, string $toCurrency, float $convertedAmount, float $usedRate): void
    {
        ConversionLog::create([
            'from_currency_id' => Currency::where('code', $fromCurrency)->first()->id,
            'to_currency_id' => Currency::where('code', $toCurrency)->first()->id,
            'amount' => $amount,
            'converted_amount' => $convertedAmount,
            'used_rate' => $usedRate,
            'rate_type' => 'SML',
            'conversion_date' => Carbon::now(),
            'user_id' => auth()->id() ?? null,
        ]);
    }
}
