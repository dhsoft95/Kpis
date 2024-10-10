<?php

namespace App\Filament\Support\Resources\TransactionChargeResource\Pages;

use App\Filament\Support\Resources\TransactionChargeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransactionCharges extends ManageRecords
{
    protected static string $resource = TransactionChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
