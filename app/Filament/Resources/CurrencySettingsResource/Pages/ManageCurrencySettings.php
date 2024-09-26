<?php

namespace App\Filament\Resources\CurrencySettingsResource\Pages;

use App\Filament\Resources\CurrencySettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCurrencySettings extends ManageRecords
{
    protected static string $resource = CurrencySettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
