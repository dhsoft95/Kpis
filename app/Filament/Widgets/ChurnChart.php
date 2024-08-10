<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Exception;

class ChurnChart extends ChartWidget
{
    protected static ?string $heading = 'Week-on-Week Churn Users';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    public ?string $filter = 'five_weeks';

    protected function getData(): array
    {
        try {
            $filter = $this->filter;
            $today = Carbon::now()->startOfDay();
            $weeks = $this->getWeekPeriods($filter, $today);

            $currentWeekChurn = [];
            $previousWeekChurn = [];
            $labels = [];

            foreach ($weeks as $week) {
                $currentWeekChurn[] = $this->getCachedChurnCount($week['currentStart'], $week['currentEnd']);
                $previousWeekChurn[] = $this->getCachedChurnCount($week['previousStart'], $week['previousEnd']);
                $labels[] = $week['currentStart']->format('M d') . ' - ' . $week['currentEnd']->format('M d');
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Previous Week',
                        'data' => $previousWeekChurn,
                        'backgroundColor' => '#4A58EC',
                    ],
                    [
                        'label' => 'Current Week',
                        'data' => $currentWeekChurn,
                        'backgroundColor' => '#48D3FF',
                    ],
                ],
                'labels' => $labels,
            ];
        } catch (Exception $e) {
            \Log::error('Error in ChurnChart getData: ' . $e->getMessage());
            return $this->getEmptyChartData();
        }
    }

    private function getCachedChurnCount(Carbon $start, Carbon $end): int
    {
        $cacheKey = "churn_count_{$start->timestamp}_{$end->timestamp}";
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($start, $end) {
            return $this->getChurnCount($start, $end);
        });
    }

    private function getChurnCount(Carbon $start, Carbon $end): int
    {
        return DB::connection('mysql_second')->table('users')
            ->whereIn('phone_number', function ($query) use ($start, $end) {
                $query->select('sender_phone')
                    ->from('tbl_transactions')
                    ->groupBy('sender_phone')
                    ->havingRaw('MAX(created_at) < ?', [$start]);
            })
            ->where('created_at', '<=', $end)
            ->count();
    }


    private function getWeekPeriods(string $filter, Carbon $today): array
    {
        $weeks = [];
        $endDate = $today->copy()->endOfWeek();
        $startDate = $this->getStartDateForFilter($filter, $today);

        $period = new \DatePeriod($startDate, new \DateInterval('P1W'), $endDate);

        foreach ($period as $date) {
            $currentStart = Carbon::instance($date)->startOfWeek();
            $currentEnd = $currentStart->copy()->endOfWeek();
            if ($currentEnd->isAfter($today)) {
                $currentEnd = $today->copy();
            }
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
    private function getStartDateForFilter(string $filter, Carbon $today): Carbon
    {
        return match ($filter) {
            'month' => $today->copy()->startOfMonth(),
            'quarter' => $today->copy()->startOfQuarter(),
            'year' => $today->copy()->startOfYear(),
            default => $today->copy()->startOfWeek()->subWeeks(4),
        };
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'five_weeks' => 'Last 5 Weeks',
            'month' => 'Last Month',
            'quarter' => 'Last Quarter',
            'year' => 'Last Year',
        ];
    }

    protected function getFooterWidgets(): array
    {
        try {
            $churnData = $this->getChurnData();
            $averageChange = $this->calculateAverageChange($churnData);

            return [
                Stat::make('Week-on-Week Change', $averageChange . '%')
                    ->description($averageChange >= 0 ? 'Increase in churn' : 'Decrease in churn')
                    ->color($averageChange >= 0 ? 'danger' : 'success')
                    ->chart($churnData)
            ];
        } catch (Exception $e) {
            \Log::error('Error in ChurnChart getFooterWidgets: ' . $e->getMessage());
            return [];
        }
    }

    private function getChurnData(): array
    {
        $filter = $this->filter;
        $today = Carbon::now()->startOfDay();
        $weeks = $this->getWeekPeriods($filter, $today);

        $churnData = [];
        foreach ($weeks as $week) {
            $churnData[] = $this->getCachedChurnCount($week['previousStart'], $week['previousEnd']);
            $churnData[] = $this->getCachedChurnCount($week['currentStart'], $week['currentEnd']);
        }

        return $churnData;
    }

    private function calculateAverageChange(array $churnData): float
    {
        $changes = [];
        for ($i = 0; $i < count($churnData) - 1; $i += 2) {
            $changes[] = $this->calculatePercentageChange($churnData[$i], $churnData[$i + 1]);
        }
        return round(array_sum($changes) / count($changes), 2);
    }

    private function calculatePercentageChange($oldValue, $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
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
                    'ticks' => [
                        'autoSkip' => false,
                    ],
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

    private function getEmptyChartData(): array
    {
        return [
            'datasets' => [
                ['label' => 'Previous Week', 'data' => []],
                ['label' => 'Current Week', 'data' => []],
            ],
            'labels' => [],
        ];
    }
}
