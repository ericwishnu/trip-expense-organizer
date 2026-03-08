<?php

namespace App\Filament\Resources\TripDays\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripDaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->stackedOnMobile()
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
            ])
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('trip.name')
                    ->label('Trip')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('day_number')
                    ->label('Day')
                    ->formatStateUsing(fn ($state) => 'Day ' . $state)
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('title')
                    ->searchable()
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
