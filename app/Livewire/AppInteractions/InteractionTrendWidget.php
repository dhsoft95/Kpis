<?php

namespace App\Livewire\AppInteractions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class InteractionTrendWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'interactionTrendWidget';

    protected static ?string $heading = 'Interaction Trends (Last 10 Days)';



    protected static ?int $contentHeight = 300;

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
                'zoom' => [
                    'enabled' => false,
                ],
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
                'title' => [
                    'text' => 'Number of Interactions',
                ],
            ],
            'colors' => ['#584408', '#E0B22C', '#F5E5B9'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'center',
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'legend' => [
                            'position' => 'bottom',
                            'offsetY' => 10,
                        ],
                    ],
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
            ],
        ];
    }

    private function getData(): array
    {
        $endDate = now();
        $startDate = now()->subDays(9);

        $data = [
            'inquiry' => array_fill(0, 10, 0),
            'complaint' => array_fill(0, 10, 0),
            'request' => array_fill(0, 10, 0),
            'dates' => [],
        ];

        for ($i = 0; $i < 10; $i++) {
            $data['dates'][] = $startDate->copy()->addDays($i)->format('M d');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(config('services.zendesk.username') . '/token:' . config('services.zendesk.token')),
            ])->get("https://" . config('services.zendesk.subdomain') . ".zendesk.com/api/v2/search.json", [
                'query' => 'type:ticket created_between:' . $startDate->toIso8601String() . '..' . $endDate->toIso8601String(),
                'group_by' => 'created_at,tags',
                'group_by_type' => 'day',
                'sort_by' => 'created_at',
                'sort_order' => 'asc',
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $results = $responseData['results'] ?? [];

                foreach ($results as $result) {
                    $createdAt = Carbon::parse($result['created_at']);
                    $dayIndex = $createdAt->diffInDays($startDate);

                    if ($dayIndex >= 0 && $dayIndex < 10) {
                        $count = $result['count'] ?? 0;
                        $tags = $result['tags'] ?? [];

                        if (in_array('enquiries', $tags)) {
                            $data['inquiry'][$dayIndex] += $count;
                        } elseif (in_array('complaint', $tags)) {
                            $data['complaint'][$dayIndex] += $count;
                        } elseif (in_array('request', $tags)) {
                            $data['request'][$dayIndex] += $count;
                        } else {
                            // Default to 'request' if no matching tag is found
                            $data['request'][$dayIndex] += $count;
                        }
                    }
                }
            } else {
                Log::error('Zendesk API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $response->effectiveUri(),
                    'headers' => $response->headers(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching data from Zendesk API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $data;
    }


}
