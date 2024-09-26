<?php

namespace App\Livewire;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TotalAmountByTransactionType extends ApexChartWidget
{
    protected static ?string $chartId = 'totalAmountByTransactionType';

    protected static ?string $heading = 'Total Amount by Transaction Type';

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 310,
                'toolbar' => ['show' => true],
                'animations' => ['enabled' => true, 'speed' => 500],
            ],
            'series' => $data['amounts'],
            'labels' => $data['types'],
            'legend' => [
                'position' => 'right',
                'horizontalAlign' => 'right',
                'floating' => true,
                'fontSize' => '14px',
                'fontFamily' => 'inherit',
                'fontWeight' => 400,
            ],
            'tooltip' => [
                'enabled' => true,
                'y' => [
                    'formatter' => 'function (val) { return val.toLocaleString("en-US", { style: "currency", currency: "TZS", minimumFractionDigits: 2, maximumFractionDigits: 2 }) }',
                ],
                'custom' => 'function({ series, seriesIndex, dataPointIndex, w }) {
                    var value = series[seriesIndex];
                    var label = w.globals.labels[seriesIndex];
                    return "<div class=\'apexcharts-tooltip-title\'>" + label + ": " +
                           value.toLocaleString("en-US", { style: "currency", currency: "TZS", minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</div>";
                }',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '100%',
                        'labels' => [
                            'show' => true,
                            'name' => [
                                'show' => true,
                                'formatter' => 'function (val) { return val.charAt(0).toUpperCase() + val.slice(1).toLowerCase() }',
                            ],
                            'value' => [
                                'show' => true,
                                'formatter' => 'function (val) { return val.toLocaleString("en-US", { style: "currency", currency: "TZS", minimumFractionDigits: 2, maximumFractionDigits: 2 }) }',
                            ],
                            'total' => [
                                'show' => true,
                                'label' => 'Total',
                                'formatter' => 'function (w) { return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString("en-US", { style: "currency", currency: "TZS", minimumFractionDigits: 2, maximumFractionDigits: 2 }) }',
                            ],
                        ],
                    ],
                ],
            ],
            'colors' => ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A', '#26a69a', '#D10CE8'],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => ['height' => 300],
                        'legend' => ['position' => 'bottom'],
                    ],
                ],
            ],
        ];
    }

    private function getData(): array
    {
        $result = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->select('transaction_type', DB::raw('ROUND(SUM(credit_amount + debit_amount), 2) as total_amount'))
            ->groupBy('transaction_type')
            ->orderByDesc('total_amount')
            ->get();

        $types = $result->pluck('transaction_type')->map(function($type) {
            return Str::ucfirst(Str::lower($type));
        })->toArray();

        $amounts = $result->pluck('total_amount')->map(function($amount) {
            return (float) $amount;
        })->toArray();

        // Calculate the total sum
        $totalSum = array_sum($amounts);

        // Filter out types with very small percentages (e.g., less than 1%)
        $filteredTypes = [];
        $filteredAmounts = [];
        $otherAmount = 0;

        foreach ($types as $index => $type) {
            $percentage = ($amounts[$index] / $totalSum) * 100;
            if ($percentage >= 1) {
                $filteredTypes[] = $type;
                $filteredAmounts[] = $amounts[$index];
            } else {
                $otherAmount += $amounts[$index];
            }
        }

        // Add "Other" category if there are small amounts
        if ($otherAmount > 0) {
            $filteredTypes[] = 'Other';
            $filteredAmounts[] = $otherAmount;
        }

        return [
            'types' => $filteredTypes,
            'amounts' => $filteredAmounts,
        ];
    }
}
