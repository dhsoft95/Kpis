<?php

namespace App\Filament\Pages;

use App\Livewire\BasedOnDateOverview;
use App\Livewire\FinancialOverview;
use App\Livewire\MonthlyComparisionOverview;
use App\Livewire\MonthlyTransactionsChart;
use App\Livewire\SinceInceptionOverview;
use App\Livewire\TotalAmountByTransactionType;
use App\Livewire\TransactionsValueChart;
use App\Livewire\TransvalueChart;
use App\Livewire\WalletOverview;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class financial extends Page
{
    protected static ?string $title = 'Financial';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.financial';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    protected function getHeaderWidgets(): array
    {
        return [
//            WalletOverview::class,
            SinceInceptionOverview::class,
            BasedOnDateOverview::class,
            MonthlyComparisionOverview::class,
//            FinancialOverview::class,
            TransactionsValueChart::class,
            MonthlyTransactionsChart::class,
            TotalAmountByTransactionType::class
        ];
    }
}
