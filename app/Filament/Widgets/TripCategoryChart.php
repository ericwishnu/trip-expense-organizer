<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use Filament\Widgets\ChartWidget;

class TripCategoryChart extends ChartWidget
{
    // protected ?string $heading = 'Spending by category';

    protected static bool $isDiscovered = false;

    protected int | string | array $columnSpan = 'full';

    public ?int $recordId = null;

    public ?string $filter = null;

    public function mount(): void
    {
        if (! $this->recordId) {
            return;
        }

        $trip = Trip::query()
            ->where('user_id', auth()->id())
            ->find($this->recordId);

        if (! $trip) {
            return;
        }

        $trip->loadMissing(['days.expenses']);

        $availableCurrencies = $trip->days
            ->flatMap(fn ($day) => $day->expenses)
            ->map(fn ($expense) => $expense->currency ?? $trip->currency ?? 'USD')
            ->unique()
            ->values();

        $defaultCurrency = $trip->currency ?? $availableCurrencies->first();

        $this->filter = $availableCurrencies->contains($defaultCurrency)
            ? $defaultCurrency
            : $availableCurrencies->first();
    }

    protected function getFilters(): ?array
    {
        if (! $this->recordId) {
            return null;
        }

        $trip = Trip::query()
            ->where('user_id', auth()->id())
            ->find($this->recordId);

        if (! $trip) {
            return null;
        }

        $trip->loadMissing(['days.expenses']);

        $currencies = $trip->days
            ->flatMap(fn ($day) => $day->expenses)
            ->map(fn ($expense) => $expense->currency ?? $trip->currency ?? 'USD')
            ->unique()
            ->sort()
            ->values()
            ->all();

        return collect($currencies)
            ->mapWithKeys(fn ($currency) => [$currency => $currency])
            ->all();
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        if (! $this->recordId) {
            return [
                'labels' => [],
                'datasets' => [
                    [
                        'data' => [],
                        'backgroundColor' => [],
                    ],
                ],
            ];
        }

        $trip = Trip::query()
            ->where('user_id', auth()->id())
            ->findOrFail($this->recordId);

        $trip->loadMissing(['days.expenses']);

        $expenses = $trip->days->flatMap(fn ($day) => $day->expenses);

        $availableCurrencies = $expenses
            ->map(fn ($expense) => $expense->currency ?? $trip->currency ?? 'USD')
            ->unique()
            ->values();

        $selectedCurrency = $this->filter ?: $availableCurrencies->first();

        $expenses = $expenses->filter(fn ($expense) => ($expense->currency ?? $trip->currency ?? 'USD') === $selectedCurrency);

        $groups = $expenses->groupBy(function ($expense) use ($trip) {
            $currency = $expense->currency ?? $trip->currency ?? 'USD';
            $category = trim((string) $expense->category);

            $category = $category !== '' ? $category : 'Uncategorized';

            return $category . '||' . $currency;
        });

        $labels = $groups->keys()->map(function ($key) {
            [$category] = explode('||', $key);

            return $category;
        })->values()->all();
        $data = $groups->map(fn ($items) => $items->sum('amount'))->values()->all();

        $colors = [
            '#f59e0b',
            '#f97316',
            '#fb7185',
            '#38bdf8',
            '#34d399',
            '#a78bfa',
            '#facc15',
            '#22c55e',
        ];

        $backgrounds = collect($labels)
            ->map(fn ($_, $index) => $colors[$index % count($colors)])
            ->all();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgrounds,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
