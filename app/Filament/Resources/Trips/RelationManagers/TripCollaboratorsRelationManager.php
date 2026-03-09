<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripCollaboratorsRelationManager extends RelationManager
{
    protected static string $relationship = 'collaborators';

    protected static ?string $title = 'Collaborators';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge()
                    ->toggleable(),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remove'),
            ]);
    }
}
