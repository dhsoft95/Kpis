<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RegisteredChart extends ChartWidget
{
    protected static ?string $heading = 'Week-over-Week User Registration Growth';
    protected static ?string $maxHeight = '300px';

    protected function getFilters(): ?array
    {
        return [
            4 => '4 weeks',
            8 => '8 weeks',
            12 => '12 weeks',
            26 => '26 weeks',
        ];
    }

    protected function getData(): array
    {
        $weeks = $this->filter ?? 8;
        $data = $this->getWeekOverWeekGrowthRate($weeks);

        return [
            'datasets' => [
                [
                    'label' => 'Growth Rate (%)',
                    'data' => array_column($data, 'growth_rate'),
                    'backgroundColor' => array_map(function ($rate) {
                        return $rate >= 0 ? 'rgba(34, 197, 94, 0.6)' : 'rgba(239, 68, 68, 0.6)';
                    }, array_column($data, 'growth_rate')),
                    'borderColor' => array_map(function ($rate) {
                        return $rate >= 0 ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)';
                    }, array_column($data, 'growth_rate')),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_column($data, 'week'),
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
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.parsed.y.toFixed(2) + "%"; }',
                    ],
                ],
            ],
        ];
    }

    private function getWeekOverWeekGrowthRate($weeks = 8)
    {
        $data = [];
        for ($i = 0; $i < $weeks; $i++) {
            $endDate = now()->subWeeks($i);
            $startDate = $endDate->copy()->startOfWeek();

            $thisWeek = User::whereBetween('created_at', [$startDate, $endDate])->count();
            $lastWeek = User::whereBetween('created_at', [$startDate->copy()->subWeek(), $endDate->copy()->subWeek()])->count();

            $growthRate = $lastWeek > 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;

            $data[] = [
                'week' => $startDate->format('M d') . ' - ' . $endDate->format('M d'),
                'growth_rate' => round($growthRate, 2)
            ];
        }
        return array_reverse($data);
    }
}
