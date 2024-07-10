<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActiveUserTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Active Users Trend';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '3600s'; // Update every hour

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'week';
        $data = $this->getActiveUserCounts($filter);

        return [
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => $data['counts'],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getActiveUserCounts(string $filter): array
    {
        $counts = [];
        $labels = [];

        switch ($filter) {
            case 'today':
                $data = $this->getTodayData();
                break;
            case 'week':
                $data = $this->getWeekData();
                break;
            case 'month':
                $data = $this->getMonthData();
                break;
            case 'year':
                $data = $this->getYearData();
                break;
            default:
                $data = $this->getWeekData();
        }

        return $data;
    }

    private function getTodayData(): array
    {
        $counts = [];
        $labels = [];

        for ($i = 0; $i < 24; $i++) {
            $startHour = Carbon::today()->addHours($i);
            $endHour = $startHour->copy()->addHour();

            $count = DB::table('live.users')
                ->where('is_active', '1')
                ->whereBetween('created_at', [$startHour, $endHour])
                ->count();

            $counts[] = $count;
            $labels[] = $startHour->format('H:i');
        }

        return ['counts' => $counts, 'labels' => $labels];
    }

    private function getWeekData(): array
    {
        $counts = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $count = DB::table('live.users')
                ->where('is_active', '1')
                ->whereDate('created_at', $date)
                ->count();

            $counts[] = $count;
            $labels[] = $date->format('D');
        }

        return ['counts' => $counts, 'labels' => $labels];
    }

    private function getMonthData(): array
    {
        $counts = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $count = DB::table('live.users')
                ->where('is_active', '1')
                ->whereDate('created_at', $date)
                ->count();

            $counts[] = $count;
            $labels[] = $date->format('M d');
        }

        return ['counts' => $counts, 'labels' => $labels];
    }

    private function getYearData(): array
    {
        $counts = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $count = DB::table('live.users')
                ->where('is_active', '1')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $counts[] = $count;
            $labels[] = $startDate->format('M');
        }

        return ['counts' => $counts, 'labels' => $labels];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
