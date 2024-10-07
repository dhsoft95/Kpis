<?php

namespace App\Filament\Support\Resources\TemboPlusConfigResource\Pages;

use App\Filament\Support\Resources\TemboPlusConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTemboPlusConfigs extends ManageRecords
{
    protected static string $resource = TemboPlusConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
