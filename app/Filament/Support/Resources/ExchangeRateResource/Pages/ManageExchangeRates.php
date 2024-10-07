<?php

namespace App\Filament\Support\Resources\ExchangeRateResource\Pages;

use App\Filament\Support\Resources\ExchangeRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageExchangeRates extends ManageRecords
{
    protected static string $resource = ExchangeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
