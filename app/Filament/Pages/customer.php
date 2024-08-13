<?php

namespace App\Filament\Pages;


use App\Filament\Widgets\ActiveChartAp;
use App\Filament\Widgets\Custvalue;
use App\Livewire\CustStatsOverview;
use App\Livewire\NPSComponentsChart;
use App\Livewire\WeeklyTrendsChart;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class customer extends Page
{

    protected function getHeaderWidgets(): array
    {
        return [
            CustStatsOverview::class,
            WeeklyTrendsChart::class,
            NPSComponentsChart::class,
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
//    protected static ?string $navigationGroup = 'KPIs';

    protected static ?string $title = 'Customer Metric';
//    protected int | string | array $columnSpan = 'full';


//    protected ?string $maxContentWidth = 'full';

//    public function getTabs(): array
//    {
//        return [
//            'All' => Tab::make()
//                ->badge(transData::query()->count()),
//            'Today' => Tab::make()
//                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->startOfDay()))
//                ->badge(transData::query()->where('created_at', '>=', now()->startOfDay())->count()),
//            'This Week' => Tab::make()
//                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subWeek()))
//                ->badge(transData::query()->where('created_at', '>=', now()->subWeek())->count()),
//            'This Month' => Tab::make()
//                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subMonth()))
//                ->badge(transData::query()->where('created_at', '>=', now()->subMonth())->count()),
//            'This Year' => Tab::make()
//                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subYear()))
//                ->badge(transData::query()->where('created_at', '>=', now()->subYear())->count()),
//        ];
//    }
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.customer';
}
