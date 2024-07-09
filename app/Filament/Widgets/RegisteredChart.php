<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RegisteredChart extends ChartWidget
{
    protected static ?string $heading = 'All Registered Users Trend';

    protected static ?string $maxHeight = '300px';
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
                    'label' => 'Registered users',
                    'data' => [1000, 1200, 1500, 1300, 1400, 1600, 1800], // Example data for registered users
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
