<?php

namespace App\Filament\Widgets\UserWidget;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class userStatsOverview extends Widget
{
    protected static string $view = 'filament.widgets.user-stats-overview';
//    protected int | string | array $columnSpan = 'full';
    public array $popularTransfers = [];
    public array $getPopularTransfersrouter = [];

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

    public function mount(): void
    {
        $this->calculateStats();
        $this->popularTransfers = $this->getPopularTransfers();
        $this->getPopularTransfersrouter = $this->getPopularTransfersrouter();
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
        $this->calculateTransactionStats($now, $oneWeekAgo);
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

    private function calculateAdditionalStats(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): void
    {
        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay($now, $oneWeekAgo, $thirtyDaysAgo);
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer($now, $oneWeekAgo, $thirtyDaysAgo);
    }

    private function calculateTransactionStats(Carbon $now, Carbon $oneWeekAgo): void
    {
        $currentTotalSuccess = $this->getTransactionCount($oneWeekAgo, $now, 3);
        $previousTotalSuccess = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, 3);
        $this->updateStat('totalSuccess', $currentTotalSuccess, $previousTotalSuccess);

        $currentFailed = $this->getTransactionCount($oneWeekAgo, $now, 4);
        $previousFailed = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, 4);
        $this->updateStat('failed', $currentFailed, $previousFailed);

        $currentPending = $this->getTransactionCount($oneWeekAgo, $now, 1);
        $previousPending = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, 1);
        $this->updateStat('pending', $currentPending, $previousPending);

        \Log::info('Transaction Stats', [
            'currentTotalSuccess' => $currentTotalSuccess,
            'previousTotalSuccess' => $previousTotalSuccess,
            'currentFailed' => $currentFailed,
            'previousFailed' => $previousFailed,
            'currentPending' => $currentPending,
            'previousPending' => $previousPending,
        ]);
    }

    private function calculateAverageValuePerDay(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): array
    {
        $currentValue = $this->getAverageTransactionValuePerDay($thirtyDaysAgo);
        $previousValue = $this->getAverageTransactionValuePerDay($oneWeekAgo);

        return $this->calculateStatArray($currentValue, $previousValue);
    }

    private function getAverageTransactionValuePerDay(Carbon $fromDate): float
    {
        $query = "
            SELECT AVG(daily_transaction_count) as average_transactions_per_day
            FROM (
                SELECT SUM(sender_amount) as daily_transaction_count
                FROM tbl_transactions
                WHERE created_at >= ? AND status = '3'
                GROUP BY DATE(created_at)
            ) as daily_transactions
        ";

        $result = DB::connection('mysql_second')
            ->select($query, [$fromDate->toDateString()]);

        return $result[0]->average_transactions_per_day ?? 0;
    }

    private function calculateAverageTransactionPerCustomer(Carbon $now, Carbon $oneWeekAgo, Carbon $thirtyDaysAgo): array
    {
        $currentValue = $this->getAverageTransactionPerCustomer($thirtyDaysAgo);
        $previousValue = $this->getAverageTransactionPerCustomer($oneWeekAgo);

        return $this->calculateStatArray($currentValue, $previousValue);
    }

    private function getAverageTransactionPerCustomer(Carbon $fromDate): float
    {
        $query = "
            SELECT AVG(daily_transaction_count) as average_transactions_per_customer
            FROM (
                SELECT COUNT(sender_amount) as daily_transaction_count
                FROM tbl_transactions
                WHERE created_at >= ? AND status = '3'
                GROUP BY sender_phone
            ) as daily_transactions
        ";

        $result = DB::connection('mysql_second')
            ->select($query, [$fromDate->toDateString()]);

        return $result[0]->average_transactions_per_customer ?? 0;
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

    private function getTransactionCount(Carbon $fromDate, Carbon $toDate, int $status): int
    {
        $count = DB::connection('mysql_second')->table('tbl_transactions')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', $status)
            ->count();

        \Log::info('Query Params', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'status' => $status,
            'count' => $count
        ]);

        return $count;
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

    public function getPopularTransfers()
    {
        return DB::connection('mysql_second')
            ->table('tbl_transactions')
            ->select('sender_channel_name', 'receiver_channel_name', DB::raw('COUNT(*) AS transaction_count'))
            ->groupBy('sender_channel_name', 'receiver_channel_name')
            ->orderBy('transaction_count', 'DESC')
            ->limit(2)
            ->get()
            ->map(function ($item) {
                return [
                    'route' => $item->sender_channel_name . ' → ' . $item->receiver_channel_name,
                    'count' => $item->transaction_count
                ];
            })->toArray();
    }

    public function getPopularTransfersrouter()
    {
        return DB::connection('mysql_second')
            ->table('tbl_transactions')
            ->select('sender_channel_country', 'receiver_channel_country', DB::raw('COUNT(*) AS Popular_transfers_router'))
            ->groupBy('sender_channel_country', 'receiver_channel_country')
            ->orderBy('Popular_transfers_router', 'DESC')
            ->limit(2)
            ->get()
            ->map(function ($item) {
                return [
                    'route' => $item->sender_channel_country . ' → ' . $item->receiver_channel_country,
                    'count' => $item->Popular_transfers_router
                ];
            })
            ->toArray();
    }

    private function calculatePercentageChange($previous, $current): float
    {
        if ($previous == 0) {
            return $current == 0 ? 0 : 100;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
