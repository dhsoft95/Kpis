<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ActiveChart extends ChartWidget
{
    protected static ?string $heading = 'Active Users Trend';
    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = '10s';
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Active users',
                    'data' => [800, 900, 1000, 950, 1100, 1150, 1200, 12], // Example data for active users
                ],
            ],
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7'], // Example labels for the data
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
