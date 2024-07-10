<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ChurnChart extends ChartWidget
{
    protected static ?string $heading = 'Churn Users Trend';
    protected static ?string $maxHeight = '300px';

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
        $filter = $this->filter; // Get the selected filter

        switch ($filter) {
            case 'today':
                $data = $this->getChurnDataByDay();
                $labels = $this->generateDateLabels('day');
                break;

            case 'week':
                $data = $this->getChurnDataByWeek();
                $labels = $this->generateDateLabels('week');
                break;

            case 'month':
                $data = $this->getChurnDataByMonth();
                $labels = $this->generateDateLabels('month');
                break;

            case 'year':
                $data = $this->getChurnDataByYear();
                $labels = $this->generateDateLabels('year');
                break;

            default:
                $data = [];
                $labels = [];
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Churned users',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getChurnDataByDay(): array
    {
        $days = Carbon::now()->subDays(7)->daysUntil(Carbon::now());

        return $days->map(function ($day) {
            return DB::table('users')
                ->whereNotIn('phone_number', function ($query) {
                    $query->select('sender_phone')
                        ->from('tbl_transactions');
                })
                ->whereDate('created_at', $day)
                ->count();
        })->toArray();
    }

    private function getChurnDataByWeek(): array
    {
        $weeks = collect();
        for ($i = 6; $i >= 0; $i--) {
            $weeks->push([
                Carbon::now()->startOfWeek()->subWeeks($i),
                Carbon::now()->endOfWeek()->subWeeks($i),
            ]);
        }

        return $weeks->map(function ($week) {
            [$start, $end] = $week;

            return DB::table('users')
                ->whereNotIn('phone_number', function ($query) {
                    $query->select('sender_phone')
                        ->from('tbl_transactions');
                })
                ->whereBetween('created_at', [$start, $end])
                ->count();
        })->toArray();
    }

    private function getChurnDataByMonth(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push([
                Carbon::now()->startOfMonth()->subMonths($i),
                Carbon::now()->endOfMonth()->subMonths($i),
            ]);
        }

        return $months->map(function ($month) {
            [$start, $end] = $month;

            return DB::table('users')
                ->whereNotIn('phone_number', function ($query) {
                    $query->select('sender_phone')
                        ->from('tbl_transactions');
                })
                ->whereBetween('created_at', [$start, $end])
                ->count();
        })->toArray();
    }

    private function getChurnDataByYear(): array
    {
        $years = collect();
        for ($i = 4; $i >= 0; $i--) {
            $years->push([
                Carbon::now()->startOfYear()->subYears($i),
                Carbon::now()->endOfYear()->subYears($i),
            ]);
        }

        return $years->map(function ($year) {
            [$start, $end] = $year;

            return DB::table('users')
                ->whereNotIn('phone_number', function ($query) {
                    $query->select('sender_phone')
                        ->from('tbl_transactions');
                })
                ->whereBetween('created_at', [$start, $end])
                ->count();
        })->toArray();
    }

    private function generateDateLabels(string $period): array
    {
        switch ($period) {
            case 'day':
                return Carbon::now()->subDays(7)->daysUntil(Carbon::now())->map->toDateString()->toArray();

            case 'week':
                return collect(range(0, 6))->map(fn($i) => 'Week ' . (7 - $i))->toArray();

            case 'month':
                return Carbon::now()->subMonths(5)->monthsUntil(Carbon::now())->map(fn($date) => $date->format('F'))->toArray();

            case 'year':
                return Carbon::now()->subYears(4)->yearsUntil(Carbon::now())->map->year->toArray();

            default:
                return [];
        }
    }
}
