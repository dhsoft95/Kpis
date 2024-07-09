<?php

namespace App\Filament\Widgets;

use App\Models\AppUser;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class RegisteredUsersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'registeredUsersChart';
    protected static ?string $heading = 'Registered Users Trend';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This week',
            'last_week' => 'Last week',
            'two_weeks' => 'Last 2 weeks',
            'month' => 'This month',
            'growth_4' => 'Growth (4 weeks)',
            'growth_8' => 'Growth (8 weeks)',
        ];
    }

    protected function getOptions(): array
    {
        $activeFilter = $this->filter ?? 'two_weeks';

        if (str_starts_with($activeFilter, 'growth_')) {
            return $this->getGrowthChartOptions($activeFilter);
        }

        $data = $this->getWeekOnWeekData($activeFilter);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 480,
            ],
            'series' => [
                [
                    'name' => 'This Week',
                    'data' => $data['current']->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                ],
                [
                    'name' => 'Previous Week',
                    'data' => $data['previous']->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
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
            'colors' => ['#f59e0b', '#60a5fa'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }

    private function getGrowthChartOptions($filter)
    {
        $weeks = $filter === 'growth_4' ? 4 : 8;
        $data = $this->getWeekOverWeekGrowthRate($weeks);

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

    private function getWeekOnWeekData($filter)
    {
        $endDate = match ($filter) {
            'week' => now(),
            'last_week' => now()->subWeek(),
            'two_weeks' => now(),
            'month' => now()->endOfMonth(),
        };

        $startDate = match ($filter) {
            'week' => $endDate->copy()->startOfWeek(),
            'last_week' => $endDate->copy()->subWeek()->startOfWeek(),
            'two_weeks' => $endDate->copy()->subWeeks(2)->startOfWeek(),
            'month' => $endDate->copy()->startOfMonth(),
        };

        $currentWeekData = Trend::model(User::class)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->count();

        $previousWeekData = Trend::model(User::class)
            ->between(
                start: $startDate->copy()->subWeek(),
                end: $endDate->copy()->subWeek(),
            )
            ->perDay()
            ->count();

        return [
            'current' => $currentWeekData,
            'previous' => $previousWeekData,
        ];
    }

    private function getWeekOverWeekGrowthRate($weeks = 8)
    {
        $data = [];
        for ($i = 0; $i < $weeks; $i++) {
            $endDate = now()->subWeeks($i)->endOfWeek();
            $startDate = $endDate->copy()->startOfWeek();

            $thisWeek = AppUser::whereBetween('created_at', [$startDate, $endDate])->count();
            $lastWeek = AppUser::whereBetween('created_at', [$startDate->copy()->subWeek(), $endDate->copy()->subWeek()])->count();

            $growthRate = $lastWeek > 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;

            $data[] = [
                'week' => $startDate->format('M d') . ' - ' . $endDate->format('M d'),
                'growth_rate' => round($growthRate, 2)
            ];
        }
        return array_reverse($data);
    }
}
