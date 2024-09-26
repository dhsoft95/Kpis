<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CurrencyExchangeService
{
    private string $apiKey;
    private string $baseUrl;
    private string $baseCurrency;
    private ?float $usdDeterminant = null;

    public function __construct()
    {
        $this->apiKey = Config::get('services.currency_api.api_key');
        $this->baseUrl = 'https://api.currencyapi.com/v3';
        $this->baseCurrency = Config::get('services.currency_api.base_currency', 'TZS');
    }

    /**
     * Get the USD determinant from the database.
     *
     * @return float
     * @throws \Exception
     */
    private function getUsdDeterminant(): float
    {
        if ($this->usdDeterminant === null) {
            try {
                $result = DB::connection('mysql_second')->table('currency_settings')
                    ->where('key', 'usd_determinant')
                    ->select('value')
                    ->first();

                if ($result && is_numeric($result->value)) {
                    $this->usdDeterminant = (float) $result->value;
                } else {
                    throw new \Exception('USD determinant not found or invalid in database.');
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch USD determinant: ' . $e->getMessage());
                throw new \Exception('Failed to fetch USD determinant. Please try again later.');
            }
        }
        return $this->usdDeterminant;
    }

    /**
     * Fetch and cache currency exchange rates.
     *
     * @return array
     * @throws \Exception
     */
    public function getRates(): array
    {
        return Cache::remember('currency_rates', 3600, function () {
            try {
                $response = Http::withHeaders(['apikey' => $this->apiKey])
                    ->get("{$this->baseUrl}/latest");

                $response->throw();
                $data = $response->json();
                $rates = $this->processRates($data['data']);

                Log::info('Fetched currency rates', $rates);

                return $rates;
            } catch (\Exception $e) {
                Log::error('Failed to fetch currency rates: ' . $e->getMessage());
                throw new \Exception('Failed to fetch currency rates. Please try again later.');
            }
        });
    }

    /**
     * Process and calculate currency rates.
     *
     * @param array $rawRates
     * @return array
     */
    private function processRates(array $rawRates): array
    {
        $rates = [];

        foreach ($rawRates as $currency => $info) {
            $rates["USD_{$currency}"] = $info['value'];
            $rates["{$currency}_USD"] = 1 / $info['value'];
        }

        // Calculate cross rates
        foreach ($rates as $pair => $rate) {
            [$from, $to] = explode('_', $pair);
            if ($from !== 'USD' && $to !== 'USD') {
                $rates["{$from}_{$to}"] = $rates["{$from}_USD"] * $rates["USD_{$to}"];
            }
        }

        return $rates;
    }

    /**
     * Convert an amount from the base currency to a target currency.
     *
     * @param float $amount
     * @param string $toCurrency
     * @return array
     * @throws \Exception
     */
    public function convertFromBase(float $amount, string $toCurrency): array
    {
        try {
            $rates = $this->getRates();
            $usdDeterminant = $this->getUsdDeterminant();

            $baseToUsdRate = $rates["{$this->baseCurrency}_USD"] ?? null;
            $usdToDestRate = $rates["USD_{$toCurrency}"] ?? null;

            $this->validateRates($baseToUsdRate, $usdToDestRate, $toCurrency);

            $usdEquivalent = $amount * $baseToUsdRate;
            $adjustedUsdAmount = $usdEquivalent * $usdDeterminant;
            $finalAmount = $adjustedUsdAmount * $usdToDestRate;

            $this->logConversionDetails($amount, $toCurrency, $baseToUsdRate, $usdEquivalent, $adjustedUsdAmount, $usdToDestRate, $finalAmount);

            return [
                'usdEquivalent' => $usdEquivalent,
                'adjustedUsdAmount' => $adjustedUsdAmount,
                'finalAmount' => $finalAmount,
                'baseToUsdRate' => $baseToUsdRate,
                'usdToDestRate' => $usdToDestRate
            ];
        } catch (\Exception $e) {
            Log::error('Conversion failed: ' . $e->getMessage());
            throw new \Exception('Currency conversion failed. Please try again later.');
        }
    }

    /**
     * Validate the exchange rates.
     *
     * @param float|null $baseToUsdRate
     * @param float|null $usdToDestRate
     * @param string $toCurrency
     * @throws \Exception
     */
    private function validateRates(?float $baseToUsdRate, ?float $usdToDestRate, string $toCurrency): void
    {
        if ($baseToUsdRate === null) {
            throw new \Exception("Exchange rate not available for {$this->baseCurrency} to USD");
        }
        if ($usdToDestRate === null) {
            throw new \Exception("Exchange rate not available for USD to {$toCurrency}");
        }
    }

    /**
     * Log conversion details for debugging.
     *
     * @param float $amount
     * @param string $toCurrency
     * @param float $baseToUsdRate
     * @param float $usdEquivalent
     * @param float $adjustedUsdAmount
     * @param float $usdToDestRate
     * @param float $finalAmount
     */
    private function logConversionDetails(
        float $amount,
        string $toCurrency,
        float $baseToUsdRate,
        float $usdEquivalent,
        float $adjustedUsdAmount,
        float $usdToDestRate,
        float $finalAmount
    ): void {
        Log::info('Conversion details', [
            'amount' => $amount,
            'toCurrency' => $toCurrency,
            'baseToUsdRate' => $baseToUsdRate,
            'usdEquivalent' => $usdEquivalent,
            'adjustedUsdAmount' => $adjustedUsdAmount,
            'usdToDestRate' => $usdToDestRate,
            'finalAmount' => $finalAmount
        ]);
    }

    /**
     * Generate a quotation for currency exchange from base currency.
     *
     * @param float $amount
     * @param string $toCurrency
     * @return array
     * @throws \Exception
     */
    public function exchangeFromBase(float $amount, string $toCurrency): array
    {
        $conversionResult = $this->convertFromBase($amount, $toCurrency);
        $usdDeterminant = $this->getUsdDeterminant();

        return [
            'quotation' => [
                'timestamp' => now()->toIso8601String(),
                'reference' => 'QUO-' . now()->format('Ymd-His'),
                'description' => 'Currency Exchange Quotation',
                'exchange_details' => $this->getExchangeDetails($amount, $toCurrency, $conversionResult),
                'exchange_rates' => $this->getExchangeRates($amount, $toCurrency, $conversionResult),
                'usd_determinant' => $usdDeterminant
            ]
        ];
    }

    /**
     * Get exchange details for the quotation.
     *
     * @param float $amount
     * @param string $toCurrency
     * @param array $conversionResult
     * @return array
     */
    private function getExchangeDetails(float $amount, string $toCurrency, array $conversionResult): array
    {
        return [
            'source' => [
                'amount' => $amount,
                'currency' => $this->baseCurrency,
                'description' => $this->getCurrencyDescription($this->baseCurrency)
            ],
            'usd_equivalent' => [
                'amount' => round($conversionResult['usdEquivalent'], 2),
                'currency' => 'USD',
                'description' => 'USD equivalent before applying determinant'
            ],
            'adjusted_usd' => [
                'amount' => round($conversionResult['adjustedUsdAmount'], 3),
                'currency' => 'USD',
                'description' => 'USD amount after applying determinant'
            ],
            'destination' => [
                'amount' => round($conversionResult['finalAmount'], 2),
                'currency' => $toCurrency,
                'description' => $this->getCurrencyDescription($toCurrency)
            ]
        ];
    }

    /**
     * Get exchange rates for the quotation.
     *
     * @param float $amount
     * @param string $toCurrency
     * @param array $conversionResult
     * @return array
     */
    private function getExchangeRates(float $amount, string $toCurrency, array $conversionResult): array
    {
        return [
            "{$this->baseCurrency}_USD" => round($conversionResult['baseToUsdRate'], 6),
            "USD_{$toCurrency}" => round($conversionResult['usdToDestRate'], 6),
            'effective_rate' => round($conversionResult['finalAmount'] / $amount, 6),
        ];
    }

    /**
     * Get the description for a currency code.
     *
     * @param string $currencyCode
     * @return string
     */
    private function getCurrencyDescription(string $currencyCode): string
    {
        return Config::get("currencies.{$currencyCode}", 'Unknown Currency');
    }

    /**
     * Get supported currencies.
     *
     * @return array
     * @throws \Exception
     */
    public function getSupportedCurrencies(): array
    {
        return Cache::remember('supported_currencies', 86400, function () {
            try {
                $response = Http::withHeaders(['apikey' => $this->apiKey])
                    ->get("{$this->baseUrl}/currencies");

                $response->throw();

                $currencies = $response->json()['data'];
                return array_keys($currencies);
            } catch (\Exception $e) {
                Log::error('Failed to fetch supported currencies: ' . $e->getMessage());
                throw new \Exception('Failed to fetch supported currencies. Please try again later.');
            }
        });
    }
}
