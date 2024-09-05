<?php

namespace App\Livewire\CustomerMetric;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActiveAndInactive extends ApexChartWidget
{
    protected static ?string $chartId = 'activeAndInactive';
    protected static ?string $heading = 'Active and Inactive Users';

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
                    'name' => 'Active Users',
                    'data' => $data['active'],
                ],
                [
                    'name' => 'Inactive Users',
                    'data' => $data['inactive'],
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
                'enabled' => false,
            ],
            'legend' => [
                'position' => 'top',
            ],
            'title' => [
                'text' => 'Active vs Inactive Users (Last 5 Weeks)',
                'align' => 'center',
            ],
        ];
    }

    private function getData(): array
    {
        $weeks = [];
        $activeUsers = [];
        $inactiveUsers = [];

        for ($i = 4; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeks[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');
            $totalUsers = DB::connection('mysql_second')->table('users')
                ->where('created_at', '<=', $endDate)
                ->count();
            $activeUsersCount = DB::connection('mysql_second')->table('users')
                ->join('tbl_simba_transactions', 'users.id', '=', 'tbl_simba_transactions.user_id')
                ->where('users.created_at', '<=', $endDate)
                ->where('tbl_simba_transactions.created_at', '>=', $startDate)
                ->where('tbl_simba_transactions.created_at', '<=', $endDate)
                ->distinct('users.id')
                ->count('users.id');

            $activeUsers[] = $activeUsersCount;
            $inactiveUsers[] = $totalUsers - $activeUsersCount;
        }

        return [
            'weeks' => $weeks,
            'active' => $activeUsers,
            'inactive' => $inactiveUsers,
        ];
    }
}
