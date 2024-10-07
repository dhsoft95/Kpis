<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExchangeRate;
use App\Models\Currency;

class CheckExchangeRatesCommand extends Command
{
    protected $signature = 'exchange-rates:check';
    protected $description = 'Check exchange rates in the database';

    public function handle()
    {
        $tzsToUsdRate = ExchangeRate::whereHas('baseCurrency', function ($query) {
            $query->where('code', 'TZS');
        })
            ->whereHas('targetCurrency', function ($query) {
                $query->where('code', 'USD');
            })
            ->latest('effective_date')
            ->first();

        if ($tzsToUsdRate) {
            $this->info("TZS to USD rate found:");
            $this->info("Global rate: " . $tzsToUsdRate->global_rate);
            $this->info("SML rate: " . $tzsToUsdRate->sml_rate);
            $this->info("Effective date: " . $tzsToUsdRate->effective_date);
        } else {
            $this->error("TZS to USD rate not found in the database.");
        }

        $allRates = ExchangeRate::with(['baseCurrency', 'targetCurrency'])->get();
        $this->info("\nAll exchange rates:");
        foreach ($allRates as $rate) {
            $this->line("{$rate->baseCurrency->code} to {$rate->targetCurrency->code}: {$rate->sml_rate}");
        }
    }
}
