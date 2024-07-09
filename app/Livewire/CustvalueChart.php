<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class CustvalueChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Transaction Analysis';

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
                    'label' => 'Average Transaction Value ($)',
                    'data' => [65, 59, 80, 81, 56, 55, 70, 68, 72, 75, 79, 85],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
