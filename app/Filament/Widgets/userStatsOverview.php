<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Widget;

class UserStatsOverview extends Widget
{
    protected static string $view = 'filament.widgets.user-stats-overview';

    public array $stats = [
        'registered' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'active' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'inactive' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'churn' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgValuePerDay' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgTransactionPerCustomer' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
    ];

    public function mount(): void
    {
        $this->calculateStats();
    }

    public function calculateStats(): void
    {
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subWeek();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        $this->calculateRegisteredUsers($now, $oneWeekAgo);
        $this->calculateActiveUsers($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->calculateInactiveUsers($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->calculateChurnUsers($now);
        $this->calculateAverageValuePerDay($now, $oneWeekAgo);
        $this->calculateAverageTransactionPerCustomer($now, $oneWeekAgo);
    }

    private function calculateRegisteredUsers(Carbon $now, Carbon $oneWeekAgo): void
    {
        $countOneWeekAgo = DB::connection('mysql_second')->table('users')
            ->where('created_at', '<=', $oneWeekAgo)
            ->count();

        $countNow = DB::connection('mysql_second')->table('users')->count();

        $this->updateStat('registered', $countNow, $countOneWeekAgo);
    }

    private function calculateActiveUsers(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): void
    {
        $currentCount = $this->getActiveUsersCount($thirtyDaysAgo);
        $previousCount = $this->getActiveUsersCount($oneWeekAgo);

        $this->updateStat('active', $currentCount, $previousCount);
    }

    private function calculateInactiveUsers(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): void
    {
        $totalUsersCurrent = DB::connection('mysql_second')->table('users')->count();
        $activeUsersCurrent = $this->getActiveUsersCount($thirtyDaysAgo);

        $totalUsersPrevious = DB::connection('mysql_second')->table('users')
            ->where('created_at', '<', $oneWeekAgo)
            ->count();
        $activeUsersPrevious = $this->getActiveUsersCount($oneWeekAgo);

        $inactiveUsersCurrent = $totalUsersCurrent - $activeUsersCurrent;
        $inactiveUsersPrevious = $totalUsersPrevious - $activeUsersPrevious;

        $this->updateStat('inactive', $inactiveUsersCurrent, $inactiveUsersPrevious);
    }

    private function calculateChurnUsers(Carbon $now): void
    {
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo = $now->copy()->subDays(7);

        $currentChurn = $this->getChurnUsersCount($thirtyDaysAgo);
        $previousChurn = $this->getChurnUsersCount($sevenDaysAgo);

        $this->updateStat('churn', $currentChurn, $previousChurn);
    }

    private function calculateAverageValuePerDay(Carbon $now, Carbon $oneWeekAgo): void
    {
        $currentValue = $this->getAverageValuePerDay($now->copy()->subDays(7), $now);
        $previousValue = $this->getAverageValuePerDay($oneWeekAgo->copy()->subDays(7), $oneWeekAgo);

        $this->updateStat('avgValuePerDay', $currentValue, $previousValue);
    }

    private function calculateAverageTransactionPerCustomer(Carbon $now, Carbon $oneWeekAgo): void
    {
        $currentValue = $this->getAverageTransactionPerCustomer($now->copy()->subDays(30), $now);
        $previousValue = $this->getAverageTransactionPerCustomer($oneWeekAgo->copy()->subDays(30), $oneWeekAgo);

        $this->updateStat('avgTransactionPerCustomer', $currentValue, $previousValue);
    }

    private function getActiveUsersCount(Carbon $fromDate): int
    {
        return DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) use ($fromDate) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', $fromDate);
            })
            ->count();
    }

    private function getChurnUsersCount(Carbon $date): int
    {
        return DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) use ($date) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->groupBy('sender_phone')
                    ->havingRaw('MAX(created_at) < ?', [$date]);
            })
            ->count();
    }

    private function getAverageValuePerDay(Carbon $fromDate, Carbon $toDate): float
    {
        $totalValue = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3) // Assuming status 3 means successful transaction
            ->sum(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))'));

        $totalTransactions = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3)
            ->count();

        $numberOfDays = $toDate->diffInDays($fromDate) + 1; // +1 to include both start and end dates

        return ($totalTransactions > 0 && $numberOfDays > 0) ? ($totalValue / $totalTransactions) / $numberOfDays : 0;
    }

    private function getAverageTransactionPerCustomer(Carbon $fromDate, Carbon $toDate): float
    {
        $transactionCount = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3) // Assuming status 3 means completed transaction
            ->count();

        $customerCount = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3)
            ->distinct('sender_phone')
            ->count('sender_phone');

        return $customerCount > 0 ? $transactionCount / $customerCount : 0;
    }

    private function updateStat(string $key, $currentValue, $previousValue): void
    {
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);
        $this->stats[$key] = [
            'count' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculatePercentageChange($previous, $current): float
    {
        if ($previous == 0) {
            return $current == 0 ? 0 : 100;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
