<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'shift_type_id',
        'work_date',
        'start_time',
        'end_time',
        'actual_minutes',
        'target_minutes',
        'difference_minutes',
        'remark',
        'deletded_at'
    ];

    protected $casts = [
        'work_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shiftType(): BelongsTo
    {
        return $this->belongsTo(ShiftType::class);
    }

    // Berechne tatsächliche Arbeitszeit
    public function calculateActualMinutes(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        // Wenn Schicht über Mitternacht geht
        if ($end->lessThan($start)) {
            $end->addDay();
        }

        return $start->diffInMinutes($end);
    }

    // Berechne Differenz zwischen Ist und Soll
    public function calculateDifference(): void
    {
        $this->actual_minutes = $this->calculateActualMinutes();
        $this->target_minutes = $this->shiftType?->duration_minutes ?? 0;
        $this->difference_minutes = $this->actual_minutes - $this->target_minutes;
    }
}
