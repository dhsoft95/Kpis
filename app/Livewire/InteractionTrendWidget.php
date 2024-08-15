<?php

namespace App\Livewire;

use App\Models\UserInteraction;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;

class InteractionTrendWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'interactionTrendWidget';
    protected static ?string $heading = 'Interaction Trends';

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Inquiry',
                    'data' => $data['inquiry'],
                ],
                [
                    'name' => 'Complaint',
                    'data' => $data['complaint'],
                ],
                [
                    'name' => 'Request',
                    'data' => $data['request'],
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
            ],
            'colors' => ['#584408', '#E0B22C', '#F5E5B9'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'legend' => [
                'position' => 'top',
            ],
        ];
    }

    private function getData(): array
    {
        $endDate = now();
        $startDate = now()->subDays(30);

        $interactions = UserInteraction::select('type', DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereIn('type', ['inquiry', 'complaint', 'request'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type', 'date')
            ->orderBy('date')
            ->get();

        $dates = $interactions->pluck('date')->unique()->sort()->values()->toArray();

        $data = [
            'inquiry' => array_fill(0, count($dates), 0),
            'complaint' => array_fill(0, count($dates), 0),
            'request' => array_fill(0, count($dates), 0),
            'dates' => $dates,
        ];

        foreach ($interactions as $interaction) {
            $index = array_search($interaction->date, $dates);
            $data[$interaction->type][$index] = $interaction->count;
        }

        return $data;
    }
}
