<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    /** @use HasFactory<\Database\Factories\TripFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id','start_stock_id','target_stock_id','truck_id','trip_type_id','status_id','trip_number',
        'date','plan_date','duration','depot_vt','depot_nt','start','end','plan_distance',
        'distance_driven','start_km','end_km','status','remark'
    ];

    protected $casts = [
        'date' => 'date',
        'plan_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Trip $trip) {
            $start = $trip->start_km;
            $end = $trip->end_km;

            // Nur berechnen, wenn beide Werte vorhanden sind
            if ($start !== null && $end !== null) {
                $trip->distance_driven = max(0, (int)$end - (int)$start);
            } else {
                // Je nach Wunsch: null lassen oder 0 setzen
                $trip->distance_driven = null; // oder: 0
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stopps(): HasMany
    {
        return $this->hasMany(Stopp::class);
    }

    public function startStock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'start_stock_id');
    }

    public function targetStock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'target_stock_id');
    }

    public function tripType(): BelongsTo
    {
        return $this->belongsTo(TripType::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}
