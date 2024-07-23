<?php

namespace App\Filament\Widgets;

use App\Models\AppUser;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class userStatsOverview extends Widget
{
    // Specifies the view associated with this widget
    protected static string $view = 'filament.widgets.user-stats-overview';

    // Initialize statistics with default values
    public array $stats = [
        'all' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'active' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true], // Added statistic for active users
        'inactive' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true], // Updated statistic for inactive users
        'churn' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true], // Updated statistic for churn
        'avgValuePerDay' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgTransactionPerCustomer' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
    ];

    // Lifecycle hook to calculate stats when the component is mounted
    public function mount(): void
    {
        $this->calculateStats();
    }

    // Method to calculate all statistics
    public function calculateStats(): void
    {
        $this->calculateGrowth();
        $this->calculateUserStatuses();
    }

    // Method to calculate the growth in the number of users
    public function calculateGrowth(): void
    {
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subWeek();

        // Count users as of one week ago and now
        $countOneWeekAgo = AppUser::where('created_at', '<=', $oneWeekAgo)->count();
        $countNow = AppUser::count();

        // Calculate percentage change
        $percentageChange = $this->calculatePercentageChange($countOneWeekAgo, $countNow);

        // Update stats with calculated values
        $this->stats['all'] = [
            'count' => $countNow,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    // Method to calculate user statuses and additional statistics
    public function calculateUserStatuses(): void
    {
        Log::info('Starting calculateUserStatuses');

        $now = Carbon::now();
        $currentPeriodStart = $now->startOfWeek();
        $previousPeriodStart = $currentPeriodStart->copy()->subWeek();
        $previousPeriodEnd = $currentPeriodStart->copy()->subSecond(); // End of previous week

        // Total registered users
        $totalUsersCurrent = AppUser::count();

        // Active users in the last 30 days
        $activeUsersCurrent = DB::table('tbl_transactions')
            ->select('receiver_phone')
            ->where('created_at', '>=', $currentPeriodStart->subDays(30)) // Last 30 days
            ->distinct()
            ->count('receiver_phone');

        // Calculate inactive users in the current period
        $inactiveUsersCurrent = $totalUsersCurrent - $activeUsersCurrent;

        // Active users in the previous period
        $activeUsersPrevious = DB::table('tbl_transactions')
            ->select('receiver_phone')
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->distinct()
            ->count('receiver_phone');

        // Calculate inactive users in the previous period
        $inactiveUsersPrevious = $totalUsersCurrent - $activeUsersPrevious;

        // Calculate percentage change in inactive users
        $percentageChangeInactiveUsers = $this->calculatePercentageChange($inactiveUsersPrevious, $inactiveUsersCurrent);

        // Calculate percentage change in active users
        $percentageChangeActiveUsers = $this->calculatePercentageChange($activeUsersPrevious, $activeUsersCurrent);

        // Calculate churn users
        $churnUsersCurrent = $this->calculateChurnUsers($currentPeriodStart);
        $churnUsersPrevious = $this->calculateChurnUsers($previousPeriodStart, $previousPeriodEnd);

        // Calculate percentage change in churn users
        $percentageChangeChurnUsers = $this->calculatePercentageChange($churnUsersPrevious, $churnUsersCurrent);

        // Log results
        Log::info("Active Users: Current: $activeUsersCurrent, Previous: $activeUsersPrevious, Change: $percentageChangeActiveUsers%");
        Log::info("Inactive Users: Current: $inactiveUsersCurrent, Previous: $inactiveUsersPrevious, Change: $percentageChangeInactiveUsers%");
        Log::info("Churn Users: Current: $churnUsersCurrent, Previous: $churnUsersPrevious, Change: $percentageChangeChurnUsers%");

        // Update stats
        $this->stats['active'] = [
            'count' => $activeUsersCurrent,
            'percentageChange' => $percentageChangeActiveUsers,
            'isGrowth' => $percentageChangeActiveUsers >= 0,
        ];

        $this->stats['inactive'] = [
            'count' => $inactiveUsersCurrent,
            'percentageChange' => $percentageChangeInactiveUsers,
            'isGrowth' => $percentageChangeInactiveUsers <= 0, // Decrease in inactive users is considered growth
        ];

        $this->stats['churn'] = [
            'count' => $churnUsersCurrent,
            'percentageChange' => $percentageChangeChurnUsers,
            'isGrowth' => $percentageChangeChurnUsers <= 0, // Decrease in churn users is considered growth
        ];

        // Calculate additional statistics
        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay();
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer();

        Log::info('Finished calculateUserStatuses');
    }

    // Method to calculate churn users
    private function calculateChurnUsers(Carbon $start, Carbon $end = null): int
    {
        $end = $end ?? Carbon::now();
        $churnUsers = DB::table('users')
            ->leftJoin('tbl_transactions', 'users.phone_number', '=', 'tbl_transactions.receiver_phone')
            ->whereNull('tbl_transactions.receiver_phone')
            ->where('users.created_at', '<=', $end->subDays(30))
            ->count('users.id');

        return $churnUsers;
    }

    // Method to calculate average value per day
    private function calculateAverageValuePerDay(): array
    {
        $currentDate = Carbon::now();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Average value for transactions in the last 30 days
        $currentValue = DB::table('tbl_transactions')
            ->whereBetween('created_at', [$thirtyDaysAgo, $currentDate])
            ->where('status', 3)
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))'));

        // Calculate previous week's average value
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $previousValue = DB::table('tbl_transactions')
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->where('status', 3)
            ->avg(DB::raw('CAST(sender_amount AS DECIMAL(15, 2))'));

        // Calculate difference and percentage change
        $difference = $currentValue - $previousValue;
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);
        Log::info("Avg Value Per Day: Current: $currentValue, Previous: $previousValue, Difference: $difference, Change: $percentageChange%");

        // Return average value stats
        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    // Method to calculate average transactions per customer
    private function calculateAverageTransactionPerCustomer(): array
    {
        // Define date ranges for calculations
        $last30DaysStart = Carbon::now()->subDays(30);
        $last30DaysEnd = Carbon::now();
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        // Calculate average transactions for the last 30 days
        $averageValue = DB::table(DB::raw('(
        SELECT receiver_phone, COUNT(*) AS transaction_count
        FROM tbl_transactions
        WHERE created_at BETWEEN ? AND ?
        AND status = 3
        GROUP BY receiver_phone
    ) AS monthly_transactions'))
            ->setBindings([$last30DaysStart, $last30DaysEnd])
            ->avg('transaction_count');

        // Calculate average transactions for the current week
        $currentWeekValue = DB::table(DB::raw('(
        SELECT receiver_phone, COUNT(*) AS transaction_count
        FROM tbl_transactions
        WHERE created_at BETWEEN ? AND ?
        AND status = 3
        GROUP BY receiver_phone
    ) AS weekly_transactions'))
            ->setBindings([$currentWeekStart, $currentWeekEnd])
            ->avg('transaction_count');

        // Calculate average transactions for the previous week
        $previousWeekValue = DB::table(DB::raw('(
        SELECT receiver_phone, COUNT(*) AS transaction_count
        FROM tbl_transactions
        WHERE created_at BETWEEN ? AND ?
        AND status = 3
        GROUP BY receiver_phone
    ) AS weekly_transactions'))
            ->setBindings([$previousWeekStart, $previousWeekEnd])
            ->avg('transaction_count');

        // Calculate percentage change and log results
        $percentageChange = $this->calculatePercentageChange($previousWeekValue, $currentWeekValue);
        Log::info("Avg Transaction Per Customer: Last 30 Days: $averageValue, Current Week: $currentWeekValue, Previous Week: $previousWeekValue, Change: $percentageChange%");

        // Return average transaction stats
        return [
            'value' => $averageValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    // Helper method to calculate percentage change
    public function calculatePercentageChange($previousValue, $currentValue): float
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }

        $change = $currentValue - $previousValue;
        return ($change / $previousValue) * 100;
    }

    // Method to provide data to the view
    protected function getViewData(): array
    {
        return [
            'stats' => $this->stats,
        ];
    }
}
