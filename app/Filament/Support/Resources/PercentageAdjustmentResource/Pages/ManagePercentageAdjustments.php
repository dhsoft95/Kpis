<?php

namespace App\Filament\Support\Resources\PercentageAdjustmentResource\Pages;

use App\Filament\Support\Resources\PercentageAdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePercentageAdjustments extends ManageRecords
{
    protected static string $resource = PercentageAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
