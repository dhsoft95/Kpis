<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ChurnChart extends ChartWidget
{

    protected static ?string $heading = 'Weekly Churn Users';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

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
        $filter = $this->filter ?? 'week';

        $data = match ($filter) {
            'week' => $this->getChurnData(5),
            'month' => $this->getChurnData(13),  // 13 weeks ~= 3 months
            'quarter' => $this->getChurnData(13),
            'year' => $this->getChurnData(52),
            default => [],
        };

        return [
            'datasets' => [
                [
                    'label' => 'Churn Users',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getChurnData(int $weeks): array
    {
        $end = Carbon::now()->endOfWeek();
        $start = $end->copy()->subWeeks($weeks - 1)->startOfWeek();

        return $this->fetchChurnData($start, $end, 'week');
    }

    private function fetchChurnData(Carbon $start, Carbon $end, string $groupBy): array
    {
        $dateFormat = $this->getSqlDateFormat($groupBy);

        $data = DB::connection('mysql_second')->table('users')
            ->leftJoin('tbl_transactions', function ($join) {
                $join->on('users.phone_number', '=', 'tbl_transactions.sender_phone')
                    ->where('tbl_transactions.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL 30 DAY)'));
            })
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
            'week' => '%Y-%m-%d',  // Changed to full date
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };
    }


    private function getPhpDateFormat(string $groupBy): string
    {
        return match ($groupBy) {
            'week' => 'Y-m-d',  // Changed to full date
            'month' => 'Y-m',
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

        ksort($formattedData);
        return $formattedData;
    }

    private function generateDateRange(Carbon $start, Carbon $end, string $groupBy): array
    {
        $dates = [];
        $current = $start->copy();
        $dateFormat = $this->getPhpDateFormat($groupBy);

        while ($current <= $end) {
            if ($groupBy === 'week') {
                $dates[] = $current->startOfWeek()->format($dateFormat);
            } else {
                $dates[] = $current->format($dateFormat);
            }
            $current->add($this->getDateInterval($groupBy));
        }

        return $dates;
    }

    private function getDateInterval(string $groupBy): \DateInterval
    {
        return match ($groupBy) {
            'week' => new \DateInterval('P1W'),
            'month' => new \DateInterval('P1M'),
            default => new \DateInterval('P1D'),
        };
    }

    private function formatLabel(string $date, string $groupBy): string
    {
        $carbon = Carbon::createFromFormat($this->getPhpDateFormat($groupBy), $date);

        return match ($groupBy) {
            'week' => 'Week ' . $carbon->weekOfYear . ', ' . $carbon->year,
            'month' => $carbon->format('M Y'),
            'quarter' => 'Q' . $carbon->quarter . ' ' . $carbon->year,
            'year' => $carbon->format('M Y'),
            default => $carbon->format('d M Y'),
        };
    }


    private function createCarbonFromFormat(string $date, string $groupBy): Carbon
    {
        return match ($groupBy) {
            'week' => Carbon::createFromFormat('o-W', $date),
            'month' => Carbon::createFromFormat('Y-m', $date),
            default => Carbon::createFromFormat('Y-m-d', $date),
        };
    }

}
