<?php

namespace App\Filament\Support\Resources;

use App\Filament\Support\Resources\ConversionLogResource\Pages;
use App\Filament\Support\Resources\ConversionLogResource\RelationManagers;
use App\Models\ConversionLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConversionLogResource extends Resource
{
    protected static ?string $model = ConversionLog::class;

//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Transaction Processing';
//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('from_currency_id')
                    ->relationship('fromCurrency', 'name')
                    ->required(),
                Forms\Components\Select::make('to_currency_id')
                    ->relationship('toCurrency', 'name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('converted_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('used_rate')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('rate_type')
                    ->required(),
                Forms\Components\DateTimePicker::make('conversion_date')
                    ->required(),
                Forms\Components\TextInput::make('user_id')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fromCurrency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('toCurrency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('converted_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('used_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_type'),
                Tables\Columns\TextColumn::make('conversion_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->searchable(),
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
            'index' => Pages\ManageConversionLogs::route('/'),
        ];
    }
}
