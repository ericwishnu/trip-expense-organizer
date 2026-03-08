<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LatestTripStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $trip = Trip::query()
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->with(['days.expenses'])
            ->first();

        if (! $trip) {
            return [
                Stat::make('Latest trip', 'No trips yet')
                    ->description('Create a trip to see stats.'),
            ];
        }

        $total = $trip->days->flatMap(fn ($day) => $day->expenses)->sum('amount');
        $currency = $trip->currency ?? 'USD';
        $days = $trip->days->count();

        return [
            Stat::make('Latest trip', $trip->name)
                ->description($trip->destination ?: 'No destination'),
            Stat::make('Total spent', $currency . ' ' . number_format($total, 2))
                ->description($days . ' days'),
        ];
    }
}
