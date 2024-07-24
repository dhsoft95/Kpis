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

    // Default filter value
    protected string $filter = 'four_weeks';

    protected function getData(): array
    {
        $filter = $this->filter;
        $today = Carbon::now()->startOfDay();
        $weeks = $this->getWeekPeriods($filter, $today);

        $currentWeekChurn = [];
        $previousWeekChurn = [];

        foreach ($weeks as $week) {
            $currentWeekChurn[] = $this->getChurnCount($week['currentStart'], $week['currentEnd']);
            $previousWeekChurn[] = $this->getChurnCount($week['previousStart'], $week['previousEnd']);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Previous Week',
                    'data' => $previousWeekChurn,
                    'backgroundColor' => '#007bff', // Simple color
                ],
                [
                    'label' => 'Current Week',
                    'data' => $currentWeekChurn,
                    'backgroundColor' => '#28a745', // Simple color
                ],
            ],
            'labels' => array_map(function ($week) {
                return $week['currentStart']->format('M d') . ' - ' . $week['currentEnd']->format('M d');
            }, $weeks),
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

    private function getWeekPeriods(string $filter, Carbon $today): array
    {
        $weeks = [];
        switch ($filter) {
            case 'month':
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
                break;
            case 'quarter':
                $startDate = $today->copy()->startOfQuarter();
                $endDate = $today->copy()->endOfQuarter();
                break;
            case 'year':
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfYear();
                break;
            case 'four_weeks':
            default:
                $startDate = $today->copy()->startOfWeek()->subWeeks(3);
                $endDate = $today->copy()->endOfWeek();
                break;
        }

        // Generate weekly periods
        for ($i = 0; $i < 4; $i++) {
            $currentStart = $startDate->copy()->addWeeks($i);
            $currentEnd = $currentStart->copy()->endOfWeek();
            $previousStart = $currentStart->copy()->subWeek();
            $previousEnd = $previousStart->copy()->endOfWeek();

            $weeks[] = [
                'currentStart' => $currentStart,
                'currentEnd' => $currentEnd,
                'previousStart' => $previousStart,
                'previousEnd' => $previousEnd,
            ];
        }

        return $weeks;
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
            'four_weeks' => 'Last 4 Weeks',
            'month' => 'Last 3 Months',
            'quarter' => 'Last Quarter',
            'year' => 'Last Year',
        ];
    }

    protected function getFooterWidgets(): array
    {
        $filter = $this->filter;
        $today = Carbon::now()->startOfDay();
        $weeks = $this->getWeekPeriods($filter, $today);

        $currentWeekChurn = [];
        $previousWeekChurn = [];

        foreach ($weeks as $week) {
            $currentWeekChurn[] = $this->getChurnCount($week['currentStart'], $week['currentEnd']);
            $previousWeekChurn[] = $this->getChurnCount($week['previousStart'], $week['previousEnd']);
        }

        $percentageChanges = [];
        foreach ($currentWeekChurn as $index => $currentChurn) {
            $percentageChanges[] = $this->calculatePercentageChange($previousWeekChurn[$index], $currentChurn);
        }

        return [
            Stat::make('Week-on-Week Change', round(array_sum($percentageChanges) / count($percentageChanges), 2) . '%')
                ->description($percentageChanges[0] >= 0 ? 'Increase in churn' : 'Decrease in churn')
                ->color($percentageChanges[0] >= 0 ? 'danger' : 'success')
                ->chart(array_merge($previousWeekChurn, $currentWeekChurn))
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
