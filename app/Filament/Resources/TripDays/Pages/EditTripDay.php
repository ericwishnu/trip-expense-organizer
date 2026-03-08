<?php

namespace App\Filament\Resources\TripDays\Pages;

use App\Filament\Resources\TripDays\TripDayResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTripDay extends EditRecord
{
    protected static string $resource = TripDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
