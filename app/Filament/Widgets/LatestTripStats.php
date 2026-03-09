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
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('collaborators', fn ($collabQuery) => $collabQuery->where('users.id', auth()->id()));
            })
            ->latest('created_at')
            ->with(['days.expenses'])
            ->first();

        if (! $trip) {
            return [
                Stat::make('Latest trip', 'No trips yet')
                    ->description('Create a trip to see stats.'),
            ];
        }

        $totalsByCurrency = $trip->days
            ->flatMap(fn ($day) => $day->expenses)
            ->groupBy(fn ($expense) => $expense->currency ?? $trip->currency ?? 'USD')
            ->map(fn ($expenses) => $expenses->sum('amount'))
            ->sortKeys();

        $totalLabel = $totalsByCurrency->isEmpty()
            ? '—'
            : $totalsByCurrency
                ->map(fn ($amount, $currency) => $currency . ' ' . number_format($amount, 2))
                ->implode(' • ');
        $days = $trip->days->count();

        return [
            Stat::make('Latest trip', $trip->name)
                ->description($trip->destination ?: 'No destination'),
            Stat::make('Total spent', $totalLabel)
                ->description($days . ' days'),
        ];
    }
}
