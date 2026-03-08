<x-filament-widgets::widget>
    <x-filament::section heading="Latest trip daily summary">
        @if ($days->isEmpty())
            <p class="text-sm text-gray-500">No trips yet.</p>
        @else
            <p class="text-sm text-gray-500 mb-4">{{ $tripName }}</p>
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full table-auto border border-gray-200 dark:border-gray-700 border-separate border-spacing-0">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Day</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Title</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-r border-gray-200 dark:border-gray-700">Expenses</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-gray-200 dark:border-gray-700">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($days as $day)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-r border-gray-200 dark:border-gray-700">
                                    Day {{ $day['day_number'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                    {{ optional($day['date'])->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                    {{ $day['title'] ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600 dark:text-gray-300 border-b border-r border-gray-200 dark:border-gray-700">
                                    {{ $day['count'] }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                                    {{ $currency }} {{ number_format($day['total'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
