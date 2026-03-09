<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\TripDay;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                        if (! $state) {
                            return;
                        }

                        $tripDay = TripDay::query()
                            ->with('trip.exchangeRates')
                            ->find($state);

                        if (! $tripDay) {
                            return;
                        }

                        $tripCurrency = $tripDay->trip?->currency;
                        if ($tripCurrency) {
                            $set('currency', $tripCurrency);
                        }

                        $currency = $get('currency') ?? $tripCurrency;
                        if ($currency) {
                            $set('conversion_rate', $tripDay->trip?->getExchangeRateFor($currency));
                        }
                    })
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('category')
                    ->datalist([
                        'F&B',
                        'Flight',
                        'Transport',
                        'Accommodation',
                        'Activities',
                        'Entertainment',
                        'Shopping',
                        'Groceries',
                        'Tips',
                        'Fees',
                        'Tickets',
                        'Gifts',
                        'Attraction',
                        'Other',
                    ])
                    ->placeholder('Select or type category')
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
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                        $tripDayId = $get('trip_day_id');
                        if (! $tripDayId) {
                            return;
                        }

                        $tripDay = TripDay::query()
                            ->with('trip.exchangeRates')
                            ->find($tripDayId);

                        if (! $tripDay) {
                            return;
                        }

                        $set('conversion_rate', $tripDay->trip?->getExchangeRateFor($state));
                    }),
                TextInput::make('conversion_rate')
                    ->label('Conversion rate to trip currency')
                    ->numeric()
                    ->helperText('Optional. Enter 1 expense currency = ? trip currency'),
                DateTimePicker::make('spent_at')
                    ->label('Spent at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
