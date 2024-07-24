<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActiveChart extends ChartWidget
{
    protected static ?string $heading = ' Active Vs Inactive users (WoW)';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '3600s'; // Update every hour

    protected function getData(): array
    {
        $data = $this->getUserCounts();

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

    protected function getUserCounts(): array
    {
        $activeCounts = [];
        $inactiveCounts = [];
        $labels = [];
        $wowActivePercentages = [];
        $wowInactivePercentages = [];

        for ($i = 4; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            // Active Users
            $activeCount = DB::connection('mysql_second')
                ->table('transactions')
                ->select('sender_phone')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1) // Assuming status 1 is for successful transactions
                ->whereNotNull('sender_amount') // Ensure there's an amount for the transaction
                ->distinct()
                ->count();

            // Total Registered Users
            $totalRegisteredUsers = DB::connection('mysql_second')
                ->table('users')
                ->where('created_at', '<=', $endDate)
                ->count();

            // Inactive Users
            $inactiveCount = $totalRegisteredUsers - $activeCount;

            $activeCounts[] = $activeCount;
            $inactiveCounts[] = $inactiveCount;
            $labels[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');

            // Calculate WoW percentages
            if ($i < 4) {
                $wowActivePercentages[] = ($activeCounts[3-$i] - $activeCounts[4-$i]) / $activeCounts[4-$i] * 100;
                $wowInactivePercentages[] = ($inactiveCounts[3-$i] - $inactiveCounts[4-$i]) / $inactiveCounts[4-$i] * 100;
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

    protected function getType(): string
    {
        return 'bar';
    }

}
