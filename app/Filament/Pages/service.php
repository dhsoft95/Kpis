<?php

namespace App\Filament\Pages;

use App\Livewire\CustStatsOverview;
use App\Livewire\CustvalueChart;
use App\Livewire\SericeDeskOverview;
use App\Livewire\TransvalueChart;
use Filament\Pages\Page;

class service extends Page
{



    protected static ?string $title = 'Service Desk';

//    protected static ?string $navigationParentItem = 'Notifications';

    protected static ?string $navigationGroup = 'KPIs';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.service';
}
