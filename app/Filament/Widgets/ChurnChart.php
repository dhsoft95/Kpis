<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ChurnChart extends ChartWidget
{


    protected static ?string $heading = 'Week-on-Week Churn Users';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $currentWeekStart = $currentWeekEnd->copy()->startOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();

        $currentWeekChurn = $this->getChurnCount($currentWeekStart, $currentWeekEnd);
        $previousWeekChurn = $this->getChurnCount($previousWeekStart, $currentWeekStart->subDay());

        $percentageChange = $this->calculatePercentageChange($previousWeekChurn, $currentWeekChurn);

        return [
            'datasets' => [
                [
                    'label' => 'Churn Users',
                    'data' => [$previousWeekChurn, $currentWeekChurn],
                    'backgroundColor' => ['#36A2EB', '#FF6384'],
                ],
            ],
            'labels' => [
                $previousWeekStart->format('M d') . ' - ' . $currentWeekStart->subDay()->format('M d'),
                $currentWeekStart->addDay()->format('M d') . ' - ' . $currentWeekEnd->format('M d'),
            ],
        ];
    }

    private function getChurnCount(Carbon $start, Carbon $end): int
    {
        return DB::connection('mysql_second')->table('users')
            ->leftJoin('tbl_transactions', function ($join) use ($end) {
                $join->on('users.phone_number', '=', 'tbl_transactions.sender_phone')
                    ->where('tbl_transactions.created_at', '>', DB::raw("DATE_SUB('{$end}', INTERVAL 30 DAY)"));
            })
            ->whereNull('tbl_transactions.sender_phone')
            ->where('users.created_at', '<=', $end)
            ->count();
    }

    private function calculatePercentageChange($oldValue, $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFooterWidgets(): array
    {
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $currentWeekStart = $currentWeekEnd->copy()->startOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();

        $currentWeekChurn = $this->getChurnCount($currentWeekStart, $currentWeekEnd);
        $previousWeekChurn = $this->getChurnCount($previousWeekStart, $currentWeekStart->subDay());

        $percentageChange = $this->calculatePercentageChange($previousWeekChurn, $currentWeekChurn);

        return [
            Stat::make('Week-on-Week Change', $percentageChange . '%')
                ->description($percentageChange >= 0 ? 'Increase in churn' : 'Decrease in churn')
                ->color($percentageChange >= 0 ? 'danger' : 'success')
                ->chart([
                    $previousWeekChurn,
                    $currentWeekChurn,
                ])
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
