<?php

namespace App\Http\Controllers;

use App\Models\TripShareLink;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripShareController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $share = TripShareLink::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_if($share->is_expired, 404);

        $share->increment('view_count');

        $trip = $share->trip->loadMissing(['days.expenses']);

        $currencyResolver = fn ($expense) => $expense->currency
            ?? $trip->currency
            ?? 'USD';

        $allExpenses = $trip->days->flatMap(fn ($day) => $day->expenses);

        $totalsByCurrency = $allExpenses
            ->groupBy($currencyResolver)
            ->map(fn ($expenses) => $expenses->sum('amount'))
            ->sortKeys();

        $baseCurrency = $trip->currency ?? 'USD';
        $rateResolver = function ($expense) use ($trip): ?string {
            $currency = $expense->currency ?? $trip->currency ?? 'USD';

            if ($currency === ($trip->currency ?? 'USD')) {
                return '1';
            }

            return $expense->conversion_rate
                ?? $trip->getExchangeRateFor($currency);
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

        $baseTotal = $baseTotalAvailable ? $baseTotal : null;

        $daySummaries = $trip->days
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

        return view('public.trip-summary', [
            'trip' => $trip,
            'totalsByCurrency' => $totalsByCurrency,
            'baseCurrency' => $baseCurrency,
            'baseTotal' => $baseTotal,
            'daySummaries' => $daySummaries,
        ]);
    }
}
