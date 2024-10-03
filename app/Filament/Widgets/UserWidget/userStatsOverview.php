<?php

namespace App\Filament\Widgets\UserWidget;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class userStatsOverview extends Widget
{
    protected static string $view = 'filament.widgets.user-stats-overview';
    protected int | string | array $columnSpan = 'full';

    public array $popularTransfers = [];
    public array $getPopularTransfersrouter = [];
    public array $stats = [
        'registered' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'active' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'inactive' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'churn' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgValuePerDay' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'avgTransactionPerCustomer' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'totalSuccess' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
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
        $twoWeeksAgo = $now->copy()->subWeeks(2);

        $this->calculateRegisteredUsers($now, $oneWeekAgo);
        $this->calculateActiveUsers($now, $oneWeekAgo, $twoWeeksAgo);
        $this->calculateInactiveUsers($now, $oneWeekAgo, $twoWeeksAgo);
        $this->calculateChurnUsers($now, $oneWeekAgo, $twoWeeksAgo);

        $this->calculateAdditionalStats($now, $oneWeekAgo, $twoWeeksAgo);
        $this->calculateTransactionStats($now, $oneWeekAgo);

        Log::info('User Stats Calculation Completed', $this->stats);
    }

    private function calculateRegisteredUsers(Carbon $now, Carbon $oneWeekAgo): void
    {
        $countOneWeekAgo = DB::connection('mysql_second')->table('users')
            ->where('created_at', '<=', $oneWeekAgo)
            ->count();

        $countNow = DB::connection('mysql_second')->table('users')->count();

        $this->updateStat('registered', $countNow, $countOneWeekAgo);
    }

    private function calculateActiveUsers(Carbon $now, Carbon $oneWeekAgo, Carbon $twoWeeksAgo): void
    {
        $currentCount = $this->getActiveUsersCount($oneWeekAgo, $now);
        $previousCount = $this->getActiveUsersCount($twoWeeksAgo, $oneWeekAgo);

        $this->updateStat('active', $currentCount, $previousCount);
    }

    private function calculateInactiveUsers(Carbon $now, Carbon $oneWeekAgo, Carbon $twoWeeksAgo): void
    {
        $currentCount = $this->getInactiveUsersCount($oneWeekAgo, $now);
        $previousCount = $this->getInactiveUsersCount($twoWeeksAgo, $oneWeekAgo);

        $this->updateStat('inactive', $currentCount, $previousCount);
    }

    private function calculateChurnUsers(Carbon $now, Carbon $oneWeekAgo, Carbon $twoWeeksAgo): void
    {
        $currentCount = $this->getChurnUsersCount($oneWeekAgo, $now);
        $previousCount = $this->getChurnUsersCount($twoWeeksAgo, $oneWeekAgo);

        $this->updateStat('churn', $currentCount, $previousCount);
    }

    private function calculateAdditionalStats(Carbon $now, Carbon $oneWeekAgo, Carbon $twoWeeksAgo): void
    {
        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay($now, $oneWeekAgo, $twoWeeksAgo);
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer($now, $oneWeekAgo, $twoWeeksAgo);
    }

    private function calculateTransactionStats(Carbon $now, Carbon $oneWeekAgo): void
    {
        $currentTotalSuccess = $this->getTransactionCount($oneWeekAgo, $now, ['deposited', 'sent', 'received']);
        $previousTotalSuccess = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, ['deposited', 'sent', 'received']);
        $this->updateStat('totalSuccess', $currentTotalSuccess, $previousTotalSuccess);

