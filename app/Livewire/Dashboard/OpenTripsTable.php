<?php

namespace App\Livewire\Dashboard;

use App\Models\Status;
use App\Models\Trip;
use Livewire\Component;

class OpenTripsTable extends Component
{
    public $trips;

    public function mount(): void
    {
        $openId = Status::where('name', 'open')->value('id');

        $this->trips = Trip::query()
            ->with(['status'])
            ->withCount('stopps')
            ->where('user_id', auth()->id())
            ->when($openId, fn($q) => $q->where('status_id', '<=', 2))
            ->orderByDesc('date')
            ->limit(20)
            ->get();
        //ds($this->trips);
    }

    public function render()
    {
        return view('livewire.dashboard.open-trips-table');
    }
}
