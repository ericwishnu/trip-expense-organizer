<?php

namespace App\Filament\Resources\Trips\Tables;

use App\Filament\Resources\Trips\TripResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->stackedOnMobile()
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn ($record) => TripResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('destination')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('currency')
                    ->toggleable(),
                TextColumn::make('budget')
                    ->money(fn ($record) => $record->currency ?? 'USD')
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
