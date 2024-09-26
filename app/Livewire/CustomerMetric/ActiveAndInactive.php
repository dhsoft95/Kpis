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
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            $startDate = $endDate->copy()->startOfWeek();

            $weeks[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');

            $totalUsers = DB::connection('mysql_second')->table('users')
                ->where('created_at', '<=', $endDate)
                ->count();

            $activeUsersCount = $this->getActiveUsersCount($startDate, $endDate);

            $activeUsers[] = $activeUsersCount;
            $inactiveUsers[] = $totalUsers - $activeUsersCount;
        }

        return [
            'weeks' => $weeks,
            'active' => $activeUsers,
            'inactive' => $inactiveUsers,
        ];
    }

    private function getActiveUsersCount(Carbon $startDate, Carbon $endDate): int
    {
        return DB::connection('mysql_second')
            ->table('tbl_simba_transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['deposited', 'sent', 'received'])
            ->distinct('user_id')
            ->count('user_id');
    }
}
