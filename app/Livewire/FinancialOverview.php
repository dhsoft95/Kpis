<?php

namespace App\Livewire;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class FinancialOverview extends Widget
{
    protected static string $view = 'livewire.financial-overview';
    protected int | string | array $columnSpan = 'full';

    public $totalTransactions;
    public $totalTransactionValue;
    public $monthlyTransactions;
    public $monthlyTransactionValue;
    public $activeUsers;
    public $defaultCurrency = 'TZS';

    public function mount()
    {
        $this->fetchTransactionData();
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

        // Fetch active users in the last 60 days
        $this->activeUsers = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->where('created_at', '>=', Carbon::now()->subDays(60))
            ->whereIn('status', ['deposited', 'sent', 'received'])
            ->where(function ($query) {
                $query->where('credit_amount', '>', 0)
                    ->orWhere('debit_amount', '>', 0);
            })
            ->distinct('user_id')
            ->count('user_id');
    }
}
