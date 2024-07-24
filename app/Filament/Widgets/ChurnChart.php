<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChurnChart extends ChartWidget
{
    protected static ?string $heading = 'Churn Users';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Default filter set to 'week'
        $filter = $this->filter ?? 'week';

        switch ($filter) {
            case 'month':
                $start = Carbon::now()->subMonths(3)->startOfMonth();
                break;
            case 'quarter':
                $start = Carbon::now()->subQuarter()->startOfQuarter();
                break;
            case 'year':
                $start = Carbon::now()->subYear()->startOfYear();
                break;
            case 'week':
            default:
                $start = Carbon::now()->subWeeks(5)->startOfWeek();
                break;
        }

        $end = Carbon::now()->endOfDay(); // Ensure to count only up to today
        $churnData = $this->getChurnData($start, $end, $filter);

        return [
            'datasets' => [
                [
                    'label' => 'Churn Users',
                    'data' => $churnData['data'],
                    'backgroundColor' => ['#007bff', '#28a745', '#dc3545', '#ffc107', '#6c757d'], // Simple colors
                ],
            ],
            'labels' => $churnData['labels'],
        ];
    }

    private function getChurnData(Carbon $start, Carbon $end, $filter): array
    {
        $churnCounts = [];
        $labels = [];
        $date = $start->copy();
        $interval = $filter === 'week' ? '1 week' : ($filter === 'month' ? '1 month' : '3 months');

        while ($date->lt($end)) {
            $periodStart = $date->copy();
            $periodEnd = $date->copy()->add($interval)->subDay();

            // Ensure the period end does not exceed today
            if ($periodEnd->gt(Carbon::now())) {
                $periodEnd = Carbon::now();
            }

            $churnCounts[] = $this->getChurnCount($periodStart, $periodEnd);
            $labels[] = $periodStart->format('M d') . ' - ' . $periodEnd->format('M d');

            $date->add($interval);
        }

        return [
            'data' => $churnCounts,
            'labels' => $labels,
        ];
    }

    private function getChurnCount(Carbon $start, Carbon $end): int
    {
        return DB::connection('mysql_second')->table('users')
            ->whereNotIn('users.phone_number', function ($query) use ($end) {
                $query->select('tbl_transactions.sender_phone')
                    ->from('tbl_transactions')
                    ->where('tbl_transactions.created_at', '>', DB::raw("DATE_SUB('{$end}', INTERVAL 30 DAY)"));
            })
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

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 5 Weeks',
            'month' => 'Last 3 Months',
            'quarter' => 'Last Quarter',
            'year' => 'Last Year',
        ];
    }

    protected function getFooterWidgets(): array
    {
        // Default filter set to 'week'
        $filter = $this->filter ?? 'week';
        $interval = $filter === 'week' ? '1 week' : ($filter === 'month' ? '1 month' : '3 months');

        switch ($filter) {
            case 'month':
                $start = Carbon::now()->subMonths(3)->startOfMonth();
                break;
            case 'quarter':
                $start = Carbon::now()->subQuarter()->startOfQuarter();
                break;
            case 'year':
                $start = Carbon::now()->subYear()->startOfYear();
                break;
            case 'week':
            default:
                $start = Carbon::now()->subWeeks(5)->startOfWeek();
                break;
        }

        $end = Carbon::now()->endOfDay(); // Ensure to count only up to today
        $currentPeriodChurn = $this->getChurnCount($start, $end);
        $previousPeriodChurn = $this->getChurnCount($start->copy()->sub($interval), $start->copy()->subDay());

        $percentageChange = $this->calculatePercentageChange($previousPeriodChurn, $currentPeriodChurn);

        return [
            Stat::make('Change', $percentageChange . '%')
                ->description($percentageChange >= 0 ? 'Increase in churn' : 'Decrease in churn')
                ->color($percentageChange >= 0 ? 'danger' : 'success')
                ->chart([
                    $previousPeriodChurn,
                    $currentPeriodChurn,
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
