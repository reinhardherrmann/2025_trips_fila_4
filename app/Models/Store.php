<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory;

    use SoftDeletes;
    protected $fillable = ['brand_id','address_id','nav_id','cost_center_number',
        'name','phone_number','is_active','has_ramp','remark', 'images'];


    public function brand():BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function address():BelongsTo{
        return $this->belongsTo(Address::class);
    }
    public function navAddress():BelongsTo{
        return $this->belongsTo(Address::class, 'nav_id');
    }
}
