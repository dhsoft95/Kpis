<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChurnChart extends ChartWidget
{
    protected static ?string $heading = 'Churn Users Comparison';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    // Default filter value
    protected string $filter = 'four_weeks';

    protected function getData(): array
    {
        $filter = $this->filter;
        $today = Carbon::now()->startOfDay();

        switch ($filter) {
            case 'month':
                $currentPeriodStart = $today->copy()->startOfMonth();
                $currentPeriodEnd = $today->copy()->endOfMonth();
                $previousPeriodStart = $currentPeriodStart->copy()->subMonth();
                $previousPeriodEnd = $currentPeriodEnd->copy()->subMonth();
                break;
            case 'quarter':
                $currentPeriodStart = $today->copy()->startOfQuarter();
                $currentPeriodEnd = $today->copy()->endOfQuarter();
                $previousPeriodStart = $currentPeriodStart->copy()->subQuarter();
                $previousPeriodEnd = $currentPeriodEnd->copy()->subQuarter();
                break;
            case 'year':
                $currentPeriodStart = $today->copy()->startOfYear();
                $currentPeriodEnd = $today->copy()->endOfYear();
                $previousPeriodStart = $currentPeriodStart->copy()->subYear();
                $previousPeriodEnd = $currentPeriodEnd->copy()->subYear();
                break;
            case 'four_weeks':
            default:
                $currentPeriodStart = $today->copy()->startOfWeek()->subWeeks(3);
                $currentPeriodEnd = $today->copy()->endOfWeek();
                $previousPeriodStart = $currentPeriodStart->copy()->subWeeks(4);
                $previousPeriodEnd = $currentPeriodEnd->copy()->subWeeks(4);
                break;
        }

        $currentPeriodChurn = $this->getChurnCount($currentPeriodStart, $currentPeriodEnd);
        $previousPeriodChurn = $this->getChurnCount($previousPeriodStart, $previousPeriodEnd);

        return [
            'datasets' => [
                [
                    'label' => 'Previous Period',
                    'data' => [$previousPeriodChurn],
                    'backgroundColor' => '#007bff', // Simple color
                ],
                [
                    'label' => 'Current Period',
                    'data' => [$currentPeriodChurn],
                    'backgroundColor' => '#28a745', // Simple color
                ],
            ],
            'labels' => [
                $previousPeriodStart->format('M d') . ' - ' . $previousPeriodEnd->format('M d'),
                $currentPeriodStart->format('M d') . ' - ' . $currentPeriodEnd->format('M d'),
            ],
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
            'four_weeks' => 'Last 4 Weeks',
            'month' => 'Last 3 Months',
            'quarter' => 'Last Quarter',
            'year' => 'Last Year',
        ];
    }

    protected function getFooterWidgets(): array
    {
        $filter = $this->filter;
        $today = Carbon::now()->startOfDay();

        switch ($filter) {
            case 'month':
                $currentPeriodStart = $today->copy()->startOfMonth();
                $currentPeriodEnd = $today->copy()->endOfMonth();
                $previousPeriodStart = $currentPeriodStart->copy()->subMonth();
                $previousPeriodEnd = $currentPeriodEnd->copy()->subMonth();
                break;
            case 'quarter':
                $currentPeriodStart = $today->copy()->startOfQuarter();
                $currentPeriodEnd = $today->copy()->endOfQuarter();
                $previousPeriodStart = $currentPeriodStart->copy()->subQuarter();
                $previousPeriodEnd = $currentPeriodEnd->copy()->subQuarter();
                break;
            case 'year':
                $currentPeriodStart = $today->copy()->startOfYear();
                $currentPeriodEnd = $today->copy()->endOfYear();
                $previousPeriodStart = $currentPeriodStart->copy()->subYear();
                $previousPeriodEnd = $currentPeriodEnd->copy()->subYear();
                break;
            case 'four_weeks':
            default:
                $currentPeriodStart = $today->copy()->startOfWeek()->subWeeks(3);
                $currentPeriodEnd = $today->copy()->endOfWeek();
                $previousPeriodStart = $currentPeriodStart->copy()->subWeeks(4);
                $previousPeriodEnd = $currentPeriodEnd->copy()->subWeeks(4);
                break;
        }

        $currentPeriodChurn = $this->getChurnCount($currentPeriodStart, $currentPeriodEnd);
        $previousPeriodChurn = $this->getChurnCount($previousPeriodStart, $previousPeriodEnd);

        $percentageChange = $this->calculatePercentageChange($previousPeriodChurn, $currentPeriodChurn);

        return [
            Stat::make('Period-on-Period Change', $percentageChange . '%')
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
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
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
