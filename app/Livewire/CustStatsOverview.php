<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class CustStatsOverview extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.cust-stats-overview';
}
