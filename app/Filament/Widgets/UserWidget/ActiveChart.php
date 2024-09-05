<?php

namespace App\Filament\Widgets\UserWidget;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ActiveChart extends ChartWidget
{
    protected static ?string $heading = 'Active vs Inactive Users';
    protected static ?int $sort = 1;
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
        try {
            $dateRange = $this->getDateRange();
            $data = $this->getUserCounts($dateRange['start'], $dateRange['end']);

            return [
                'datasets' => [
                    [
                        'label' => 'Active Users',
                        'data' => $data['activeCounts'],
                        'backgroundColor' => '#584408',
                    ],
                    [
                        'label' => 'Inactive Users',
                        'data' => $data['inactiveCounts'],
                        'backgroundColor' => '#E0B22C',
                    ],
                ],
                'labels' => $data['labels'],
            ];
        } catch (Exception $e) {
            // Log the error
            \Log::error('Error in ActiveChart getData: ' . $e->getMessage());

            // Return empty data to prevent chart errors
            return [
                'datasets' => [
                    ['label' => 'Active Users', 'data' => []],
                    ['label' => 'Inactive Users', 'data' => []],
                ],
                'labels' => [],
            ];
        }
    }

    protected function getDateRange(): array
    {
        $end = now()->endOfDay();
        $start = match ($this->filter) {
            'week' => $end->copy()->subWeeks(4)->startOfDay(),
            'month' => $end->copy()->subMonths(3)->startOfMonth(),
            'quarter' => $end->copy()->subQuarter()->startOfQuarter(),
            'year' => $end->copy()->subYear()->startOfYear(),
            default => $end->copy()->subWeeks(4)->startOfDay(),
        };

        return compact('start', 'end');
    }

    protected function getUserCounts(Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = "user_counts_{$this->filter}_{$startDate->timestamp}_{$endDate->timestamp}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($startDate, $endDate) {
            $activeCounts = [];
            $inactiveCounts = [];
            $labels = [];
            $period = $this->getDatePeriod($startDate, $endDate);

            foreach ($period as $periodStart) {
                $periodEnd = $periodStart->copy()->add($this->getIntervalFromFilter())->subDay();
                if ($periodEnd->gt($endDate)) {
                    $periodEnd = $endDate->copy();
                }

                $activeCount = $this->getActiveUserCount($periodStart, $periodEnd);
                $totalUsers = $this->getTotalUserCount($periodEnd);
                $inactiveCount = $totalUsers - $activeCount;

                $activeCounts[] = $activeCount;
                $inactiveCounts[] = $inactiveCount;
                $labels[] = $periodStart->format('M d') . ' - ' . $periodEnd->format('M d');
            }

            return compact('activeCounts', 'inactiveCounts', 'labels');
        });
    }

    protected function getDatePeriod(Carbon $startDate, Carbon $endDate): CarbonPeriod
    {
        return CarbonPeriod::create($startDate, $this->getIntervalFromFilter(), $endDate);
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

    protected function getActiveUserCount(Carbon $start, Carbon $end): int
    {
        return DB::connection('mysql_second')
            ->table(' tbl_simba_transactions')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 3)
            ->whereNotNull('sender_amount')
            ->distinct('sender_phone')
            ->count('sender_phone');
    }

    protected function getTotalUserCount(Carbon $date): int
    {
        return DB::connection('mysql_second')
            ->table('users')
            ->where('created_at', '<=', $date)
            ->count();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
