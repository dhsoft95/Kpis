<?php

namespace App\Filament\Widgets;

use App\Models\AppUser;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class RegisterdUsersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'registeredUsersChart';
    protected static ?string $loadingIndicator = 'Loading...';
    protected static ?string $heading = 'Registered Users Trend';

    protected int | string | array $columnSpan = 'full';
//    protected static ?int $contentHeight = 440;
    protected static ?int $contentHeight = 200;



    protected function getOptions(): array
    {
        $userRegistrations = $this->getUserRegistrations();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 200,
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
                'categories' => $this->getWeekLabels(),
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
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#d97706', '#c2410c'],
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 1,
                'lineCap' => 'round',
            ],
            'colors' => ['#f59e0b', '#ea580c'],
        ];
    }


    /**
     * Get user registrations for the last 12 weeks.
     */
    protected function getUserRegistrations(): array
    {
        $registrations = DB::connection('mysql_second')->table('users')
            ->select(DB::raw('YEARWEEK(created_at, 1) as week, COUNT(*) as count'))
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->take(13)
            ->get()
            ->toArray();

        $data = [];

        for ($i = 1; $i < count($registrations); $i++) {
            $current = $registrations[$i]->count;
            $previous = $registrations[$i - 1]->count;

            $data[] = [
                'current' => $current,
                'previous' => $previous,
            ];
        }

        return array_reverse(array_slice($data, 0, 6));
    }

    /**
     * Get week labels for the last 12 weeks.
     */
    protected function getWeekLabels(): array
    {
        $endDate = Carbon::now()->startOfWeek();
        $labels = [];

        for ($i = 0; $i < 6; $i++) {
            $startDate = $endDate->copy()->subDays(6);
            $labels[] = $startDate->format('M d') . '-' . $endDate->format('M d');
            $endDate->subWeek();
        }

        return array_reverse($labels);
    }

}
