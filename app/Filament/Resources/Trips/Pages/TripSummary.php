<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class TripSummary extends Page
{
    use InteractsWithRecord;

    protected static string $resource = TripResource::class;

    protected string $view = 'filament.resources.trips.pages.trip-summary';

    public Collection $daySummaries;

    public Collection $categorySummaries;

    public float $total = 0.0;

    public string $currency = 'USD';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(TripResource::canView($this->record), 403);

        $this->record->loadMissing(['days.expenses']);

        $this->currency = $this->record->currency ?? 'USD';

        $this->total = 0.0;

        $this->daySummaries = $this->record->days
            ->sortBy('day_number')
            ->values()
            ->map(function ($day) {
                $dayTotal = $day->expenses->sum('amount');

                $this->total += $dayTotal;

                return [
                    'day_number' => $day->day_number,
                    'date' => $day->date,
                    'title' => $day->title,
                    'total' => $dayTotal,
                    'count' => $day->expenses->count(),
                ];
            });

        $allExpenses = $this->record->days
            ->flatMap(fn ($day) => $day->expenses);

        $this->categorySummaries = $allExpenses
            ->groupBy(function ($expense) {
                $category = trim((string) $expense->category);

                return $category !== '' ? $category : 'Uncategorized';
            })
            ->map(function ($expenses, $category) {
                $amount = $expenses->sum('amount');

                return [
                    'category' => $category,
                    'amount' => $amount,
                    'count' => $expenses->count(),
                ];
            })
            ->sortByDesc('amount')
            ->values();
    }
}
