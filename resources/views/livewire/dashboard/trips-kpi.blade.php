<div class="rounded-xl border border-neutral-200 bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-900">
    <div class="text-sm text-neutral-600 dark:text-neutral-300">Touren der letzten 7 Tage: <span
            class="ps-3 font-semibold">{{ $countLast7 }}</span></div>
    <div class="mt-1 py-1 text-3xl font-bold text-neutral-900 dark:text-neutral-100"></div>
    <div class="mt-1 py-1 text-xs text-neutral-500 dark:text-neutral-400">Touren pro Tag: <span
            class="ps-3 font-semibold">{{ $tripsPerDay }}</span></div>
    <div class="mt-1 py-1 text-xs text-neutral-500 dark:text-neutral-400">Stopps pro Tour: <span
            class="ps-3 font-semibold">{{ $avgStops }}</span></div>
</div>