        $currentFailed = $this->getTransactionCount($oneWeekAgo, $now, ['failed']);
        $previousFailed = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, ['failed']);
        $this->updateStat('failed', $currentFailed, $previousFailed);

        $currentPending = $this->getTransactionCount($oneWeekAgo, $now, ['pending']);
        $previousPending = $this->getTransactionCount($oneWeekAgo->copy()->subWeek(), $oneWeekAgo, ['pending']);
        $this->updateStat('pending', $currentPending, $previousPending);
    }

    private function getTransactionCount(Carbon $startDate, Carbon $endDate, array $statuses): int
    {
        return DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', $statuses)
            ->count();
    }

    private function getActiveUsersCount(Carbon $startDate, Carbon $endDate): int
    {
        return DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['deposited', 'sent', 'received'])
            ->distinct('user_id')
            ->count('user_id');
    }

    private function getInactiveUsersCount(Carbon $startDate, Carbon $endDate): int
    {
        $activeUsers = DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['deposited', 'sent', 'received'])
            ->distinct('user_id')
            ->pluck('user_id');

        $totalUsers = DB::connection('mysql_second')
            ->table('users')
            ->where('created_at', '<=', $endDate)
            ->count();

        return $totalUsers - count($activeUsers);
    }

    private function getChurnUsersCount(Carbon $startDate, Carbon $endDate): int
    {
        $activeUsersCurrentWeek = DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'received')
            ->distinct('user_id')
            ->pluck('user_id');

        return DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->where('created_at', '<', $startDate)
            ->where('status', 'received')
            ->whereNotIn('user_id', $activeUsersCurrentWeek)
            ->distinct('user_id')
            ->count('user_id');
    }

    private function calculateAverageValuePerDay(Carbon $now, Carbon $oneWeekAgo, Carbon $twoWeeksAgo): array
    {
        $currentValue = $this->getAverageTransactionValuePerDay($oneWeekAgo, $now);
        $previousValue = $this->getAverageTransactionValuePerDay($twoWeeksAgo, $oneWeekAgo);

        return $this->calculateStatArray($currentValue, $previousValue);
    }

    private function getAverageTransactionValuePerDay(Carbon $startDate, Carbon $endDate): float
    {
        $result = DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->select(DB::raw('AVG(daily_total) as avg_daily_value'))
            ->fromSub(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('DATE(created_at) as date, SUM(credit_amount) as daily_total'))
                    ->from('tbl_simba_transactions')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'received')
                    ->groupBy(DB::raw('DATE(created_at)'));
            }, 'daily_totals')
            ->first();

        return $result->avg_daily_value ?? 0;
    }

    private function calculateAverageTransactionPerCustomer(Carbon $now, Carbon $oneWeekAgo, Carbon $twoWeeksAgo): array
    {
        $currentValue = $this->getAverageTransactionPerCustomer($oneWeekAgo, $now);
        $previousValue = $this->getAverageTransactionPerCustomer($twoWeeksAgo, $oneWeekAgo);

        return $this->calculateStatArray($currentValue, $previousValue);
    }

    private function getAverageTransactionPerCustomer(Carbon $startDate, Carbon $endDate): float
    {
        $result = DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->select(DB::raw('AVG(transaction_count) as avg_transactions_per_user'))
            ->fromSub(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('user_id, COUNT(*) as transaction_count'))
                    ->from('tbl_simba_transactions')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'received')
                    ->groupBy('user_id');
            }, 'user_transactions')
            ->first();

        return $result->avg_transactions_per_user ?? 0;
    }

    private function updateStat(string $key, int $currentCount, int $previousCount): void
    {
        $percentageChange = $previousCount > 0
            ? (($currentCount - $previousCount) / $previousCount) * 100
            : ($currentCount > 0 ? 100 : 0);

        $isGrowth = $percentageChange >= 0;

        $this->stats[$key]['count'] = $currentCount;
        $this->stats[$key]['percentageChange'] = round($percentageChange, 2);
        $this->stats[$key]['isGrowth'] = $isGrowth;
    }

    private function calculateStatArray(float $currentValue, float $previousValue): array
    {
        $percentageChange = $previousValue > 0
            ? (($currentValue - $previousValue) / $previousValue) * 100
            : ($currentValue > 0 ? 100 : 0);

        $isGrowth = $percentageChange >= 0;

        return [
            'value' => round($currentValue, 2),
            'percentageChange' => round($percentageChange, 2),
            'isGrowth' => $isGrowth,
        ];
    }

    public function getPopularTransfers(): array
    {
        return DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->select('sender_currency', DB::raw('COUNT(*) as transfer_count'))
            ->whereNotNull('sender_currency')
            ->groupBy('sender_currency')
            ->orderBy('transfer_count', 'desc')
            ->get()
            ->toArray();
    }

    public function getPopularTransfersrouter(): array
    {
        return DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->select('transaction_type', DB::raw('COUNT(*) as transfer_count'))
            ->whereNotNull('transaction_type')
            ->groupBy('transaction_type')
            ->orderBy('transfer_count', 'desc')
            ->get()
            ->toArray();
    }
}
