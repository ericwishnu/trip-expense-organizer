<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
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
                TextColumn::make('tripDay.trip.name')
                    ->label('Trip')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tripDay.day_number')
                    ->label('Day')
                    ->formatStateUsing(fn ($state) => 'Day ' . $state)
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
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
