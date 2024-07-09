<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class financial extends Page
{
    protected static ?string $title = 'Financial  ';
    protected static ?string $navigationGroup = 'KPIs';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.financial';
}
