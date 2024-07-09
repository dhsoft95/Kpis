<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class RegisteredChart extends ChartWidget
{
    protected static ?string $heading = 'Registered Users Trend';
    protected static ?string $maxHeight = '300px';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This week',
            'last_week' => 'Last week',
            'two_weeks' => 'Last 2 weeks',
            'month' => 'This month',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'two_weeks';
        $data = $this->getWeekOnWeekData($activeFilter);

        return [
            'datasets' => [
                [
                    'label' => 'This Week',
                    'data' => $data['current']->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Previous Week',
                    'data' => $data['previous']->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'backgroundColor' => '#60a5fa',
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
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
