<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustStatsOverview extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.cust-stats-overview';

    public array $stats = [
        'avgValuePerDay' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgTransactionPerCustomer' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'customerStratification' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
    ];

    public function mount(): void
    {
        $this->calculateStats();
    }

    public function calculateStats(): void
    {
        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay();
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer();
        $this->stats['customerStratification'] = $this->calculateCustomerStratification();
    }

    private function calculateAverageValuePerDay(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $currentValue = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))'));

        $previousValue = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))'));

        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateAverageTransactionPerCustomer(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $currentValue = DB::connection('mysql_second')->table(DB::raw('(
        SELECT receiver_phone, COUNT(*) AS transaction_count
        FROM tbl_transactions
        WHERE created_at BETWEEN ? AND ?
        GROUP BY receiver_phone
    ) AS weekly_transactions'))
            ->setBindings([$currentWeekStart, $currentWeekEnd])
            ->avg('transaction_count');

        $previousValue = DB::connection('mysql_second')->table(DB::raw('(
        SELECT receiver_phone, COUNT(*) AS transaction_count
        FROM tbl_transactions
        WHERE created_at BETWEEN ? AND ?
        GROUP BY receiver_phone
    ) AS weekly_transactions'))
            ->setBindings([$previousWeekStart, $previousWeekEnd])
            ->avg('transaction_count');

        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateCustomerStratification(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        // Example stratification logic
        $stratum1Threshold = 2000; // Example threshold for high-value transactions
        $stratum1Count = DB::connection('mysql_second')->table('tbl_transactions')
            ->select(DB::raw('SUM(sender_amount) AS total_amount'))
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->groupBy('receiver_phone')
            ->having('total_amount', '>=', $stratum1Threshold)
            ->count();

        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $previousStratum1Count = DB::connection('mysql_second')->table('tbl_transactions')
            ->select(DB::raw('SUM(sender_amount) AS total_amount'))
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->groupBy('receiver_phone')
            ->having('total_amount', '>=', $stratum1Threshold)
            ->count();

        $percentageChange = $this->calculatePercentageChange($previousStratum1Count, $stratum1Count);

        return [
            'value' => $stratum1Count,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculatePercentageChange($previousValue, $currentValue): float
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }

        $change = $currentValue - $previousValue;
        return ($change / $previousValue) * 100;
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->stats,
        ];
    }
}
