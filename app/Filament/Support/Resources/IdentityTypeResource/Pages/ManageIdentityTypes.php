<?php

namespace App\Filament\Support\Resources\IdentityTypeResource\Pages;

use App\Filament\Support\Resources\IdentityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIdentityTypes extends ManageRecords
{
    protected static string $resource = IdentityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
