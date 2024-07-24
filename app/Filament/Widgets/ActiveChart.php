<?php

namespace App\Filament\Widgets;

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
            'day' => 'Last 24 Hours',
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            'year' => 'Last 365 Days',
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

    protected function getDateRange(): array
    {
        $end = now();
        $start = match ($this->filter) {
            'day' => $end->copy()->subDay(),
            'week' => $end->copy()->subWeek(),
            'month' => $end->copy()->subMonth(),
            'year' => $end->copy()->subYear(),
            default => $end->copy()->subWeek(),
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

        $period = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate
        );

        foreach ($period as $date) {
            // Active Users
            $activeCount = DB::connection('mysql_second')
                ->table('transactions')
                ->select('sender_phone')
                ->whereDate('created_at', $date)
                ->where('status', 3) // Assuming status 1 is for successful transactions
                ->whereNotNull('sender_amount') // Ensure there's an amount for the transaction
                ->distinct()
                ->count();

            // Total Registered Users
            $totalRegisteredUsers = DB::connection('mysql_second')
                ->table('users')
                ->where('created_at', '<=', $date)
                ->count();

            // Inactive Users
            $inactiveCount = $totalRegisteredUsers - $activeCount;

            $activeCounts[] = $activeCount;
            $inactiveCounts[] = $inactiveCount;
            $labels[] = $date->format('M d');

            // Calculate WoW percentages
            if (count($activeCounts) > 1) {
                $wowActivePercentages[] = $this->calculatePercentageChange(
                    $activeCounts[count($activeCounts) - 2],
                    $activeCounts[count($activeCounts) - 1]
                );
                $wowInactivePercentages[] = $this->calculatePercentageChange(
                    $inactiveCounts[count($inactiveCounts) - 2],
                    $inactiveCounts[count($inactiveCounts) - 1]
                );
            }
        }

        return [
            'activeCounts' => $activeCounts,
            'inactiveCounts' => $inactiveCounts,
            'labels' => $labels,
            'wowActivePercentages' => $wowActivePercentages,
            'wowInactivePercentages' => $wowInactivePercentages,
        ];
    }

    protected function calculatePercentageChange($oldValue, $newValue): float|int
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0; // 100% increase if new value is positive, 0% if it's also 0
        }
        return (($newValue - $oldValue) / $oldValue) * 100;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
