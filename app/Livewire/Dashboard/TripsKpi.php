<?php

namespace App\Livewire\Dashboard;

use App\Models\Trip;
use Livewire\Component;

class TripsKpi extends Component
{
    public int $countLast7 = 0;
    public string $avgStops = '0.0';
    public string $tripsPerDay = '0.0';

    public function mount(): void
    {
        $from = now()->subDays(7)->startOfDay();

        $q = Trip::query()
            ->where('user_id', auth()->id())
            ->where(function ($q) use ($from) {
                $q->whereDate('date', '>=', $from)
                    ->orWhereDate('created_at', '>=', $from);
            })
            ->withCount('stopps');

        $trips = (clone $q)->get();

        $this->countLast7 = $trips->count();

        $avg = $trips->avg('stopps_count');
        $this->avgStops = $avg ? number_format((float)$avg, 1) : '0.0';

        $daysWithTrips = $trips
            ->map(fn($t) => optional($t->date ?? $t->created_at)->toDateString())
            ->filter()
            ->unique()
            ->count();

        $this->tripsPerDay = $daysWithTrips > 0
            ? number_format($this->countLast7 / $daysWithTrips, 1)
            : '0.0';
    }

    public function render()
    {
        return view('livewire.dashboard.trips-kpi');
    }
}
