<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    /** @use HasFactory<\Database\Factories\TruckFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'manufacturer_id', 'trailer_id', 'truck_number', 'type', 'licence_plate', 'capacity', 'height', 'weight', 'is_active', 'remark', 'image'
    ];

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}
