<div
    class="rounded-xl border border-neutral-200 p-4 bg-white shadow dark:border-neutral-700 dark:bg-neutral-900 overflow-hidden">
    <div class="ps-4 pt-4 text-sm font-semibold text-neutral-600 dark:text-neutral-300">offene Touren</div>
    <table class="pe-4 w-full text-sm">

        <thead class="bg-neutral-50 dark:bg-neutral-800/50">
        <tr>
            <th class="px-4 py-2 text-left font-medium">Tour</th>
            <th class="px-4 py-2 text-left font-medium">Date</th>
            <th class="px-4 py-2 text-left font-medium">Stops</th>
            <th class="px-4 py-2 text-left font-medium">Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($trips as $trip)
            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 cursor-pointer"
                onclick="window.location='{{ route('trips.edit', $trip) }}'">
                <td class="px-4 py-2">{{ $trip->trip_number }}</td>
                <td class="px-4 py-2">{{ optional($trip->date)->format('d.m.Y') }}</td>
                <td class="px-4 py-2">{{ $trip->stopps_count }}</td>
                <td class="px-4 py-2">
            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs
              {{ $trip->status?->name === 'open' ? 'bg-red-100 text-red-800' : (
                   $trip->status?->name === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}
            ">
              {{ $trip->status?->value }}
            </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-neutral-500 dark:text-neutral-400">Keine offenen
                    Touren
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
