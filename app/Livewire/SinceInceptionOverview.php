<?php

namespace App\Livewire;

use App\Models\WalletBalance;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class SinceInceptionOverview extends Widget
{
    protected static string $view = 'livewire.since-inception-overview';
    protected int | string | array $columnSpan = 'full';
    public $stats = [];

    // Partner balance properties
    public $balanceTembo;
    public $balanceTeraPay;
    public $currencyTeraPay;

    // Financial overview properties
    public $totalTransactions;
    public $totalTransactionValue;
    public $totalAccounts;
    public $activeAccounts;
    public $inactiveAccounts;
    public $defaultCurrency = 'TZS';

    public function mount()
    {
        $this->fetchData();
    }

    #[Computed]
    public function stats()
    {
        return $this->calculateStats();
    }

    public function calculateStats()
    {
        $currentWeekData = $this->getWeekData(0);
        $lastWeekData = $this->getWeekData(1);

        $stats = [];
        $metrics = ['total_transactions', 'total_transaction_value', 'active_accounts', 'inactive_accounts'];

        foreach ($metrics as $metric) {
            $currentValue = $currentWeekData[$metric] ?? 0;
            $lastWeekValue = $lastWeekData[$metric] ?? 0;

            $percentageChange = $lastWeekValue > 0
                ? (($currentValue - $lastWeekValue) / $lastWeekValue) * 100
                : 0;

            $stats[$metric] = [
                'value' => $currentValue,
                'isGrowth' => $percentageChange >= 0,
                'percentageChange' => abs($percentageChange),
            ];
        }

        return $stats;
    }

    private function getWeekData(int $weeksAgo): array
    {
        $endDate = Carbon::now()->subWeeks($weeksAgo)->endOfWeek();
        $startDate = $endDate->copy()->subDays(59);  // 60 days including the end date

        $transactionData = DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total_transactions, SUM(credit_amount + debit_amount) as total_transaction_value')
            ->first();

        $activeAccounts = $this->getActiveUsersCount($startDate, $endDate);

        $totalAccounts = DB::connection('mysql_second')
            ->table('users')
            ->where('created_at', '<=', $endDate)
            ->count();

        $inactiveAccounts = $totalAccounts - $activeAccounts;

        return [
            'total_transactions' => $transactionData->total_transactions,
            'total_transaction_value' => $transactionData->total_transaction_value,
            'active_accounts' => $activeAccounts,
            'inactive_accounts' => $inactiveAccounts,
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

    public function fetchData(): void
    {
        // Fetch partner balances
        $tembo = WalletBalance::where('partner', 'Tembo')->first();
        $teraPay = WalletBalance::where('partner', 'TeraPay')->first();

        $this->balanceTembo = $tembo->balance ?? 0;
        $this->balanceTeraPay = $teraPay->balance ?? 0;
        $this->currencyTeraPay = $teraPay->currency ?? 'USD';

        // Fetch total transactions and value since inception
        $totals = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->selectRaw('COUNT(*) as count, SUM(credit_amount + debit_amount) as total')
            ->first();
        $this->totalTransactions = $totals->count;
        $this->totalTransactionValue = $totals->total;

        // Fetch account data for the last 60 days
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(59);

        $this->activeAccounts = $this->getActiveUsersCount($startDate, $endDate);

        $this->totalAccounts = DB::connection('mysql_second')
            ->table('users')
            ->count();

        $this->inactiveAccounts = $this->totalAccounts - $this->activeAccounts;
    }
}
