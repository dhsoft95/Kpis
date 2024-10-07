<?php

namespace App\Filament\Support\Resources\SubcategoryResource\Pages;

use App\Filament\Support\Resources\SubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubcategories extends ManageRecords
{
    protected static string $resource = SubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
