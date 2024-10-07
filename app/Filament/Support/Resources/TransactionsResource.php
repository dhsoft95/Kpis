<?php

namespace App\Filament\Support\Resources;

use App\Filament\Support\Resources\TransactionsResource\Pages;
use App\Models\transactions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Fieldset;

class TransactionsResource extends Resource
{
    protected static ?string $model = transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

    protected static ?string $modelLabel = 'All Transaction Records';
    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('trx_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('third_party_trx_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_no')
                    ->maxLength(60)
                    ->default('NO ACCOUNT')
                    ->required(),
                Forms\Components\TextInput::make('transaction_type')
                    ->maxLength(100)
                    ->required(),
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('txn_source')
                    ->maxLength(20),
                Forms\Components\TextInput::make('credit_amount')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('debit_amount')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('sender_currency')
                    ->maxLength(3)
                    ->default('TZS'),
                Forms\Components\TextInput::make('receiver_currency')
                    ->maxLength(3)
                    ->default('TZS'),
                Forms\Components\TextInput::make('charges')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('txn_destination')
                    ->maxLength(20),
                Forms\Components\TextInput::make('receiver_fullname')
                    ->maxLength(255),
                Forms\Components\TextInput::make('partner_charges')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('biller_code')
                    ->maxLength(100),
                Forms\Components\TextInput::make('biller_ref')
                    ->maxLength(100),
                Forms\Components\TextInput::make('tax')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('exchange_rate')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('partner_exchange_rate')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('partner_name')
                    ->maxLength(200),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(100),
                Forms\Components\TextInput::make('network_type')
                    ->maxLength(50),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'deposited' => 'Deposited',
                        'sent' => 'Sent',
                        'received' => 'Received',
                        'failed' => 'Failed',
                        'reversed' => 'Reversed',
                        'cancelled' => 'Cancelled'
                    ])
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trx_id')->searchable(),
                Tables\Columns\TextColumn::make('account_no')->searchable(),
                Tables\Columns\TextColumn::make('transaction_type')->searchable(),
                Tables\Columns\TextColumn::make('credit_amount'),
                Tables\Columns\TextColumn::make('debit_amount'),
                Tables\Columns\TextColumn::make('sender_currency'),
                Tables\Columns\TextColumn::make('receiver_currency'),
                Tables\Columns\TextColumn::make('charges'),
                Tables\Columns\TextColumn::make('status')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'deposited' => 'Deposited',
                        'sent' => 'Sent',
                        'received' => 'Received',
                        'failed' => 'Failed',
                        'reversed' => 'Reversed',
                        'cancelled' => 'Cancelled'
                    ]),
                SelectFilter::make('transaction_type')
                    ->multiple()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Fieldset::make('Transaction Details')
                    ->schema([
                        TextEntry::make('trx_id'),
                        TextEntry::make('account_no'),
                        TextEntry::make('transaction_type'),
                        TextEntry::make('third_party_trx_id'),
                        TextEntry::make('user_id'),
                        TextEntry::make('txn_source'),
                        TextEntry::make('credit_amount'),
                        TextEntry::make('debit_amount'),
                        TextEntry::make('sender_currency'),
                        TextEntry::make('receiver_currency'),
                        TextEntry::make('charges'),
                        TextEntry::make('txn_destination'),
                        TextEntry::make('receiver_fullname'),
                        TextEntry::make('partner_charges'),
                        TextEntry::make('biller_code'),
                        TextEntry::make('biller_ref'),
                        TextEntry::make('tax'),
                        TextEntry::make('exchange_rate'),
                        TextEntry::make('partner_exchange_rate'),
                        TextEntry::make('partner_name'),
                        TextEntry::make('reason'),
                        TextEntry::make('network_type'),
                        TextEntry::make('status'),
                    ])->columns(3)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransactions::route('/create'),
            'edit' => Pages\EditTransactions::route('/{record}/edit'),
        ];
    }
}
