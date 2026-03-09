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

        $baseCurrency = $trip->currency ?? 'USD';
        $baseTotal = 0.0;
        $baseTotalAvailable = false;

        foreach ($trip->days->flatMap(fn ($day) => $day->expenses) as $expense) {
            $currency = $expense->currency ?? $trip->currency ?? 'USD';
            $rate = $currency === $baseCurrency
                ? 1
                : ($expense->conversion_rate ?? $trip->getExchangeRateFor($currency));

            if ($rate === null) {
                continue;
            }

            $baseTotalAvailable = true;
            $baseTotal += ((float) $expense->amount) * (float) $rate;
        }

        $baseTotalLabel = $baseTotalAvailable
            ? $baseCurrency . ' ' . number_format($baseTotal, 2)
            : '—';

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
            Stat::make('Total (base)', $baseTotalLabel)
                ->description($baseCurrency . ' base'),
        ];
    }
}
