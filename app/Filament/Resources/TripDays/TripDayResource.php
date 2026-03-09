<?php

namespace App\Filament\Resources\TripDays;

use App\Filament\Resources\TripDays\Pages\CreateTripDay;
use App\Filament\Resources\TripDays\Pages\EditTripDay;
use App\Filament\Resources\TripDays\Pages\ListTripDays;
use App\Filament\Resources\TripDays\RelationManagers\ExpensesRelationManager;
use App\Filament\Resources\TripDays\Schemas\TripDayForm;
use App\Filament\Resources\TripDays\Tables\TripDaysTable;
use App\Models\TripDay;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TripDayResource extends Resource
{
    protected static ?string $model = TripDay::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return TripDayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripDaysTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ExpensesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('trip', function (Builder $query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('collaborators', fn (Builder $collabQuery) => $collabQuery->where('users.id', auth()->id()));
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTripDays::route('/'),
            'create' => CreateTripDay::route('/create'),
            'edit' => EditTripDay::route('/{record}/edit'),
        ];
    }
}
