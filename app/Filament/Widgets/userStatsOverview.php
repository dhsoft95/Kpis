<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UserStatsOverview extends Widget
{
    protected static string $view = 'filament.widgets.user-stats-overview';

    public array $stats = [
        'all' => ['count' => 0, 'percentageChange' => 0, 'isGrowth' => true],
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
        $this->calculateGrowth();
        $this->calculateUserStatuses();
    }

    public function calculateGrowth(): void
    {
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subWeek();
        $twoWeeksAgo = $now->copy()->subWeeks(2);

        $countTwoWeeksAgo = User::where('created_at', '<=', $twoWeeksAgo)->count();
        $countOneWeekAgo = User::where('created_at', '<=', $oneWeekAgo)->count();
        $countNow = User::count();

        $percentageChange = $this->calculatePercentageChange($countOneWeekAgo, $countNow);

        $this->stats['all'] = [
            'count' => $countNow,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    public function calculateUserStatuses(): void
    {
        Log::info('Starting calculateUserStatuses');

        $totalUsers = User::count();
        $statuses = ['active', 'inactive', 'churn'];

        $currentPeriodEnd = Carbon::now();
        $currentPeriodStart = $currentPeriodEnd->copy()->startOfWeek();
        $previousPeriodStart = $currentPeriodStart->copy()->subWeek();


        foreach ($statuses as $status) {
            $currentCount = User::where('status', $status)->count();

            $previousCount = User::where('status', $status)
                ->where('updated_at', '<', $currentPeriodStart)
                ->count();

            $difference = $currentCount - $previousCount;
            $percentage = $totalUsers > 0 ? ($currentCount / $totalUsers) * 100 : 0;
            $percentageChange = $this->calculatePercentageChange($previousCount, $currentCount);

            Log::info("$status: Current: $currentCount, Previous: $previousCount, Difference: $difference, Percentage: " . round($percentage, 2) . "%, PercentageChange: $percentageChange%");

            $this->stats[$status] = [
                'count' => $currentCount,
                'percentage' => round($percentage, 2),
                'percentageChange' => $percentageChange,
                'isGrowth' => $percentageChange >= 0,
            ];
        }

        $this->stats['avgValuePerDay'] = $this->calculateAverageValuePerDay();
        $this->stats['avgTransactionPerCustomer'] = $this->calculateAverageTransactionPerCustomer();

        Log::info('Finished calculateUserStatuses');
    }

    private function calculateAverageValuePerDay(): array
    {
        // Placeholder calculation - replace with actual logic
        $currentValue = 100; // Example value
        $previousValue = 2000; // Example value
        $difference = $currentValue - $previousValue;
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        Log::info("Avg Value Per Day: Current: $currentValue, Previous: $previousValue, Difference: $difference, Change: $percentageChange%");

        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateAverageTransactionPerCustomer(): array
    {
        // Placeholder calculation - replace with actual logic
        $currentValue = 12; // Example value
        $previousValue = 10; // Example value
        $difference = $currentValue - $previousValue;
        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        Log::info("Avg Transaction Per Customer: Current: $currentValue, Previous: $previousValue, Difference: $difference, Change: $percentageChange%");

        return [
            'value' => $currentValue,
            'percentageChange' => $percentageChange,
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    public function calculatePercentageChange($previousValue, $currentValue): float
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
