<?php

namespace App\Filament\Support\Resources\ConversionLogResource\Pages;

use App\Filament\Support\Resources\ConversionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConversionLogs extends ManageRecords
{
    protected static string $resource = ConversionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
