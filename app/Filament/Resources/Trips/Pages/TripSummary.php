<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TripSummary extends Page
{
    use InteractsWithRecord;

    protected static string $resource = TripResource::class;

    protected string $view = 'filament.resources.trips.pages.trip-summary';

    public Collection $daySummaries;

    public Collection $categorySummaries;

    public Collection $totalsByCurrency;

    public string $baseCurrency = 'USD';

    public ?float $baseTotal = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Expenses')
                ->action('export')
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(TripResource::canView($this->record), 403);

        $this->record->loadMissing(['days.expenses']);

        $this->baseCurrency = $this->record->currency ?? 'USD';

        $currencyResolver = fn ($expense) => $expense->currency
            ?? $this->record->currency
            ?? 'USD';

        $allExpenses = $this->record->days
            ->flatMap(fn ($day) => $day->expenses);

        $this->totalsByCurrency = $allExpenses
            ->groupBy($currencyResolver)
            ->map(fn ($expenses) => $expenses->sum('amount'))
            ->sortKeys();

        $rateResolver = function ($expense): ?string {
            $currency = $expense->currency ?? $this->record->currency ?? 'USD';

            if ($currency === ($this->record->currency ?? 'USD')) {
                return '1';
            }

            return $expense->conversion_rate
                ?? $this->record->getExchangeRateFor($currency);
        };

        $baseTotal = 0.0;
        $baseTotalAvailable = false;

        foreach ($allExpenses as $expense) {
            $rate = $rateResolver($expense);

            if ($rate === null) {
                continue;
            }

            $baseTotalAvailable = true;
            $baseTotal += ((float) $expense->amount) * (float) $rate;
        }

        $this->baseTotal = $baseTotalAvailable ? $baseTotal : null;

        $this->daySummaries = $this->record->days
            ->sortBy('day_number')
            ->values()
            ->map(function ($day) use ($currencyResolver, $rateResolver) {
                $totals = $day->expenses
                    ->groupBy($currencyResolver)
                    ->map(fn ($expenses) => $expenses->sum('amount'))
                    ->sortKeys();

                $baseDayTotal = 0.0;
                $baseDayAvailable = false;

                foreach ($day->expenses as $expense) {
                    $rate = $rateResolver($expense);

                    if ($rate === null) {
                        continue;
                    }

                    $baseDayAvailable = true;
                    $baseDayTotal += ((float) $expense->amount) * (float) $rate;
                }

                $expenses = $day->expenses
                    ->map(function ($expense) use ($currencyResolver) {
                        return [
                            'title' => $expense->title,
                            'category' => $expense->category,
                            'amount' => $expense->amount,
                            'currency' => $currencyResolver($expense),
                            'spent_at' => $expense->spent_at,
                        ];
                    });

                return [
                    'day_number' => $day->day_number,
                    'date' => $day->date,
                    'title' => $day->title,
                    'totals' => $totals,
                    'count' => $day->expenses->count(),
                    'base_total' => $baseDayAvailable ? $baseDayTotal : null,
                    'expenses' => $expenses,
                ];
            });

        $this->categorySummaries = $allExpenses
            ->groupBy(function ($expense) use ($currencyResolver) {
                $currency = $currencyResolver($expense);
                $category = trim((string) $expense->category);

                $category = $category !== '' ? $category : 'Uncategorized';

                return $currency . '||' . $category;
            })
            ->map(function ($expenses, $key) {
                [$currency, $category] = explode('||', $key);
                $amount = $expenses->sum('amount');

                return [
                    'currency' => $currency,
                    'category' => $category,
                    'amount' => $amount,
                    'count' => $expenses->count(),
                ];
            })
            ->sortByDesc('amount')
            ->values();
    }

    public function export(): StreamedResponse
    {
        abort_unless(TripResource::canView($this->record), 403);

        $trip = $this->record->loadMissing(['days.expenses']);

        $fileName = Str::of($trip->name ?: 'trip')
            ->slug('-')
            ->append('-expenses.csv')
            ->toString();

        return response()->streamDownload(function () use ($trip) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Trip',
                'Day #',
                'Day Date',
                'Day Title',
                'Expense Title',
                'Category',
                'Amount',
                'Currency',
                'Spent At',
                'Notes',
            ]);

            $days = $trip->days->sortBy('day_number');

            foreach ($days as $day) {
                foreach ($day->expenses as $expense) {
                    $dayDate = $day->date instanceof \Carbon\CarbonInterface
                        ? $day->date->toDateString()
                        : (string) $day->date;

                    $spentAt = $expense->spent_at instanceof \Carbon\CarbonInterface
                        ? $expense->spent_at->toDateTimeString()
                        : (string) $expense->spent_at;

                    fputcsv($handle, [
                        $trip->name,
                        $day->day_number,
                        $dayDate,
                        $day->title,
                        $expense->title,
                        $expense->category,
                        $expense->amount,
                        $expense->currency ?? $trip->currency ?? 'USD',
                        $spentAt,
                        $expense->notes,
                    ]);
                }
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
