<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyComparisionOverview extends Widget
{
    protected static string $view = 'livewire.monthly-comparision-overview';

    protected int|string|array $columnSpan = 'full';

    public $monthlyComparisons = [];
    public $defaultCurrency = 'TZS';

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData(): void
    {
        $currentMonth = $this->getMonthData(0);
        $lastMonth = $this->getMonthData(1);

        $this->monthlyComparisons = [
            'transactions_value' => $this->calculateComparison($currentMonth['transactions_value'], $lastMonth['transactions_value']),
            'transactions_count' => $this->calculateComparison($currentMonth['transactions_count'], $lastMonth['transactions_count']),
            'new_accounts' => $this->calculateComparison($currentMonth['new_accounts'], $lastMonth['new_accounts']),
            'active_accounts' => $this->calculateComparison($currentMonth['active_accounts'], $lastMonth['active_accounts']),
            'avg_loan_value' => $this->calculateComparison($this->generateDummyLoanValue(), $this->generateDummyLoanValue(true)),
        ];
    }

    private function getMonthData(int $monthsAgo): array
    {
        $endDate = Carbon::now()->subMonths($monthsAgo)->endOfMonth();
        $startDate = $endDate->copy()->startOfMonth();

        $transactionData = DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as transactions_count, SUM(credit_amount + debit_amount) as transactions_value')
            ->first();

        $newAccounts = DB::connection('mysql_second')
            ->table('users')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $activeAccounts = $this->getActiveUsersCount($startDate, $endDate);

        return [
            'transactions_value' => $transactionData->transactions_value ?? 0,
            'transactions_count' => $transactionData->transactions_count ?? 0,
            'new_accounts' => $newAccounts,
            'active_accounts' => $activeAccounts,
        ];
    }

    private function getActiveUsersCount(Carbon $startDate, Carbon $endDate): int
    {
        return DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['deposited', 'sent', 'received'])
            ->distinct('user_id')
            ->count('user_id');
    }

    private function generateDummyLoanValue($isLastMonth = false): float
    {
        $baseValue = 100; // Base average loan value
        $variation = $isLastMonth ? rand(-10, 10) : rand(-5, 15); // More variation for current month
        return $baseValue + $variation;
    }

    private function calculateComparison($currentValue, $lastValue): array
    {
        $percentageChange = $lastValue > 0
            ? (($currentValue - $lastValue) / $lastValue) * 100
            : 0;

        return [
            'current' => round($currentValue, 2),
            'last' => round($lastValue, 2),
            'percentage_change' => round($percentageChange, 2),
            'is_increase' => $percentageChange >= 0,
        ];
    }
}
