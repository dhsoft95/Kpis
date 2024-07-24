<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChurnChart extends ChartWidget
{
    protected static ?string $heading = 'Week-on-Week Churn Users';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Define the date ranges for the current week and the previous week
        $today = Carbon::now()->startOfDay();
        $currentWeekStart = $today->copy()->startOfWeek();
        $currentWeekEnd = $today->copy()->endOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $previousWeekEnd = $currentWeekEnd->copy()->subWeek();

        // Fetch churn users count for current and previous week
        $currentWeekChurn = $this->getChurnCount($currentWeekStart, $today);
        $previousWeekChurn = $this->getChurnCount($previousWeekStart, $previousWeekEnd);

        return [
            'datasets' => [
                [
                    'label' => 'Previous Week',
                    'data' => [$previousWeekChurn],
                    'backgroundColor' => '#007bff', // Simple color
                ],
                [
                    'label' => 'Current Week',
                    'data' => [$currentWeekChurn],
                    'backgroundColor' => '#28a745', // Simple color
                ],
            ],
            'labels' => [
                $previousWeekStart->format('M d') . ' - ' . $previousWeekEnd->format('M d'),
                $currentWeekStart->format('M d') . ' - ' . $today->format('M d'),
            ],
        ];
    }

    private function getChurnCount(Carbon $start, Carbon $end): int
    {
        return DB::connection('mysql_second')->table('users')
            ->whereNotIn('users.phone_number', function ($query) use ($end) {
                $query->select('tbl_transactions.sender_phone')
                    ->from('tbl_transactions')
                    ->where('tbl_transactions.created_at', '>', DB::raw("DATE_SUB('{$end}', INTERVAL 30 DAY)"));
            })
            ->where('users.created_at', '<=', $end)
            ->count();
    }

    private function calculatePercentageChange($oldValue, $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 5 Weeks',
            'month' => 'Last 3 Months',
            'quarter' => 'Last Quarter',
            'year' => 'Last Year',
        ];
    }

    protected function getFooterWidgets(): array
    {
        $today = Carbon::now()->startOfDay();
        $currentWeekStart = $today->copy()->startOfWeek();
        $currentWeekEnd = $today->copy()->endOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $previousWeekEnd = $currentWeekEnd->copy()->subWeek();

        $currentWeekChurn = $this->getChurnCount($currentWeekStart, $today);
        $previousWeekChurn = $this->getChurnCount($previousWeekStart, $previousWeekEnd);

        $percentageChange = $this->calculatePercentageChange($previousWeekChurn, $currentWeekChurn);

        return [
            Stat::make('Week-on-Week Change', $percentageChange . '%')
                ->description($percentageChange >= 0 ? 'Increase in churn' : 'Decrease in churn')
                ->color($percentageChange >= 0 ? 'danger' : 'success')
                ->chart([
                    $previousWeekChurn,
                    $currentWeekChurn,
                ])
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
