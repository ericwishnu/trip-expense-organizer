<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $trip->name }} · Trip Summary</title>
    <meta name="description" content="Shared trip summary for {{ $trip->name }}.">
    @vite(['resources/css/filament/admin/theme.css'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <main class="mx-auto w-full max-w-4xl px-4 py-10">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">{{ $trip->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $trip->destination ?? 'No destination' }}
            </p>
        </div>

        <section class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-semibold">Trip summary</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-950">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Total spent</p>
                    @if ($totalsByCurrency->isEmpty())
                        <p class="text-xl font-semibold">—</p>
                    @else
                        <div class="space-y-1">
                            @foreach ($totalsByCurrency as $currency => $amount)
                                <p class="text-xl font-semibold">
                                    {{ $currency }} {{ number_format($amount, 2) }}
                                </p>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-950">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Total ({{ $baseCurrency }})</p>
                    @if ($baseTotal === null)
                        <p class="text-xl font-semibold">—</p>
                    @else
                        <p class="text-xl font-semibold">
                            {{ $baseCurrency }} {{ number_format($baseTotal, 2) }}
                        </p>
                    @endif
                </div>
                <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-950">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Trip days</p>
                    <p class="text-xl font-semibold">
                        {{ $daySummaries->count() }}
                    </p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Daily spending</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Read-only shared view.</p>
            </div>

            <div class="space-y-4 md:hidden">
                @forelse ($daySummaries as $day)
                    <div class="rounded-xl border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-700 dark:bg-gray-950">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold">
                                    Day {{ $day['day_number'] }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ optional($day['date'])->format('M j, Y') ?? '—' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs uppercase tracking-wider text-gray-500">Total</p>
                                <div class="space-y-1">
                                    @forelse ($day['totals'] as $currency => $amount)
                                        <p class="text-sm font-semibold">
                                            {{ $currency }} {{ number_format($amount, 2) }}
                                        </p>
                                    @empty
                                        <p class="text-sm font-semibold">—</p>
                                    @endforelse
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Base: {{ $baseCurrency }} {{ $day['base_total'] !== null ? number_format($day['base_total'], 2) : '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $day['title'] ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-500">Expenses: {{ $day['count'] }}</p>
                            @if (($day['expenses'] ?? collect())->isNotEmpty())
                                <details class="mt-2">
                                    <summary class="cursor-pointer text-xs text-amber-600 dark:text-amber-400">View expenses</summary>
                                    <ul class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-300">
                                        @foreach ($day['expenses'] as $expense)
                                            <li class="flex items-start justify-between gap-2">
                                                <span>
                                                    {{ $expense['title'] }}
                                                    @if (! empty($expense['category']))
                                                        <span class="text-gray-400">· {{ $expense['category'] }}</span>
                                                    @endif
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $expense['currency'] }} {{ number_format($expense['amount'], 2) }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500 dark:border-gray-700">
                        No days added yet.
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 md:block">
                <table class="min-w-full table-auto border border-gray-200 dark:border-gray-700 border-separate border-spacing-0">
                    <thead class="bg-gray-50 dark:bg-gray-950">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Day</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Title</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Expenses</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-gray-200 dark:border-gray-700">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($daySummaries as $day)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-r border-gray-200 dark:border-gray-700">
                                    Day {{ $day['day_number'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                    {{ optional($day['date'])->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                    <div class="space-y-2">
                                        <div>{{ $day['title'] ?? '—' }}</div>
                                        @if (($day['expenses'] ?? collect())->isNotEmpty())
                                            <details>
                                                <summary class="cursor-pointer text-xs text-amber-600 dark:text-amber-400">View expenses</summary>
                                                <ul class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-300">
                                                    @foreach ($day['expenses'] as $expense)
                                                        <li class="flex items-start justify-between gap-2">
                                                            <span>
                                                                {{ $expense['title'] }}
                                                                @if (! empty($expense['category']))
                                                                    <span class="text-gray-400">· {{ $expense['category'] }}</span>
                                                                @endif
                                                            </span>
                                                            <span class="font-semibold text-gray-900 dark:text-gray-100">
                                                                {{ $expense['currency'] }} {{ number_format($expense['amount'], 2) }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                    {{ $day['count'] }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                                    <div class="space-y-1">
                                        @forelse ($day['totals'] as $currency => $amount)
                                            <div>{{ $currency }} {{ number_format($amount, 2) }}</div>
                                        @empty
                                            <div>—</div>
                                        @endforelse
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        Base: {{ $baseCurrency }} {{ $day['base_total'] !== null ? number_format($day['base_total'], 2) : '—' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="5">
                                    No days added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
