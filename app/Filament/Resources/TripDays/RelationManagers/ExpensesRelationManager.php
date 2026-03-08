<?php

namespace App\Filament\Resources\TripDays\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                        'RMB' => 'RMB',
                        'KIP' => 'KIP',
                    ])
                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()?->trip?->currency ?? 'USD')
                    ->searchable(),
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
