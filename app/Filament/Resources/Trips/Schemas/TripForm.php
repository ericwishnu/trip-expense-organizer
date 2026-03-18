<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('destination')
                    ->maxLength(255),
                Grid::make(2)->schema([
                    DatePicker::make('start_date')
                        ->label('Start date'),
                    DatePicker::make('end_date')
                        ->label('End date'),
                ]),
                Grid::make(2)->schema([
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
                        ->default('USD')
                        ->required()
                        ->searchable(),
                    TextInput::make('budget')
                        ->numeric()
                        ->prefix('$')
                        ->label('Budget'),
                ]),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
