<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Widget;

class UserStatsOverview extends Widget
{
    // View associated with this widget
    protected static string $view = 'filament.widgets.user-stats-overview';

    // Array to store various statistics
    public array $stats = [
        'registered' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'active' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'inactive' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'churn' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgValuePerDay' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgTransactionPerCustomer' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
    ];

    // Initialize the widget and calculate statistics
    public function mount(): void
    {
        $this->calculateStats();
    }

    // Method to calculate all required statistics
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

    // Calculate the number of registered users
    private function calculateRegisteredUsers(Carbon $now, Carbon $oneWeekAgo): void
    {
        // Count of registered users up to one week ago
        $countOneWeekAgo = DB::connection('mysql_second')->table('users')
            ->where('created_at', '<=', $oneWeekAgo)
            ->count();

        // Current count of registered users
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

    // Calculate the average value of transactions per day
    private function calculateAverageValuePerDay(Carbon $now, Carbon $oneWeekAgo): void
    {
        $currentValue = $this->getAverageValuePerDay($now->copy()->subDays(7), $now);
        $previousValue = $this->getAverageValuePerDay($oneWeekAgo->copy()->subDays(7), $oneWeekAgo);

        $this->updateStat('avgValuePerDay', $currentValue, $previousValue);
    }

    // Calculate the average number of transactions per customer
    private function calculateAverageTransactionPerCustomer(Carbon $now, Carbon $oneWeekAgo): void
    {
        $currentValue = $this->getAverageTransactionPerCustomer($now->copy()->subDays(30), $now);
        $previousValue = $this->getAverageTransactionPerCustomer($oneWeekAgo->copy()->subDays(30), $oneWeekAgo);

        $this->updateStat('avgTransactionPerCustomer', $currentValue, $previousValue);
    }

    // Get the count of active users from a specific date
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

    // Get the count of churned users by checking if they have not transacted since a specific date
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

    // Calculate the average value of transactions per day over a date range
    private function getAverageValuePerDay(Carbon $fromDate, Carbon $toDate): float
    {
        // Get daily transaction values and counts
        $dailyValues = DB::connection('mysql_second')->table('tbl_transactions')
            ->select(DB::raw('DATE(created_at) as transaction_date'), DB::raw('SUM(CAST(sender_amount AS DECIMAL(15, 2))) as daily_value'), DB::raw('COUNT(*) as number_of_transactions'))
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3) // Assuming status 3 means successful transaction
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // Calculate total value and number of days
        $totalValue = $dailyValues->sum('daily_value');
        $numberOfDays = $toDate->diffInDays($fromDate) + 1; // +1 to include both start and end dates

        return ($numberOfDays > 0) ? ($totalValue / $numberOfDays) : 0;
    }

    // Calculate the average number of transactions per customer over a date range
    private function getAverageTransactionPerCustomer(Carbon $fromDate, Carbon $toDate): float
    {
        // Get total transactions and unique customers
        $transactionsData = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 3) // Assuming status 3 means completed transaction
            ->get();

        $transactionCount = $transactionsData->count();
        $customerCount = $transactionsData->pluck('sender_phone')->unique()->count();

        return $customerCount > 0 ? $transactionCount / $customerCount : 0;
    }

    // Update statistics with current and previous values and calculate percentage change
    private function updateStat(string $key, $currentValue, $previousValue): void
    {
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);
        $this->stats[$key] = [
            'count' => $currentValue,
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
