<?php

namespace App\Livewire\AppInteractions;

use Illuminate\Support\Facades\Log;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InteractionTrendWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'interactionTrendWidget';

    protected static ?string $heading = 'Interaction Trends (Week-on-Week Comparison)';

    protected static ?int $contentHeight = 300;

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'stackType' => '100%',
                'stacked' => true,
            ],
            'series' => [
                [
                    'name' => 'This Week',
                    'data' => $data['thisWeek'],
                ],
                [
                    'name' => 'Last Week',
                    'data' => $data['lastWeek'],
                ],
            ],
            'xaxis' => [
                'categories' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
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
            'colors' => ['#E0B22C', '#584408'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => true,
                    'columnWidth' => '55%',
                ],
            ],
            'legend' => [
                'position' => 'top',
            ],
        ];
    }

    private function getData(): array
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(13)->startOfDay();

        $interactions = DB::table('tickets')
            ->select(DB::raw('DATE(ticket_created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('ticket_created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get();

        $thisWeek = array_fill(0, 7, 0);
        $lastWeek = array_fill(0, 7, 0);

        foreach ($interactions as $interaction) {
            $date = Carbon::parse($interaction->date);
            $dayOfWeek = $date->dayOfWeekIso - 1; // 0 for Monday, 6 for Sunday

            if ($date->greaterThanOrEqualTo(Carbon::now()->startOfWeek())) {
                $thisWeek[$dayOfWeek] = $interaction->count;
            } else {
                $lastWeek[$dayOfWeek] = $interaction->count;
            }
        }

        return [
            'thisWeek' => array_values($thisWeek),
            'lastWeek' => array_values($lastWeek),
        ];
    }
}
