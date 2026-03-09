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

        $daySummaries = $trip->days
            ->sortBy('day_number')
            ->values()
            ->map(function ($day) use ($currencyResolver) {
                $totals = $day->expenses
                    ->groupBy($currencyResolver)
                    ->map(fn ($expenses) => $expenses->sum('amount'))
                    ->sortKeys();

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
                    'expenses' => $expenses,
                ];
            });

        return view('public.trip-summary', [
            'trip' => $trip,
            'totalsByCurrency' => $totalsByCurrency,
            'daySummaries' => $daySummaries,
        ]);
    }
}
