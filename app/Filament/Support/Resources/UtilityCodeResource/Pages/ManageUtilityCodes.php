<?php

namespace App\Filament\Support\Resources\UtilityCodeResource\Pages;

use App\Filament\Support\Resources\UtilityCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUtilityCodes extends ManageRecords
{
    protected static string $resource = UtilityCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
