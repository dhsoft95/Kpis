<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActiveChart extends ChartWidget
{
    protected static ?string $heading = 'Active Users Trend (Week over Week)';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '3600s'; // Update every hour

    protected function getData(): array
    {
        $data = $this->getActiveUserCounts();

        return [
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => $data['counts'],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getActiveUserCounts(): array
    {
        $counts = [];
        $labels = [];

        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $count = DB::table(DB::table('Users'))
                ->where('is_active', '1')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $counts[] = $count;
            $labels[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');
        }

        return [
            'counts' => $counts,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
