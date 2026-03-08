<?php

namespace App\Filament\Resources\TripDays\Schemas;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TripDayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('trip_id')
                    ->label('Trip')
                    ->relationship(
                        name: 'trip',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('user_id', auth()->id())
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
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
}
