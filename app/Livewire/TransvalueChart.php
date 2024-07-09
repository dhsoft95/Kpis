<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class TransvalueChart extends ChartWidget
{
    protected static ?string $heading = 'Transaction Volume Chart';

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
                    'label' => 'Number of Transactions',
                    'data' => [120, 150, 180, 210, 190, 240, 280, 300, 320, 350, 380, 400],
                    'borderColor' => '#4299e1',
                    'backgroundColor' => 'rgba(66, 153, 225, 0.5)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
