<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserPerformance extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'xl' => 1,
    ];

    public function getColumns(): int
    {
        return 2;
    }



    protected function getStats(): array
    {
        return [

            Stat::make('All Registered Users', '100%') // Example value
            ->description('Total : 1000')
                ->icon('heroicon-m-user-group')// Indicates the percent increase from the previous month
            ->descriptionIcon('heroicon-m-user-group') // Icon representing users
            ->chart([1000, 1200, 1500, 1300, 1400, 1600, 1800]) // Historical data for the past 7 periods
            ->color('primary'),

            Stat::make('Active Users', '1,200 users') // Example value
            ->description('Change from last month: +5%') // Indicates the percent increase from the previous month
            ->descriptionIcon('heroicon-m-battery-100') // Icon representing active users
            ->chart([800, 900, 1000, 950, 1100, 1150, 1200]) // Historical data for the past 7 periods
            ->color('success'),

            Stat::make('Inactive Users', '300 users') // Example value
            ->description('Change from last month: -2%') // Indicates the percent decrease from the previous month
            ->descriptionIcon('heroicon-m-bolt-slash') // Icon representing inactive users
            ->chart([200, 250, 300, 275, 290, 310, 300]) // Historical data for the past 7 periods
            ->color('warning'),

            Stat::make('Churn Users', '150 users') // Example value
            ->description('Change from last month: +8%') // Indicates the percent increase from the previous month
            ->descriptionIcon('heroicon-m-user-minus') // Icon representing churned users
            ->chart([100, 120, 150, 140, 130, 160, 150]) // Historical data for the past 7 periods
            ->color('danger'),

        ];
    }
}
