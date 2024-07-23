<?php

namespace App\Livewire;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UserGenders extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'userGenders';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'User Genders';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Dummy data
        $maleCount = 65;
        $femaleCount = 35;

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 200,
            ],
            'series' => [$maleCount, $femaleCount],
            'labels' => ['Male', 'Female'],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
