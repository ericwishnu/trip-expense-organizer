<?php

namespace App\Filament\Resources\Trips;

use App\Filament\Resources\Trips\Pages\CreateTrip;
use App\Filament\Resources\Trips\Pages\EditTrip;
use App\Filament\Resources\Trips\Pages\ListTrips;
use App\Filament\Resources\Trips\Pages\TripSummary;
use App\Filament\Resources\Trips\RelationManagers\TripDaysRelationManager;
use App\Filament\Resources\Trips\RelationManagers\TripInvitesRelationManager;
use App\Filament\Resources\Trips\RelationManagers\TripCollaboratorsRelationManager;
use App\Filament\Resources\Trips\RelationManagers\TripShareLinksRelationManager;
use App\Filament\Resources\Trips\Schemas\TripForm;
use App\Filament\Resources\Trips\Tables\TripsTable;
use App\Models\Trip;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TripDaysRelationManager::class,
            TripCollaboratorsRelationManager::class,
            TripInvitesRelationManager::class,
            TripShareLinksRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function (Builder $query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('collaborators', fn (Builder $collabQuery) => $collabQuery->where('users.id', auth()->id()));
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrips::route('/'),
            'create' => CreateTrip::route('/create'),
            'edit' => EditTrip::route('/{record}/edit'),
            'summary' => TripSummary::route('/{record}/summary'),
        ];
    }
}
