<?php

namespace App\Filament\Resources\TripDays\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('category')
                    ->datalist([
                        'Food',
                        'Transport',
                        'Lodging',
                        'Activities',
                        'Shopping',
                        'Groceries',
                        'Tips',
                        'Fees',
                        'Tickets',
                        'Gifts',
                        'Other',
                    ])
                    ->placeholder('Select or type category')
                    ->maxLength(100),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('$'),
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
                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()?->trip?->currency ?? 'USD')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, RelationManager $livewire, $state): void {
                        $trip = $livewire->getOwnerRecord()?->trip;

                        if (! $trip) {
                            return;
                        }

                        $trip->loadMissing('exchangeRates');
                        $set('conversion_rate', $trip->getExchangeRateFor($state));
                    }),
                TextInput::make('conversion_rate')
                    ->label('Conversion rate to trip currency')
                    ->numeric()
                    ->helperText('Optional. Enter 1 expense currency = ? trip currency'),
                DateTimePicker::make('spent_at')
                    ->label('Spent at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->stackedOnMobile()
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
            ])
            ->defaultSort('spent_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Currency')
                    ->toggleable(),
                TextColumn::make('category')
                    ->toggleable(),
                TextColumn::make('spent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add expense')
                    ->modalHeading('Add expense')
                    ->modalSubmitActionLabel('Save expense')
                    ->modalWidth('lg'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit expense')
                    ->modalSubmitActionLabel('Save changes')
                    ->modalWidth('lg'),
                DeleteAction::make(),
            ]);
    }
}
