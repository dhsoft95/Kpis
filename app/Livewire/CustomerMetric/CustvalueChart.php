<?php

namespace App\Livewire\CustomerMetric;

use App\Models\trans;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class CustvalueChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Transaction Analysis';
    public ?string $filter = 'week';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This week',
            'last_week' => 'Last week',
            '2_weeks' => 'Last 2 weeks',
            '4_weeks' => 'Last 4 weeks',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $endDate = now()->endOfWeek();
        $startDate = match ($activeFilter) {
            'week' => now()->startOfWeek(),
            'last_week' => now()->subWeek()->startOfWeek(),
            '2_weeks' => now()->subWeeks(2)->startOfWeek(),
            '4_weeks' => now()->subWeeks(4)->startOfWeek(),
            default => now()->startOfWeek(),
        };

        $data = Trend::model(trans::class)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Transactions',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('D')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
