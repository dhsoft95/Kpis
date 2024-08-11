<?php

namespace App\Livewire;

use App\Models\EscalatedCase;
use App\Models\UserInteraction;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EscalationRateTrend extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'escalationRateTrend';
    protected static ?string $heading = 'Escalation Rate Trend';
    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Escalation Rate',
                    'data' => $data['escalationRates'],
                ],
            ],
            'xaxis' => [
                'categories' => $data['dates'],
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
                'title' => [
                    'text' => 'Escalation Rate (%)',
                ],
                'min' => 0,
                'max' => 100,
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shadeIntensity' => 1,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.9,
                    'stops' => [0, 90, 100],
                ],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) { return val + "%"; }',
                ],
            ],
        ];
    }

    private function getData(): array
    {
        $endDate = now();
        $startDate = now()->subDays(30);

        // Get total interactions per day
        $totalInteractions = UserInteraction::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Get escalated cases per day
        $escalatedCases = EscalatedCase::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $dates = array_keys($totalInteractions);
        $escalationRates = [];

        foreach ($dates as $date) {
            $total = $totalInteractions[$date] ?? 0;
            $escalated = $escalatedCases[$date] ?? 0;
            $escalationRates[] = $total > 0 ? round(($escalated / $total) * 100, 2) : 0;
        }

        return [
            'dates' => $dates,
            'escalationRates' => $escalationRates,
        ];
    }
}
