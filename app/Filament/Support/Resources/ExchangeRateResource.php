<?php

namespace App\Filament\Support\Resources;

use App\Filament\Support\Resources\ExchangeRateResource\Pages;
use App\Filament\Support\Resources\ExchangeRateResource\RelationManagers;
use App\Models\ExchangeRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Transaction Processing';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('base_currency_id')
                    ->relationship('baseCurrency', 'name')
                    ->required(),
                Forms\Components\Select::make('target_currency_id')
                    ->relationship('targetCurrency', 'name')
                    ->required(),
                Forms\Components\TextInput::make('global_rate')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('sml_rate')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_flat_adjustment')
                    ->required(),
                Forms\Components\Select::make('sml_adjustment_id')
                    ->relationship('smlAdjustment', 'name')
                    ->default(null),
                Forms\Components\Select::make('government_tax_id')
                    ->relationship('governmentTax', 'name')
                    ->default(null),
                Forms\Components\DateTimePicker::make('effective_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('baseCurrency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('targetCurrency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('global_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sml_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_flat_adjustment')
                    ->boolean(),
                Tables\Columns\TextColumn::make('smlAdjustment.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('governmentTax.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_date')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ManageExchangeRates::route('/'),
        ];
    }
}
