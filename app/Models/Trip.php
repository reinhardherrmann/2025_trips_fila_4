<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    /** @use HasFactory<\Database\Factories\TripFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id','start_stock_id','target_stock_id','truck_id','trip_type_id','status_id','trip_number',
        'date','plan_date','duration','depot_vt','depot_nt','start','end','plan_distance',
        'distance_driven','start_km','end_km','status','remark'
    ];
}
