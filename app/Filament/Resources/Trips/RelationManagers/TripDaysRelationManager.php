<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use App\Filament\Resources\TripDays\TripDayResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripDaysRelationManager extends RelationManager
{
    protected static string $relationship = 'days';

    protected static ?string $title = 'Trip days';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('day_number')
                    ->label('Day number')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                DatePicker::make('date')
                    ->label('Date'),
                TextInput::make('title')
                    ->placeholder('Day 1')
                    ->maxLength(255),
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
            ->defaultSort('day_number')
            ->recordUrl(fn ($record) => TripDayResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('day_number')
                    ->label('Day')
                    ->formatStateUsing(fn ($state) => 'Day ' . $state)
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('title')
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add day')
                    ->modalHeading('Add trip day')
                    ->modalSubmitActionLabel('Save day')
                    ->modalWidth('lg'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit trip day')
                    ->modalSubmitActionLabel('Save changes')
                    ->modalWidth('lg'),
                DeleteAction::make(),
            ]);
    }
}
