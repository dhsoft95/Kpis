<?php

namespace App\Filament\Widgets;

use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $data = $this->getUserCounts($dateRange['start'], $dateRange['end']);

        return [
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => $data['activeCounts'],
                    'backgroundColor' => '#7265F9',
//                    'borderColor' => '#4A58EC',
                ],
                [
                    'label' => 'Inactive Users',
                    'data' => $data['inactiveCounts'],
                    'backgroundColor' => '#48D3FF',
//                    'borderColor' => '#e52f42',
                ],
            ],
            'labels' => $data['labels'],
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
        $wowActivePercentages = [];
        $wowInactivePercentages = [];

        $interval = $this->getIntervalFromFilter();
        $periodEnd = $endDate->copy();

        while ($periodEnd->gte($startDate)) {
            $periodStart = $periodEnd->copy()->sub($interval)->addDay();
            if ($periodStart->lt($startDate)) {
                $periodStart = $startDate->copy();
            }

            // Active Users
            $activeCount = DB::connection('mysql_second')
                ->table('tbl_transactions')
                ->select('sender_phone')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->where('status', 3)
                ->whereNotNull('sender_amount')
                ->distinct()
                ->count();

            // Total Registered Users
            $totalRegisteredUsers = DB::connection('mysql_second')
                ->table('users')
                ->where('created_at', '<=', $periodEnd)
                ->count();

            // Inactive Users
            $inactiveCount = $totalRegisteredUsers - $activeCount;

            array_unshift($activeCounts, $activeCount);
            array_unshift($inactiveCounts, $inactiveCount);
            array_unshift($labels, $periodStart->format('M d') . ' - ' . $periodEnd->format('M d'));

            $periodEnd = $periodStart->subDay();
        }

        // Calculate WoW percentages
        for ($i = 1; $i < count($activeCounts); $i++) {
            $wowActivePercentages[] = $this->calculatePercentageChange($activeCounts[$i-1], $activeCounts[$i]);
            $wowInactivePercentages[] = $this->calculatePercentageChange($inactiveCounts[$i-1], $inactiveCounts[$i]);
        }

        return [
            'activeCounts' => $activeCounts,
            'inactiveCounts' => $inactiveCounts,
            'labels' => $labels,
            'wowActivePercentages' => $wowActivePercentages,
            'wowInactivePercentages' => $wowInactivePercentages,
        ];
    }

    protected function getIntervalFromFilter(): \DateInterval
    {
        return match ($this->filter) {
            'week' => new \DateInterval('P1W'),
            'month' => new \DateInterval('P1M'),
            'quarter' => new \DateInterval('P3M'),
            'year' => new \DateInterval('P1Y'),
            default => new \DateInterval('P1W'),
        };
    }

    protected function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return (($newValue - $oldValue) / $oldValue) * 100;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
