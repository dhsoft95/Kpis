<?php
namespace App\Filament\Widgets;

use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class RegisterdUsersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'registeredUsersChart';
    protected static ?string $heading = 'Registered Users Trend';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This week',
            'last_week' => 'Last week',
            'two_weeks' => 'Last 2 weeks',
            'month' => 'This month',
        ];
    }

    protected function getOptions(): array
    {
        $activeFilter = $this->filter ?? 'two_weeks';

        $data = $this->getWeekOnWeekData($activeFilter);

        return [
            'chart' => [
                'type' => 'line',
                'height' => 480,
            ],
            'series' => [
                [
                    'name' => 'This Week',
                    'data' => $data['current']->map(fn (TrendValue $value) => $value->aggregate),
                ],
                [
                    'name' => 'Previous Week',
                    'data' => $data['previous']->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b', '#60a5fa'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                    'horizontal' => false,
                ],
            ]
        ];
    }

    private function getWeekOnWeekData($filter)
    {
        $endDate = match ($filter) {
            'week' => now(),
            'last_week' => now()->subWeek(),
            'two_weeks' => now(),
            'month' => now()->endOfMonth(),
        };

        $startDate = match ($filter) {
            'week' => $endDate->copy()->startOfWeek(),
            'last_week' => $endDate->copy()->startOfWeek(),
            'two_weeks' => $endDate->copy()->subWeek()->startOfWeek(),
            'month' => $endDate->copy()->startOfMonth(),
        };

        $currentWeekData = Trend::model(User::class)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->count();

        $previousWeekData = Trend::model(User::class)
            ->between(
                start: $startDate->copy()->subWeek(),
                end: $endDate->copy()->subWeek(),
            )
            ->perDay()
            ->count();

        return [
            'current' => $currentWeekData,
            'previous' => $previousWeekData,
        ];
    }
}
