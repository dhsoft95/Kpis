<?php

namespace App\Livewire;

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
            'colors' => ['#f59e0b'], // Amber color for escalation
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

        $dailyStats = UserInteraction::selectRaw('DATE(created_at) as date,
                                                 COUNT(*) as total_interactions,
                                                 SUM(CASE WHEN escalation_level > 0 THEN 1 ELSE 0 END) as escalated_interactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $escalationRates = [];

        foreach ($dailyStats as $stat) {
            $dates[] = $stat->date;
            $escalationRates[] = $stat->total_interactions > 0
                ? round(($stat->escalated_interactions / $stat->total_interactions) * 100, 2)
                : 0;
        }

        return [
            'dates' => $dates,
            'escalationRates' => $escalationRates,
        ];
    }
}
