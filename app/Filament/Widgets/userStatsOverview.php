<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Widget;

class userStatsOverview extends Widget
{
    // The view associated with this widget
    protected static string $view = 'filament.widgets.user-stats-overview';

    // Array to hold various statistics
    public array $stats = [
        'registered' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'active' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'inactive' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'churn' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgValuePerDay' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgTransactionPerCustomer' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'totalSuccess' => ['count' => 110, 'percentageChange' => 0, 'isGrowth' => true],
        'failed' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'pending' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
    ];

    // Initialize the widget and calculate stats
    public function mount(): void
    {
        $this->calculateStats();
    }

    // Calculate all statistics
    public function calculateStats(): void
    {
        // Get current time and time ranges for calculations
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subWeek();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        // Calculate each type of statistic
        $this->calculateRegisteredUsers($now, $oneWeekAgo);
        $this->calculateActiveUsers($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->calculateInactiveUsers($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->calculateChurnUsers($now);
        $this->calculateAdditionalStats($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->calculateTransactionStats($now, $oneWeekAgo);
    }

    // Calculate the number of registered users
    private function calculateRegisteredUsers(Carbon $now, Carbon $oneWeekAgo): void
    {
        $countOneWeekAgo = DB::connection('mysql_second')->table('users')
            ->where('created_at', '<=', $oneWeekAgo)
            ->count();

        $countNow = DB::connection('mysql_second')->table('users')->count();

        $this->updateStat('registered', $countNow, $countOneWeekAgo);
    }

    // Calculate the number of active users
    private function calculateActiveUsers(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): void
    {
        $currentCount = $this->getActiveUsersCount($thirtyDaysAgo);
        $previousCount = $this->getActiveUsersCount($oneWeekAgo);

        $this->updateStat('active', $currentCount, $previousCount);
    }

    // Calculate the number of inactive users
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

    // Calculate the number of churned users
    private function calculateChurnUsers(Carbon $now): void
    {
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo = $now->copy()->subDays(7);

        $currentChurn = $this->getChurnUsersCount($thirtyDaysAgo);
        $previousChurn = $this->getChurnUsersCount($sevenDaysAgo);

        $this->updateStat('churn', $currentChurn, $previousChurn);
    }



    // Calculate additional statistics such as average value per day and transactions per customer
    private function calculateAdditionalStats(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): void
    {
        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer($now, $oneWeekAgo, $thirtyDaysAgo);
    }

    // Calculate transaction statistics: total success, failed, and pending transactions
    private function calculateTransactionStats(Carbon $now, Carbon $oneWeekAgo): void
    {
        // Success transactions
        $currentTotalSuccess = $this->getTransactionCount($oneWeekAgo, $now, 3);
        $previousTotalSuccess = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, 3);
        $this->updateStat('totalSuccess', $currentTotalSuccess, $previousTotalSuccess);

        // Failed transactions
        $currentFailed = $this->getTransactionCount($oneWeekAgo, $now, 4);
        $previousFailed = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, 4);
        $this->updateStat('failed', $currentFailed, $previousFailed);

        // Pending transactions
        $currentPending = $this->getTransactionCount($oneWeekAgo, $now, 1);
        $previousPending = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, 1);
        $this->updateStat('pending', $currentPending, $previousPending);

        // Debug logging
        \Log::info('Transaction Stats', [
            'currentTotalSuccess' => $currentTotalSuccess,
            'previousTotalSuccess' => $previousTotalSuccess,
            'currentFailed' => $currentFailed,
            'previousFailed' => $previousFailed,
            'currentPending' => $currentPending,
            'previousPending' => $previousPending,
        ]);
    }


    // Calculate average transaction value per day
    private function calculateAverageValuePerDay(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): array
    {
        $currentValue = $this->getAverageTransactionValue($thirtyDaysAgo, $now);
        $previousValue = $this->getAverageTransactionValue($oneWeekAgo, $now);

        return $this->calculateStatArray($currentValue, $previousValue);
    }

    // Calculate average number of transactions per customer
    private function calculateAverageTransactionPerCustomer(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): array
    {
        $currentValue = $this->getAverageTransactionPerCustomer($thirtyDaysAgo, $now);
        $previousValue = $this->getAverageTransactionPerCustomer($oneWeekAgo, $now);

        return $this->calculateStatArray($currentValue, $previousValue);
    }

    // Get the count of active users since a specific date
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

    // Get the count of churned users since a specific date
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

    // Get the average value of transactions between two dates
    private function getAverageTransactionValue(Carbon $fromDate, Carbon $toDate): float
    {
        return DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3) // Success transactions
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))')) ?? 0;
    }

    // Get the average number of transactions per customer between two dates
    private function getAverageTransactionPerCustomer(Carbon $fromDate, Carbon $toDate): float
    {
        $transactionCount = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        $userCount = $this->getActiveUsersCount($fromDate);

        return $userCount > 0 ? $transactionCount / $userCount : 0;
    }

    // Get the count of transactions with a specific status between two dates
    private function getTransactionCount(Carbon $fromDate, Carbon $toDate, int $status): int
    {
        $count = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', $status)
            ->count();

        // Log the query parameters for debugging
        \Log::info('Query Params', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'status' => $status,
            'count' => $count
        ]);

        return $count;
    }

    // Update statistics with new values and percentage changes
    private function updateStat(string $key, $currentValue, $previousValue): void
    {
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);
        $this->stats[$key] = [
            'count' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    // Create an array with value, percentage change, and growth status
    private function calculateStatArray($currentValue, $previousValue): array
    {
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);
        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    // Calculate the percentage change between previous and current values
    private function calculatePercentageChange($previous, $current): float
    {
        if ($previous == 0) {
            return $current == 0 ? 0 : 100;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
