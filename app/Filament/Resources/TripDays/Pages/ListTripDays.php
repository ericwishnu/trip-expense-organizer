<?php

namespace App\Filament\Resources\TripDays\Pages;

use App\Filament\Resources\TripDays\TripDayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTripDays extends ListRecords
{
    protected static string $resource = TripDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
