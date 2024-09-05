<?php

namespace App\Filament\Pages;

use App\Livewire\WalletOverview;
use Filament\Pages\Page;

class financial extends Page
{
    protected static ?string $title = 'Financial';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.financial';



    protected function getHeaderWidgets(): array
    {
        return [
            WalletOverview::class,
        ];
    }
}
