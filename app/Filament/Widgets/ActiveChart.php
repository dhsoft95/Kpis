<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActiveChart extends ChartWidget
{
    protected static ?string $heading = 'User Activity Trend (Week over Week)';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '3600s'; // Update every hour

    protected function getData(): array
    {
        $data = $this->getUserCounts();

        return [
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => $data['activeCounts'],
//                    'backgroundColor' => '#33d096',
//                    'borderColor' => '#33d096',
                ],
                [
                    'label' => 'Inactive Users',
                    'data' => $data['inactiveCounts'],
                    'backgroundColor' => '#e52f42',
                    'borderColor' => '#e52f42',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }
    protected function getUserCounts(): array
    {
        $activeCounts = [];
        $inactiveCounts = [];
        $labels = [];

        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $activeCount = DB::table('live.users')
                ->where('is_active', '1')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $inactiveCount = DB::table('live.users')
                ->where('is_active', '0')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $activeCounts[] = $activeCount;
            $inactiveCounts[] = $inactiveCount;
            $labels[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');
        }

        return [
            'activeCounts' => $activeCounts,
            'inactiveCounts' => $inactiveCounts,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
