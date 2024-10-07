<?php

namespace App\Filament\Support\Resources\CommonPinResource\Pages;

use App\Filament\Support\Resources\CommonPinResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommonPins extends ManageRecords
{
    protected static string $resource = CommonPinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
