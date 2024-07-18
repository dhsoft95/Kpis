<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class MnoWallets extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.mno-wallets';
}
