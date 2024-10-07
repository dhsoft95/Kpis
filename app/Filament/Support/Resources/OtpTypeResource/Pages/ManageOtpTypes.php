<?php

namespace App\Filament\Support\Resources\OtpTypeResource\Pages;

use App\Filament\Support\Resources\OtpTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOtpTypes extends ManageRecords
{
    protected static string $resource = OtpTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
