<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use Filament\Widgets\ChartWidget;

class LatestTripCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Latest trip spending by category';
    protected static ?int $sort = 3;
    public static function canView(): bool
    {
        $trip = Trip::query()
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->with(['days.expenses'])
            ->first();

        if (! $trip) {
            return false;
        }

        return $trip->days->flatMap(fn($day) => $day->expenses)->isNotEmpty();
    }
    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $trip = Trip::query()
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->with(['days.expenses'])
            ->first();

        if (! $trip) {
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

        $expenses = $trip->days->flatMap(fn($day) => $day->expenses);

        $groups = $expenses->groupBy(function ($expense) use ($trip) {
            $currency = $expense->currency ?? $trip->currency ?? 'USD';
            $category = trim((string) $expense->category);

            $category = $category !== '' ? $category : 'Uncategorized';

            return $currency . '||' . $category;
        });

        $labels = $groups->keys()->map(function ($key) {
            [$currency, $category] = explode('||', $key);

            return $category . ' (' . $currency . ')';
        })->values()->all();
        $data = $groups->map(fn($items) => $items->sum('amount'))->values()->all();

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
            ->map(fn($_, $index) => $colors[$index % count($colors)])
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
