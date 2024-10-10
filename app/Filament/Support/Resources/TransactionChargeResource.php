<?php

namespace App\Filament\Support\Resources;

use App\Filament\Support\Resources\TransactionChargeResource\Pages;
use App\Filament\Support\Resources\TransactionChargeResource\RelationManagers;
use App\Models\Currency;
use App\Models\Subcategory;
use App\Models\TransactionCharge;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionChargeResource extends Resource
{
    protected static ?string $model = TransactionCharge::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Transaction Processing';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('service_id')
                    ->label('Service')
                    ->options(Subcategory::all()->pluck('name', 'id'))
                    ->searchable(),
                Select::make('charge_type')
                    ->options([
                        'fixed' => 'Fixed',
                        'percentage' => 'Percentage',
                        'both' => 'Both',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('fixed_amount')
                    ->numeric()
                    ->default(null)
                    ->visible(fn (Forms\Get $get) =>
                    in_array($get('charge_type'), ['fixed', 'both'])
                    ),
                Forms\Components\TextInput::make('percentage')
                    ->numeric()
                    ->default(null)
                    ->visible(fn (Forms\Get $get) =>
                    in_array($get('charge_type'), ['percentage', 'both'])
                    ),
                Select::make('currency_id')
                    ->label('Currency')
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->searchable()->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Service.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_type'),
                Tables\Columns\TextColumn::make('fixed_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactionCharges::route('/'),
        ];
    }
}
