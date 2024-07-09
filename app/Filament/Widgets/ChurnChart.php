<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ChurnChart extends ChartWidget
{
    protected static ?string $heading = 'Churn Users Trend';
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
                    'label' => 'Churned users',
                    'data' => [100, 120, 150, 140, 130, 160, 150], // Example data for churned users
                ],
            ],
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7'], // Example labels for the data
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
