<?php

namespace App\Livewire\CustomerMetric;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChurnUsers extends ApexChartWidget
{
    protected static ?string $chartId = 'churnUsers';
    protected static ?string $heading = 'Churn and Retained Users';

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'stacked' => true,
                'stackType' => '100%',
            ],
            'series' => [
                [
                    'name' => 'Churn Users',
                    'data' => $data['churn'],
                ],
                [
                    'name' => 'Retained Users',
                    'data' => $data['retained'],
                ],
            ],
            'xaxis' => [
                'categories' => $data['weeks'],
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
            'dataLabels' => [
                'enabled' => true,
            ],
            'legend' => [
                'position' => 'top',
            ],
            'title' => [
                'text' => 'Churn vs Retained Users (Last 5 Weeks)',
                'align' => 'center',
            ],
        ];
    }

    private function getData(): array
    {
        $weeks = [];
        $churnUsers = [];
        $retainedUsers = [];

        for ($i = 4; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeks[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');

            $totalUsers = DB::connection('mysql_second')->table('users')
                ->where('created_at', '<=', $endDate)
                ->count();

            $churnCount = DB::connection('mysql_second')->table('users')
                ->leftJoin('tbl_simba_transactions', 'users.id', '=', 'tbl_simba_transactions.user_id')
                ->where('users.created_at', '<=', $endDate)
                ->where(function ($query) use ($endDate) {
                    $query->whereNull('tbl_simba_transactions.created_at')
                        ->orWhere('tbl_simba_transactions.created_at', '<=', $endDate->copy()->subDays(30));
                })
                ->distinct('users.id')
                ->count('users.id');

            $churnUsers[] = $churnCount;
            $retainedUsers[] = $totalUsers - $churnCount;
        }

        return [
            'weeks' => $weeks,
            'churn' => $churnUsers,
            'retained' => $retainedUsers,
        ];
    }
}
