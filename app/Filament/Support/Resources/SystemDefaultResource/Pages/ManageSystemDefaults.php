<?php

namespace App\Filament\Support\Resources\SystemDefaultResource\Pages;

use App\Filament\Support\Resources\SystemDefaultResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSystemDefaults extends ManageRecords
{
    protected static string $resource = SystemDefaultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
