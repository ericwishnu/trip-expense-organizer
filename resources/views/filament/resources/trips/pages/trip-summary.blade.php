<x-filament-panels::page>
    <div class="grid gap-6 grid-cols-12 lg:grid-cols-12">
        <div class="col-span-6 lg:col-span-6">
            <x-filament::section heading="Trip summary">
                <div class="flex flex-col gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $record->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $record->destination ?? 'No destination' }}
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-900">
                            <p class="text-xs uppercase tracking-wider text-gray-500">Total spent</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $currency }} {{ number_format($total, 2) }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-900">
                            <p class="text-xs uppercase tracking-wider text-gray-500">Trip days</p>
                            
                                {{ $daySummaries->count() }}
                            
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>



        <div class="col-span-12 lg:col-span-6">
            <x-filament::section heading="Daily spending" description="Track expenses for each day of the trip.">
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
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
                                        {{ $currency }} {{ number_format($day['total'], 2) }}
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
        <div class="col-span-6 lg:col-span-6">
            <x-filament::section heading="Spending by category">
                @livewire(\App\Filament\Widgets\TripCategoryChart::class, ['recordId' => $record->id])
            </x-filament::section>
        </div>
        <div class="col-span-12 lg:col-span-6">
            <x-filament::section heading="More details">
                <div class="text-sm text-gray-500">
                    Add more widgets here.
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
