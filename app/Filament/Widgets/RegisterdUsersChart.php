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
            'week' => 'Last 4 weeks', // Adjusted filter label
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getOptions(): array
    {
        $activeFilter = $this->filter ?? 'week'; // Default to 'week'

        $data = match ($activeFilter) {
            'today' => $this->getTodayData(),
            'week' => $this->getWeeksData(), // Adjusted function call
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
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value) => $value->date)->toArray(),
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
            ],
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

    private function getWeeksData()
    {
        return Trend::model(User::class)
            ->between(
                start: now()->subWeeks(4)->startOfWeek(), // Adjusted to capture last 4 weeks
                end: now()->endOfWeek(),
            )
            ->perWeek()
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
