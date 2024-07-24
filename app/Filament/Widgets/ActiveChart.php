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
    protected static ?string $heading = 'Weekly Churn Users';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

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
        $data = $this->getChurnData($dateRange['start'], $dateRange['end']);

        return [
            'datasets' => [
                [
                    'label' => 'Churn Users',
                    'data' => $data['churnCounts'],
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

    protected function getChurnData(Carbon $startDate, Carbon $endDate): array
    {
        $churnCounts = [];
        $labels = [];
        $wowPercentages = [];

        $interval = $this->getIntervalFromFilter();
        $periodEnd = $endDate->copy();

        while ($periodEnd->gte($startDate)) {
            $periodStart = $periodEnd->copy()->sub($interval)->addDay();
            if ($periodStart->lt($startDate)) {
                $periodStart = $startDate->copy();
            }

            $churnCount = DB::connection('mysql_second')->table('users')
                ->leftJoin('tbl_transactions', function ($join) use ($periodEnd) {
                    $join->on('users.phone_number', '=', 'tbl_transactions.sender_phone')
                        ->where('tbl_transactions.created_at', '>', DB::raw("DATE_SUB('{$periodEnd}', INTERVAL 30 DAY)"));
                })
                ->whereNull('tbl_transactions.sender_phone')
                ->where('users.created_at', '<=', $periodEnd)
                ->count();

            array_unshift($churnCounts, $churnCount);
            array_unshift($labels, $periodStart->format('M d') . ' - ' . $periodEnd->format('M d'));

            $periodEnd = $periodStart->subDay();
        }

        // Calculate WoW percentages
        for ($i = 1; $i < count($churnCounts); $i++) {
            $wowPercentages[] = $this->calculatePercentageChange($churnCounts[$i-1], $churnCounts[$i]);
        }

        return [
            'churnCounts' => $churnCounts,
            'labels' => $labels,
            'wowPercentages' => $wowPercentages,
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
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
