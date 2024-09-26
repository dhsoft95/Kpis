<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BasedOnDateOverview extends Widget
{
    protected static string $view = 'livewire.based-on-date-overview';
    protected int | string | array $columnSpan = 'full';

    public $stats = [];
    public $totalTransactionCount = 0;
    public $totalTransactionValue = 0;
    public $activeAccounts = 0;
    public $newAccountsOpened = 0;
    public $avgTransactionValue = 0;
    public $defaultCurrency = 'TZS';

    public $startDate;
    public $endDate;
    public $isFiltered = false;

    public function mount()
    {
        $this->resetFilter();
    }

    public function resetFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->isFiltered = false;
        $this->fetchData();
    }

    public function applyFilter(): void
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $this->isFiltered = true;
        $this->fetchData();
    }

    public function fetchData(): void
    {
        $query = DB::connection('mysql_second')->table('tbl_simba_transactions');

        if ($this->isFiltered && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        $transactionData = $query->selectRaw('
            COUNT(*) as total_transactions,
            SUM(credit_amount + debit_amount) as total_transaction_value,
            COUNT(DISTINCT user_id) as active_accounts
        ')->first();

        $this->totalTransactionCount = $transactionData->total_transactions;
        $this->totalTransactionValue = $transactionData->total_transaction_value;
        $this->activeAccounts = $transactionData->active_accounts;

        $this->newAccountsOpened = $this->getNewAccountsCount();

        $this->avgTransactionValue = $this->totalTransactionCount > 0
            ? $this->totalTransactionValue / $this->totalTransactionCount
            : 0;

        $this->calculateStats();
    }

    private function getNewAccountsCount()
    {
        $query = DB::connection('mysql_second')->table('users');

        if ($this->isFiltered && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        return $query->count();
    }

    private function calculateStats()
    {
        $currentPeriodData = [
            'total_transaction_count' => $this->totalTransactionCount,
            'total_transaction_value' => $this->totalTransactionValue,
            'active_accounts' => $this->activeAccounts,
            'new_accounts' => $this->newAccountsOpened,
            'avg_transaction_value' => $this->avgTransactionValue,
        ];

        if ($this->isFiltered) {
            $previousPeriodStart = Carbon::parse($this->startDate)->subDays(Carbon::parse($this->endDate)->diffInDays(Carbon::parse($this->startDate)) + 1);
            $previousPeriodEnd = Carbon::parse($this->startDate)->subDay();

            $previousPeriodData = $this->getPreviousPeriodData($previousPeriodStart, $previousPeriodEnd);

            foreach ($currentPeriodData as $key => $value) {
                $previousValue = $previousPeriodData[$key] ?? 0;
                $percentageChange = $previousValue != 0 ? (($value - $previousValue) / $previousValue) * 100 : 0;

                $this->stats[$key] = [
                    'value' => $value,
                    'isGrowth' => $percentageChange >= 0,
                    'percentageChange' => abs($percentageChange),
                ];
            }
        } else {
            foreach ($currentPeriodData as $key => $value) {
                $this->stats[$key] = [
                    'value' => $value,
                    'isGrowth' => null,
                    'percentageChange' => null,
                ];
            }
        }
    }

    private function getPreviousPeriodData($startDate, $endDate)
    {
        $query = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $transactionData = $query->selectRaw('
            COUNT(*) as total_transactions,
            SUM(credit_amount + debit_amount) as total_transaction_value,
            COUNT(DISTINCT user_id) as active_accounts
        ')->first();

        $newAccounts = DB::connection('mysql_second')->table('users')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgTransactionValue = $transactionData->total_transactions > 0
            ? $transactionData->total_transaction_value / $transactionData->total_transactions
            : 0;

        return [
            'total_transaction_count' => $transactionData->total_transactions,
            'total_transaction_value' => $transactionData->total_transaction_value,
            'active_accounts' => $transactionData->active_accounts,
            'new_accounts' => $newAccounts,
            'avg_transaction_value' => $avgTransactionValue,
        ];
    }

    #[Computed]
    public function getStats()
    {
        return $this->stats;
    }
}
