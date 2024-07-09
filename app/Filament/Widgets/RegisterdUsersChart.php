<?php
namespace App\Filament\Widgets;

use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RegisterdUsersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'registeredUsersChart';
    protected static ?string $heading = 'Registered Users Trend';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getOptions(): array
    {
        $activeFilter = $this->filter ?? 'year';

        $data = match ($activeFilter) {
            'today' => $this->getTodayData(),
            'week' => $this->getWeekData(),
            'month' => $this->getMonthData(),
            'year' => $this->getYearData(),
        };

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 480,
            ],
            'series' => [
                [
                    'name' => 'Registered Users',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value) => $value->date),
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
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                    'horizontal' => false,
                ],
                ]
        ];
    }

    private function getTodayData()
    {
        return Trend::model(User::class)
            ->between(
                start: now()->startOfDay(),
                end: now()->endOfDay(),
            )
            ->perHour()
            ->count();
    }

    private function getWeekData()
    {
        return Trend::model(User::class)
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();
    }

    private function getMonthData()
    {
        return Trend::model(User::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();
    }

    private function getYearData()
    {
        return Trend::model(User::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();
    }
}
