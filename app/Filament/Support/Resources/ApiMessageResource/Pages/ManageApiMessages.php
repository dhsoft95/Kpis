<?php

namespace App\Filament\Support\Resources\ApiMessageResource\Pages;

use App\Filament\Support\Resources\ApiMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageApiMessages extends ManageRecords
{
    protected static string $resource = ApiMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
