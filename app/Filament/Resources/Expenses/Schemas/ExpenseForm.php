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
                        modifyQueryUsing: fn ($query) => $query->whereHas('trip', function ($tripQuery) {
                            $tripQuery->where('user_id', auth()->id())
                                ->orWhereHas('collaborators', fn ($collabQuery) => $collabQuery->where('users.id', auth()->id()));
                        })
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->trip->name . ' - Day ' . $record->day_number)
                    ->searchable()
                    ->preload()
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
                Select::make('currency')
                    ->options([
                        'USD' => 'USD',
                        'IDR' => 'IDR',
                        'SGD' => 'SGD',
                        'RMB' => 'RMB',
                        'KIP' => 'KIP',
                        'VND' => 'VND',
                    ])
                    ->default('USD')
                    ->searchable(),
                DateTimePicker::make('spent_at')
                    ->label('Spent at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
