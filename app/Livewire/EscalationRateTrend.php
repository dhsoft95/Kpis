<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class EscalationRateTrend extends ApexChartWidget
{
    protected static ?string $chartId = 'escalationRateTrend';
    protected static ?string $heading = 'Escalation Rate Trend';

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Escalation Rate',
                    'data' => $data['escalationRates'],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                    'endingShape' => 'rounded',
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
                'title' => [
                    'text' => 'Escalation Rate (%)',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'min' => 0,
                'max' => 100,
            ],
            'colors' => ['#f59e0b'],
            'dataLabels' => [
                'enabled' => false,
            ],
            'fill' => [
                'opacity' => 1,
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

        $totalInteractions = array_fill(0, 30, 0);
        $escalatedCases = array_fill(0, 30, 0);
        $dates = [];

        for ($i = 0; $i < 30; $i++) {
            $dates[] = $startDate->copy()->addDays($i)->format('Y-m-d');
        }

        try {
            $response = Http::withBasicAuth(config('services.zendesk.username'), config('services.zendesk.token'))
                ->get("https://" . config('services.zendesk.subdomain') . ".zendesk.com/api/v2/ticket_metrics.json", [
                    'start_time' => $startDate->toIso8601String(),
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
//                Log::info('Zendesk API Response:', $responseData);

                $ticketMetrics = $responseData['ticket_metrics'] ?? [];

                foreach ($ticketMetrics as $metric) {
                    $createdAt = Carbon::parse($metric['created_at'])->format('Y-m-d');
                    $index = array_search($createdAt, $dates);

                    if ($index !== false) {
                        $totalInteractions[$index]++;

                        // Check if the ticket was escalated
                        if ($metric['reopens'] > 0 || $metric['replies'] > 3) {
                            $escalatedCases[$index]++;
                        }
                    }
                }
            } else {
                Log::error('Zendesk API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching data from Zendesk API', [
                'error' => $e->getMessage(),
            ]);
        }

        $escalationRates = array_map(function ($total, $escalated) {
            return $total > 0 ? round(($escalated / $total) * 100, 2) : 0;
        }, $totalInteractions, $escalatedCases);

        return [
            'dates' => $dates,
            'escalationRates' => $escalationRates,
        ];
    }
}
