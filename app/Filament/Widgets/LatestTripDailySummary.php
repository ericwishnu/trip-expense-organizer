<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class LatestTripDailySummary extends Widget
{
    protected string $view = 'filament.widgets.latest-trip-daily-summary';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public Collection $days;

    public string $tripName = '';

    public string $currency = 'USD';

    public function mount(): void
    {
        $trip = Trip::query()
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->with(['days.expenses'])
            ->first();

        if (! $trip) {
            $this->days = collect();
            return;
        }

        $this->tripName = $trip->name;
        $this->currency = $trip->currency ?? 'USD';

        $this->days = $trip->days
            ->sortBy('day_number')
            ->values()
            ->map(fn($day) => [
                'day_number' => $day->day_number,
                'date' => $day->date,
                'title' => $day->title,
                'total' => $day->expenses->sum('amount'),
                'count' => $day->expenses->count(),
            ]);
    }
}
