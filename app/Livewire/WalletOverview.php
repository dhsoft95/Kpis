<?php

// File: app/Livewire/WalletOverview.php

namespace App\Livewire;

use App\Models\WalletBalance;
use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletOverview extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.wallet-overview';

    public $stats = [];
    public $totalCounts = [];

    // Partner balance properties
    public $balanceTeraPay;
    public $currencyTeraPay;
    public $statusTeraPay;

    public $balanceTembo;
    public $availableBalanceTembo;
    public $statusTembo;

    public $balanceCellulant;
    public $currencyCellulant;
    public $statusCellulant;

    // Financial overview properties
    public $totalTransactions;
    public $totalTransactionValue;
    public $monthlyTransactions;
    public $monthlyTransactionValue;
    public $activeUsers;
    public $avgTransactionValuePerActiveCustomer;
    public $defaultCurrency = 'TZS';

    public function mount()
    {
        $this->stats = $this->calculateStats();
        $this->totalCounts = $this->getTotalCounts();
        $this->fetchPartnerBalances();
        $this->fetchTransactionData();
    }

    #[Computed]
    public function stats()
    {
        return $this->calculateStats();
    }

    public function calculateStats()
    {
        $statuses = ['active', 'failed', 'inactive', 'inprogress', 'pending'];
        $currentWeekCounts = $this->getWeekCounts($statuses, 0);
        $lastWeekCounts = $this->getWeekCounts($statuses, 1);

        $stats = [];
        foreach ($statuses as $status) {
            $currentCount = $currentWeekCounts[$status] ?? 0;
            $lastWeekCount = $lastWeekCounts[$status] ?? 0;

            $percentageChange = $lastWeekCount > 0
                ? (($currentCount - $lastWeekCount) / $lastWeekCount) * 100
                : 0;

            $stats[$status] = [
                'value' => $currentCount,
                'isGrowth' => $percentageChange >= 0,
                'percentageChange' => abs($percentageChange),
            ];
        }

        return $stats;
    }

    private function getWeekCounts(array $statuses, int $weeksAgo): array
    {
        $startDate = Carbon::now()->subWeeks($weeksAgo)->startOfWeek();
        $endDate = Carbon::now()->subWeeks($weeksAgo)->endOfWeek();

        $counts = DB::connection('mysql_second')
            ->table('users')
            ->select('wallet_status', DB::raw('COUNT(*) as count'))
            ->whereIn('wallet_status', $statuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('wallet_status')
            ->get()
            ->pluck('count', 'wallet_status')
            ->toArray();

        $allCounts = array_fill_keys($statuses, 0);
        foreach ($counts as $status => $count) {
            $allCounts[$status] = $count;
        }

        return $allCounts;
    }

    private function getTotalCounts(): array
    {
        $statuses = ['active', 'failed', 'inactive', 'inprogress', 'pending'];

        $counts = DB::connection('mysql_second')
            ->table('users')
            ->select('wallet_status', DB::raw('COUNT(*) as count'))
            ->whereIn('wallet_status', $statuses)
            ->groupBy('wallet_status')
            ->get()
            ->pluck('count', 'wallet_status')
            ->toArray();

        $totalCounts = array_fill_keys($statuses, 0);
        foreach ($counts as $status => $count) {
            $totalCounts[$status] = $count;
        }

        return $totalCounts;
    }

    public function fetchPartnerBalances(): void
    {
        $teraPay = WalletBalance::where('partner', 'TeraPay')->first();
        $tembo = WalletBalance::where('partner', 'Tembo')->first();
        $cellulant = WalletBalance::where('partner', 'Cellulant')->first();

        if ($teraPay) {
            $this->balanceTeraPay = $teraPay->balance;
            $this->currencyTeraPay = $teraPay->currency;
            $this->statusTeraPay = $teraPay->status;
        }

        if ($tembo) {
            $this->balanceTembo = $tembo->balance;
            $this->availableBalanceTembo = $tembo->available_balance;
            $this->statusTembo = $tembo->status;
        }

        if ($cellulant) {
            $this->balanceCellulant = $cellulant->balance;
            $this->currencyCellulant = $cellulant->currency;
            $this->statusCellulant = $cellulant->status;
        }
    }

    public function fetchTransactionData(): void
    {
        // Fetch total transactions and value since inception
        $totals = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->selectRaw('COUNT(*) as count, SUM(credit_amount + debit_amount) as total')
            ->first();
        $this->totalTransactions = $totals->count;
        $this->totalTransactionValue = $totals->total;

        // Fetch monthly transactions and value
        $monthlyTotals = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('COUNT(*) as count, SUM(credit_amount + debit_amount) as total')
            ->first();
        $this->monthlyTransactions = $monthlyTotals->count;
        $this->monthlyTransactionValue = $monthlyTotals->total;

        // Fetch active users and their transaction value in the last 60 days
        $activeUserData = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->where('created_at', '>=', Carbon::now()->subDays(60))
            ->whereIn('status', ['deposited', 'sent', 'received'])
            ->where(function ($query) {
                $query->where('credit_amount', '>', 0)
                    ->orWhere('debit_amount', '>', 0);
            })
            ->selectRaw('COUNT(DISTINCT user_id) as active_users, SUM(credit_amount + debit_amount) as total_value')
            ->first();

        $this->activeUsers = $activeUserData->active_users;
        // Calculate Average Transaction Value per Active Customer
        if ($this->activeUsers > 0) {
            $this->avgTransactionValuePerActiveCustomer = $activeUserData->total_value / $this->activeUsers;
        } else {
            $this->avgTransactionValuePerActiveCustomer = 0;
        }
    }
}
