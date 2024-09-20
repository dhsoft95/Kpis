<?php

namespace App\Filament\Support\Widgets;

use App\Models\AppUser;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserInfo extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(AppUser::query())
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('last_wename')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('phone_verified_at')
                    ->boolean()
                    ->label('Phone Verified')
                    ->sortable(),
//                Tables\Columns\TextColumn::make('identity_type')
//                    ->sortable(),
                SelectColumn::make('identity_type')
                    ->options([
                        '0' => 'Other',
                        '1' => 'Nida',
                        '2' => 'Driving licence',
                    ]),
                Tables\Columns\TextColumn::make('identity_value')
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('wallet_status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_no')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'unverified' => 'Unverified',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\SelectFilter::make('wallet_status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\DateTimePicker::make('phone_verified_at'),
                        Forms\Components\Select::make('identity_type')
                            ->options([
                                0=> 'Type 0',
                                1 => 'Type 1',
                                // Add more options as needed
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('identity_value')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'unverified' => 'Unverified',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active'),
                        Forms\Components\Select::make('wallet_status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('account_no')
                            ->maxLength(60),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped();
    }
}
