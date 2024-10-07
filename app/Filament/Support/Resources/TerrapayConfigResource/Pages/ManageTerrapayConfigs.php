<?php

namespace App\Filament\Support\Resources\TerrapayConfigResource\Pages;

use App\Filament\Support\Resources\TerrapayConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTerrapayConfigs extends ManageRecords
{
    protected static string $resource = TerrapayConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
