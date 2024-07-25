<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Interactions;
use App\Livewire\CustStatsOverview;
use App\Livewire\CustvalueChart;
use App\Livewire\HelpDeskChart;
use App\Livewire\SericeDeskOverview;
use App\Livewire\TransvalueChart;
use App\Livewire\UserDemographic;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets;


class service extends Page
{

    protected function getHeaderWidgets(): array
    {
        return [
           HelpDeskChart::class,
            \App\Livewire\Interactions::class,
             UserDemographic::class
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    protected static ?string $title = 'Service Desk';

//    protected static ?string $navigationParentItem = 'Notifications';

    protected static ?string $navigationGroup = 'KPIs';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.service';
}
