<?php

namespace App\Filament\Widgets\UserWidget;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RegisterdUsersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'registeredUsersChart';
    protected static ?string $loadingIndicator = 'Loading...';
    protected static ?string $heading = 'Registered Users Trend';

    protected int | string | array $columnSpan = 'full';

    protected function getContentHeight(): ?int
    {
        return 300;
    }

    protected function getOptions(): array
    {
        $userRegistrations = $this->getUserRegistrations();
        $weekLabels = $this->getWeekLabels($userRegistrations);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'parentHeightOffset' => 2,
                'stacked' => true,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Current Week',
                    'data' => array_column($userRegistrations, 'current'),
                ],
                [
                    'name' => 'Previous Week',
                    'data' => array_column($userRegistrations, 'previous'),
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'columnWidth' => '50%',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'legend' => [
                'show' => true,
                'horizontalAlign' => 'right',
                'position' => 'top',
                'fontFamily' => 'inherit',
                'markers' => [
                    'height' => 12,
                    'width' => 12,
                    'radius' => 12,
                    'offsetX' => -3,
                    'offsetY' => 2,
                ],
                'itemMargin' => [
                    'horizontal' => 5,
                ],
            ],
            'grid' => [
                'show' => true,
            ],
            'xaxis' => [
                'categories' => $weekLabels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'axisTicks' => [
                    'show' => false,
                ],
                'axisBorder' => [
                    'show' => false,
                ],
            ],
            'yaxis' => [
                'offsetX' => -16,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'min' => 0,
                'tickAmount' => 5,
            ],
            'fill' => [
                'type' => 'solid',
                'opacity' => 0.7,
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 1,
                'lineCap' => 'round',
            ],
            'colors' => ['#E0B22C', '#584408'],
        ];
    }

    protected function getUserRegistrations(): array
    {
        $startDate = DB::connection('mysql_second')
            ->table('users')
            ->min('created_at');

        $endDate = Carbon::now();

        $registrations = DB::connection('mysql_second')->table('users')
            ->select(DB::raw('YEARWEEK(created_at, 1) as week, COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get()
            ->keyBy('week')
            ->toArray();

        $data = [];
        $previousWeek = null;

        foreach ($registrations as $week => $registration) {
            $currentWeekCount = $registration->count;
            $previousWeekCount = $previousWeek ? $registrations[$previousWeek]->count : 0;

            $data[] = [
                'week' => $week,
                'current' => $currentWeekCount,
                'previous' => $previousWeekCount,
            ];

            $previousWeek = $week;
        }

        return $data;
    }

    protected function getWeekLabels(array $registrations): array
    {
        $labels = [];

        foreach ($registrations as $registration) {
            $weekNumber = $registration['week'];
            $year = substr($weekNumber, 0, 4);
            $week = substr($weekNumber, -2);

            $startOfWeek = Carbon::parse($year)->startOfYear()->addWeeks($week - 1)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();

            $labels[] = $startOfWeek->format('M d') . '-' . $endOfWeek->format('M d');
        }

        return $labels;
    }
}
