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
    public ?string $filter = 'four_weeks';

    protected function getData(): array
    {
        $filter = $this->filter;
        $today = Carbon::now()->startOfDay();

        // Define periods based on filter
        $periods = $this->getPeriodPeriods($filter, $today);

        $currentWeekChurn = $this->getChurnCount($periods['currentStart'], $periods['currentEnd']);
        $previousWeekChurn = $this->getChurnCount($periods['previousStart'], $periods['previousEnd']);

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
                $periods['previousStart']->format('M d') . ' - ' . $periods['previousEnd']->format('M d'),
                $periods['currentStart']->format('M d') . ' - ' . $periods['currentEnd']->format('M d'),
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

    private function getPeriodPeriods(string $filter, Carbon $today): array
    {
        $periods = [];
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
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
                break;
        }

        // Define current and previous week periods
        $currentStart = $startDate->copy();
        $currentEnd = $currentStart->copy()->endOfWeek();
        $previousStart = $currentStart->copy()->subWeek();
        $previousEnd = $previousStart->copy()->endOfWeek();

        $periods['currentStart'] = $currentStart;
        $periods['currentEnd'] = $currentEnd;
        $periods['previousStart'] = $previousStart;
        $periods['previousEnd'] = $previousEnd;

        return $periods;
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
        $today = Carbon::now()->startOfDay();
        $periods = $this->getPeriodPeriods($this->filter, $today);

        $currentWeekChurn = $this->getChurnCount($periods['currentStart'], $periods['currentEnd']);
        $previousWeekChurn = $this->getChurnCount($periods['previousStart'], $periods['previousEnd']);

        $percentageChange = $this->calculatePercentageChange($previousWeekChurn, $currentWeekChurn);

        return [
            Stat::make('Week-on-Week Change', $percentageChange . '%')
                ->description($percentageChange >= 0 ? 'Increase in churn' : 'Decrease in churn')
                ->color($percentageChange >= 0 ? 'danger' : 'success')
                ->chart([$previousWeekChurn, $currentWeekChurn])
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
                    'stacked' => false,
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
