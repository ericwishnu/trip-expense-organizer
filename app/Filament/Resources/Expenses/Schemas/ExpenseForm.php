<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('trip_day_id')
                    ->label('Trip day')
                    ->relationship(
                        name: 'tripDay',
                        titleAttribute: 'id',
                        modifyQueryUsing: fn ($query) => $query->whereHas('trip', fn ($tripQuery) => $tripQuery->where('user_id', auth()->id()))
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->trip->name . ' - Day ' . $record->day_number)
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('category')
                    ->maxLength(100),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('$'),
                TextInput::make('currency')
                    ->maxLength(3)
                    ->placeholder('USD'),
                DateTimePicker::make('spent_at')
                    ->label('Spent at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
