<?php

namespace App\Filament\Pages;

use App\Livewire\FinancialOverview;
use App\Livewire\WalletsBallance\PartersBallance;
use Filament\Pages\Page;


class oparations extends Page
{

    protected static ?string $title = 'Operations  ';

    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-cog';



    protected static string $view = 'filament.pages.oparations';




    protected function getHeaderWidgets(): array
    {
        return [

//            \App\Livewire\MnoWallets::class,
            PartersBallance::class,

        ];
    }

}
