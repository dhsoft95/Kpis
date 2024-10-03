<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Interactions;
use App\Livewire\AppInteractions\HelpDeskChart;
use App\Livewire\AppInteractions\InteractionTrendWidget;
use App\Livewire\EscalationRateTrend;
use App\Livewire\SericeDeskOverview;
use App\Livewire\UserDemographic;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;


class service extends Page
{

    protected function getHeaderWidgets(): array
    {
        return [
           HelpDeskChart::class,
            InteractionTrendWidget::class,
           EscalationRateTrend::class
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    protected static ?string $title = 'Service Desk';
//    protected static ?string $navigationParentItem = 'Notifications';

    protected static ?string $navigationGroup = 'Dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.service';
}
