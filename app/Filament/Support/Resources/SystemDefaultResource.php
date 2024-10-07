<?php

namespace App\Filament\Support\Resources;

use App\Filament\Support\Resources\SystemDefaultResource\Pages;
use App\Filament\Support\Resources\SystemDefaultResource\RelationManagers;
use App\Models\SystemDefault;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemDefaultResource extends Resource
{
    protected static ?string $model = SystemDefault::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'General Settings ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key_name')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ManageSystemDefaults::route('/'),
        ];
    }
}
