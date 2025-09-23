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
}
