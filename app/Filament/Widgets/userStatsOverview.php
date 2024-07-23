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
        // Initial calculation when the widget is mounted
        $this->calculateStats();
    }

    public function calculateStats(): void
    {
        // Perform calculations for each statistic
        $this->calculateRegisteredUsers();
        $this->calculateActiveUsers();
        $this->calculateInactiveUsers();
        $this->calculateChurnUsers();
        $this->calculateAdditionalStats();
    }

    private function calculateRegisteredUsers(): void
    {
        // Calculate the count and Week-on-Week (WoW) percentage change for registered users
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subWeek();

        // Count of users registered one week ago
        $countOneWeekAgo = DB::connection('mysql_second')->table('users')
            ->where('created_at', '<=', $oneWeekAgo)
            ->count();

        // Count of users registered now
        $countNow = DB::connection('mysql_second')->table('users')->count();

        // Calculate percentage change from one week ago to now
        $percentageChange = $this->calculatePercentageChange($countOneWeekAgo, $countNow);

        $this->stats['registered'] = [
            'count' => $countNow,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateActiveUsers(): void
    {
        // Calculate the count and Week-on-Week (WoW) percentage change for active users
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        // Count of active users in the last 30 days
        $currentCount = DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) use ($thirtyDaysAgo) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', $thirtyDaysAgo);
            })
            ->count();

        // Count of active users 30 days ago
        $previousCount = DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) use ($thirtyDaysAgo) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '<', $thirtyDaysAgo);
            })
            ->count();

        // Calculate percentage change from 30 days ago to now
        $percentageChange = $this->calculatePercentageChange($previousCount, $currentCount);

        $this->stats['active'] = [
            'count' => $currentCount,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateInactiveUsers(): void
    {
        // Calculate the count and Week-on-Week (WoW) percentage change for inactive users
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subWeek();

        // Count of total and active users currently and one week ago
        $totalUsersCurrent = DB::connection('mysql_second')->table('users')->count();
        $activeUsersCurrent = DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->count();

        $totalUsersPrevious = DB::connection('mysql_second')->table('users')
            ->where('created_at', '<', $oneWeekAgo)
            ->count();

        $activeUsersPrevious = DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '<', $oneWeekAgo);
            })
            ->count();

        // Calculate inactive users for the current and previous periods
        $inactiveUsersCurrent = $totalUsersCurrent - $activeUsersCurrent;
        $inactiveUsersPrevious = $totalUsersPrevious - $activeUsersPrevious;

        // Calculate percentage change in inactive users
        $percentageChange = $this->calculatePercentageChange($inactiveUsersPrevious, $inactiveUsersCurrent);

        $this->stats['inactive'] = [
            'count' => $inactiveUsersCurrent,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateChurnUsers(): void
    {
        // Calculate the count and Week-on-Week (WoW) percentage change for churn users
        $now = Carbon::now();
        $currentWeekStart = $now->startOfWeek();
        $currentWeekEnd = $now->endOfWeek();
        $previousWeekStart = $now->copy()->subWeek()->startOfWeek();
        $previousWeekEnd = $now->copy()->subWeek()->endOfWeek();

        // Count of churn users for the current and previous weeks
        $currentCount = DB::connection('mysql_second')->table('users')
            ->whereNotIn('phone_number', function ($query) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->count();

        $previousCount = DB::connection('mysql_second')->table('users')
            ->whereNotIn('phone_number', function ($query) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->count();

        // Calculate percentage change in churn users
        $percentageChange = $this->calculatePercentageChange($previousCount, $currentCount);

        $this->stats['churn'] = [
            'count' => $currentCount,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateAdditionalStats(): void
    {
        // Calculate additional statistics like average value per day and average transaction per customer
        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay();
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer();
    }

    private function calculateAverageValuePerDay(): array
    {
        // Calculate the average transaction value per day for the last 30 days and the previous week
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        $currentValue = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$thirtyDaysAgo, $now])
            ->where('status', 3)
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))'));

        $previousWeekStart = $now->copy()->subWeek()->startOfWeek();
        $previousWeekEnd = $now->copy()->subWeek()->endOfWeek();
        $previousValue = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->where('status', 3)
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))'));

        // Calculate percentage change in average value per day
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateAverageTransactionPerCustomer(): array
    {
        // Calculate the average number of transactions per customer
        $now = Carbon::now();
        $last30DaysStart = $now->copy()->subDays(30);
        $currentWeekStart = $now->startOfWeek();
        $currentWeekEnd = $now->endOfWeek();
        $previousWeekStart = $now->copy()->subWeek()->startOfWeek();
        $previousWeekEnd = $now->copy()->subWeek()->endOfWeek();

        $currentTransactionCount = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$last30DaysStart, $now])
            ->count();

        $previousTransactionCount = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->count();

        $currentUserCount = DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) use ($last30DaysStart) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', $last30DaysStart);
            })
            ->count();

        $previousUserCount = DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) use ($previousWeekStart) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->where('created_at', '>=', $previousWeekStart);
            })
            ->count();

        $currentValue = $currentUserCount > 0 ? $currentTransactionCount / $currentUserCount : 0;
        $previousValue = $previousUserCount > 0 ? $previousTransactionCount / $previousUserCount : 0;

        // Calculate percentage change in average transactions per customer
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculatePercentageChange($previous, $current): float
    {
        // Helper function to calculate percentage change between two values
        if ($previous == 0) {
            return $current == 0 ? 0 : 100;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
