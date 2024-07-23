<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Widget;

class userStatsOverview extends Widget
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
        $this->calculateAdditionalStats($now, $oneWeekAgo, $thirtyDaysAgo);
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
        $currentWeekStart = $now->copy()->startOfWeek();
        $currentWeekEnd = $now->copy()->endOfWeek();
        $previousWeekStart = $now->copy()->subWeek()->startOfWeek();
        $previousWeekEnd = $now->copy()->subWeek()->endOfWeek();

        $currentCount = $this->getChurnUsersCount($currentWeekStart, $currentWeekEnd);
        $previousCount = $this->getChurnUsersCount($previousWeekStart, $previousWeekEnd);

        $this->updateStat('churn', $currentCount, $previousCount);
    }

    private function calculateAdditionalStats(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): void
    {
        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer($now, $oneWeekAgo, $thirtyDaysAgo);
    }

    private function calculateAverageValuePerDay(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): array
    {
        $currentValue = $this->getAverageTransactionValue($thirtyDaysAgo, $now);
        $previousValue = $this->getAverageTransactionValue($oneWeekAgo, $now);

        return $this->calculateStatArray($currentValue, $previousValue);
    }

    private function calculateAverageTransactionPerCustomer(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): array
    {
        $currentValue = $this->getAverageTransactionPerCustomer($thirtyDaysAgo, $now);
        $previousValue = $this->getAverageTransactionPerCustomer($oneWeekAgo, $now);

        return $this->calculateStatArray($currentValue, $previousValue);
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

    private function getChurnUsersCount(Carbon $startDate, Carbon $endDate): int
    {
        return DB::connection('mysql_second')->table('users')
            ->whereNotIn('phone_number', function ($query) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    private function getAverageTransactionValue(Carbon $fromDate, Carbon $toDate): float
    {
        return DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3)
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))')) ?? 0;
    }

    private function getAverageTransactionPerCustomer(Carbon $fromDate, Carbon $toDate): float
    {
        $transactionCount = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        $userCount = $this->getActiveUsersCount($fromDate);

        return $userCount > 0 ? $transactionCount / $userCount : 0;
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

    private function calculateStatArray($currentValue, $previousValue): array
    {
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);
        return [
            'value' => $currentValue,
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
