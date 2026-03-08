<x-filament-panels::page>
    <div class="grid gap-6 grid-cols-12">
        <div class="col-span-12 lg:col-span-6">
            <x-filament::section heading="Trip summary">
                <div class="flex flex-col gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $record->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $record->destination ?? 'No destination' }}
                        </p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-900">
                            <p class="text-xs uppercase tracking-wider text-gray-500">Total spent</p>
                            @if ($totalsByCurrency->isEmpty())
                                <p class="text-xl font-semibold text-gray-900 dark:text-white">—</p>
                            @else
                                <div class="space-y-1">
                                    @foreach ($totalsByCurrency as $currency => $amount)
                                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ $currency }} {{ number_format($amount, 2) }}
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-900">
                            <p class="text-xs uppercase tracking-wider text-gray-500">Trip days</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $daySummaries->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>



        <div class="col-span-12 lg:col-span-6 ">
            <x-filament::section heading="Daily spending" description="Track expenses for each day of the trip.">
                <div class="space-y-4 md:hidden ">
                    @forelse ($daySummaries as $day)
                        <div class="rounded-xl border border-gray-200 bg-white p-2 mb-2 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                            <div class="flex items-start justify-between gap-4 ">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        Day {{ $day['day_number'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ optional($day['date'])->format('M j, Y') ?? '—' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs uppercase tracking-wider text-gray-500">Total</p>
                                    <div class="space-y-1">
                                        @forelse ($day['totals'] as $currency => $amount)
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $currency }} {{ number_format($amount, 2) }}
                                            </p>
                                        @empty
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">—</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $day['title'] ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-500">Expenses: {{ $day['count'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500 dark:border-gray-700">
                            No days added yet.
                        </div>
                    @endforelse
                </div>
                <div class="hidden overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 md:block">
                    <table
                        class="min-w-full table-auto border border-gray-200 dark:border-gray-700 border-separate border-spacing-0">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">
                                    Day</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">
                                    Date</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">
                                    Title</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">
                                    Expenses</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($daySummaries as $day)
                                <tr>
                                    <td
                                        class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-r border-gray-200 dark:border-gray-700">
                                        Day {{ $day['day_number'] }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                        {{ optional($day['date'])->format('M j, Y') ?? '—' }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                        {{ $day['title'] ?? '—' }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                        {{ $day['count'] }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                                        <div class="space-y-1">
                                            @forelse ($day['totals'] as $currency => $amount)
                                                <div>{{ $currency }} {{ number_format($amount, 2) }}</div>
                                            @empty
                                                <div>—</div>
                                            @endforelse
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
            </x-filament::section>
        </div>
        <div class="col-span-12 lg:col-span-6">
            <x-filament::section heading="Spending by category">
                @livewire(\App\Filament\Widgets\TripCategoryChart::class, ['recordId' => $record->id])
            </x-filament::section>
        </div>
        
    </div>
</x-filament-panels::page>
