<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class EscalationRateTrend extends ApexChartWidget
{
    protected static ?string $chartId = 'escalationRateTrendWidget';

    protected static ?string $heading = 'Escalation Rate Trend (Week-on-Week Comparison)';

    protected static ?int $contentHeight = 300;

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
                'title' => [
                    'text' => 'Escalation Rate (%)',
                ],
                'min' => 0,
                'max' => 100,
            ],
            'colors' => ['#E0B22C', '#584408'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'markers' => [
                'size' => 4,
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

        $escalations = DB::table('tickets')
            ->select(
                DB::raw('DATE(ticket_created_at) as date'),
                DB::raw('COUNT(*) as total_tickets'),
                DB::raw('SUM(CASE WHEN priority = "urgent" THEN 1 ELSE 0 END) as escalated_tickets')
            )
            ->whereBetween('ticket_created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $thisWeek = array_fill(0, 7, 0);
        $lastWeek = array_fill(0, 7, 0);

        foreach ($escalations as $escalation) {
            $date = Carbon::parse($escalation->date);
            $dayOfWeek = $date->dayOfWeekIso - 1;

            $rate = $escalation->total_tickets > 0
                ? ($escalation->escalated_tickets / $escalation->total_tickets) * 100
                : 0;
            $rate = round($rate, 2);

            if ($date->greaterThanOrEqualTo(Carbon::now()->startOfWeek())) {
                $thisWeek[$dayOfWeek] = $rate;
            } else {
                $lastWeek[$dayOfWeek] = $rate;
            }
        }

        return [
            'thisWeek' => array_values($thisWeek),
            'lastWeek' => array_values($lastWeek),
        ];
    }
}
