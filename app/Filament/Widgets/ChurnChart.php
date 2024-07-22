<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ChurnChart extends ChartWidget
{
    protected static ?string $heading = 'Churn Users Trend';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'year' => 'Last 12 months',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'week';

        $data = match ($filter) {
            'today' => $this->getChurnDataByHour(),
            'week' => $this->getChurnDataByDay(),
            'month' => $this->getChurnDataByDay(30),
            'year' => $this->getChurnDataByMonth(),
            default => [],
        };

        return [
            'datasets' => [
                [
                    'label' => 'Churned users',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getChurnDataByHour(): array
    {
        $start = Carbon::today();
        $end = Carbon::now();
        return $this->getChurnData($start, $end, 'hour');
    }

    private function getChurnDataByDay(int $days = 7): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $end = Carbon::now()->endOfDay();
        return $this->getChurnData($start, $end, 'day');
    }

    private function getChurnDataByMonth(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        return $this->getChurnData($start, $end, 'month');
    }

    private function getChurnData(Carbon $start, Carbon $end, string $groupBy): array
    {
        $dateFormat = $this->getSqlDateFormat($groupBy);

        $data = DB::connection('mysql_second')->table('users')
            ->leftJoin('mysql_second.tbl_transactions', 'users.phone_number', '=', 'tbl_transactions.sender_phone')
            ->whereNull('tbl_transactions.sender_phone')
            ->whereBetween('users.created_at', [$start, $end])
            ->groupBy(DB::raw("DATE_FORMAT(users.created_at, '{$dateFormat}')"))
            ->selectRaw("DATE_FORMAT(users.created_at, '{$dateFormat}') as date, COUNT(*) as count")
            ->pluck('count', 'date')
            ->toArray();

        return $this->fillMissingDates($data, $start, $end, $groupBy);
    }

    private function getSqlDateFormat(string $groupBy): string
    {
        return match ($groupBy) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m-01',
            default => '%Y-%m-%d',
        };
    }

    private function getPhpDateFormat(string $groupBy): string
    {
        return match ($groupBy) {
            'hour' => 'Y-m-d H:00:00',
            'day' => 'Y-m-d',
            'month' => 'Y-m-01',
            default => 'Y-m-d',
        };
    }

    private function fillMissingDates(array $data, Carbon $start, Carbon $end, string $groupBy): array
    {
        $allDates = $this->generateDateRange($start, $end, $groupBy);
        $filledData = array_fill_keys($allDates, 0);
        $mergedData = array_merge($filledData, $data);

        // Format the labels
        $formattedData = [];
        foreach ($mergedData as $date => $count) {
            $formattedData[$this->formatLabel($date, $groupBy)] = $count;
        }

        return $formattedData;
    }

    private function generateDateRange(Carbon $start, Carbon $end, string $groupBy): array
    {
        $dates = [];
        $current = $start->copy();
        $dateFormat = $this->getPhpDateFormat($groupBy);

        while ($current <= $end) {
            $dates[] = $current->format($dateFormat);
            $current->add($this->getDateInterval($groupBy));
        }

        return $dates;
    }

    private function getDateInterval(string $groupBy): \DateInterval
    {
        return match ($groupBy) {
            'hour' => new \DateInterval('PT1H'),
            'day' => new \DateInterval('P1D'),
            'month' => new \DateInterval('P1M'),
            default => new \DateInterval('P1D'),
        };
    }

    private function formatLabel(string $date, string $groupBy): string
    {
        $carbon = Carbon::createFromFormat($this->getPhpDateFormat($groupBy), $date);

        return match ($groupBy) {
            'hour' => $carbon->format('H:i'),
            'day' => $carbon->format('d M'),
            'month' => $carbon->format('M y'),
            default => $carbon->format('d M'),
        };
    }
}
