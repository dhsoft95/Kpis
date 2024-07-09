<?php

namespace App\Filament\Widgets;

use App\Models\AppUser;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RegisterdUsersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'registeredUsersChart';
    protected static ?string $heading = 'Registered Users Week-over-Week Growth';

    protected function getFilters(): ?array
    {
        return [
            4 => 'Last 4 weeks',
            8 => 'Last 8 weeks',
            12 => 'Last 12 weeks',
        ];
    }

    protected function getOptions(): array
    {
        $weeks = $this->filter ?? 8;
        $data = $this->getWeekOverWeekGrowthRate($weeks);

        // Debug: Log the data
        Log::info('Week-over-week growth data:', $data);

        if (empty($data)) {
            return $this->getNoDataOptions();
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 480,
            ],
            'series' => [
                [
                    'name' => 'Growth Rate (%)',
                    'data' => array_column($data, 'growth_rate'),
                ],
            ],
            'xaxis' => [
                'categories' => array_column($data, 'week'),
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
                    'formatter' => 'function(value) { return value.toFixed(2) + "%" }',
                ],
            ],
            'colors' => ['#10B981'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                    'colors' => [
                        'ranges' => [
                            [
                                'from' => -100,
                                'to' => 0,
                                'color' => '#EF4444'
                            ],
                            [
                                'from' => 0,
                                'to' => 100,
                                'color' => '#10B981'
                            ]
                        ]
                    ]
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'formatter' => 'function(val) { return val.toFixed(2) + "%" }',
            ],
        ];
    }

    private function getWeekOverWeekGrowthRate($weeks = 8)
    {
        $data = [];
        for ($i = 0; $i < $weeks; $i++) {
            $endDate = now()->subWeeks($i);
            $startDate = $endDate->copy()->startOfWeek();

            $thisWeek = AppUser::whereBetween('created_at', [$startDate, $endDate])->count();
            $lastWeek = AppUser::whereBetween('created_at', [$startDate->copy()->subWeek(), $endDate->copy()->subWeek()])->count();

            // Debug: Log the counts
            Log::info("Week {$i} - This week: {$thisWeek}, Last week: {$lastWeek}");

            $growthRate = $lastWeek > 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;

            $data[] = [
                'week' => $startDate->format('M d') . ' - ' . $endDate->format('M d'),
                'growth_rate' => round($growthRate, 2)
            ];
        }
        return array_reverse($data);
    }

    private function getNoDataOptions(): array
    {
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 480,
            ],
            'series' => [
                [
                    'name' => 'Growth Rate (%)',
                    'data' => [0],
                ],
            ],
            'xaxis' => [
                'categories' => ['No Data'],
            ],
            'title' => [
                'text' => 'No data available',
                'align' => 'center',
            ],
        ];
    }
}
