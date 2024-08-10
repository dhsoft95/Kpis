<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use DateInterval;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ActiveChart extends ChartWidget
{
    protected static ?string $heading = 'Active Vs Inactive Users (WoW)';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '3600s'; // Update every hour

    public ?string $filter = 'week';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 5 Weeks',
            'month' => 'Last 3 Months',
            'quarter' => 'Last Quarter',
            'year' => 'Last Year',
        ];
    }

    protected function getData(): array
    {
        $dateRange = $this->getDateRange();
        $userCounts = $this->getUserCounts($dateRange['start'], $dateRange['end']);

        return [
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => $userCounts['activeCounts'],
                    'backgroundColor' => '#4A58EC', // Simple color
                    'borderColor' => null, // Remove border color
                    'borderWidth' => 0, // Remove border width
                ],
                [
                    'label' => 'Inactive Users',
                    'data' => $userCounts['inactiveCounts'],
                    'backgroundColor' => '#48D3FF', // Simple color
                    'borderColor' => null, // Remove border color
                    'borderWidth' => 0, // Remove border width
                ],
            ],
            'labels' => $userCounts['labels'],
        ];
    }

    protected function getDateRange(): array
    {
        $end = now()->endOfDay();
        $start = match ($this->filter) {
            'week' => $end->copy()->subWeeks(4)->startOfDay(),
            'month' => $end->copy()->subMonths(2)->startOfMonth(),
            'quarter' => $end->copy()->subMonths(3)->startOfQuarter(),
            'year' => $end->copy()->subYear()->startOfYear(),
            default => $end->copy()->subWeeks(4)->startOfDay(),
        };

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    protected function getUserCounts(Carbon $startDate, Carbon $endDate): array
    {
        $activeCounts = [];
        $inactiveCounts = [];
        $labels = [];

        $interval = $this->getIntervalFromFilter();
        $periodEnd = $endDate->copy();

        while ($periodEnd->gte($startDate)) {
            $periodStart = $periodEnd->copy()->sub($interval)->addDay();
            if ($periodStart->lt($startDate)) {
                $periodStart = $startDate->copy();
            }

            $activeCount = $this->getActiveUserCount($periodStart, $periodEnd);
            $totalRegisteredUsers = $this->getTotalRegisteredUsers($periodEnd);
            $inactiveCount = $totalRegisteredUsers - $activeCount;

            array_unshift($activeCounts, $activeCount);
            array_unshift($inactiveCounts, $inactiveCount);
            array_unshift($labels, $periodStart->format('M d') . ' - ' . $periodEnd->format('M d'));

            $periodEnd = $periodStart->subDay();
        }

        return [
            'activeCounts' => $activeCounts,
            'inactiveCounts' => $inactiveCounts,
            'labels' => $labels,
        ];
    }

    protected function getActiveUserCount(Carbon $start, Carbon $end): int
    {
        return DB::connection('mysql_second')
            ->table('tbl_transactions')
            ->select('sender_phone')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 3)
            ->whereNotNull('sender_amount')
            ->distinct()
            ->count();
    }

    protected function getTotalRegisteredUsers(Carbon $end): int
    {
        return DB::connection('mysql_second')
            ->table('users')
            ->where('created_at', '<=', $end)
            ->count();
    }

    protected function getIntervalFromFilter(): DateInterval
    {
        return match ($this->filter) {
            'week' => new DateInterval('P1W'),
            'month' => new DateInterval('P1M'),
            'quarter' => new DateInterval('P3M'),
            'year' => new DateInterval('P1Y'),
            default => new DateInterval('P1W'),
        };
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
