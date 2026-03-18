<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripExchangeRatesRelationManager extends RelationManager
{
    protected static string $relationship = 'exchangeRates';

    protected static ?string $title = 'Exchange rates';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('currency')
                    ->options([
                        'USD' => 'USD',
                        'IDR' => 'IDR',
                        'SGD' => 'SGD',
                        'THB' => 'THB',
                        'MYR' => 'MYR',
                        'RMB' => 'RMB',
                        'KIP' => 'KIP',
                        'VND' => 'VND',
                    ])
                    ->required()
                    ->searchable(),
                TextInput::make('rate')
                    ->label('Rate to trip currency')
                    ->numeric()
                    ->required()
                    ->helperText('1 unit of this currency equals ? trip currency'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('currency')
            ->columns([
                TextColumn::make('currency')
                    ->sortable(),
                TextColumn::make('rate')
                    ->label('Rate')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add rate')
                    ->modalHeading('Add exchange rate')
                    ->modalSubmitActionLabel('Save rate')
                    ->modalWidth('lg'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit exchange rate')
                    ->modalSubmitActionLabel('Save changes')
                    ->modalWidth('lg'),
                DeleteAction::make(),
            ]);
    }
}
