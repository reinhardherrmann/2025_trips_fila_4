<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'start_time',
        'end_time',
        'duration_minutes',
        'break_minutes',
        'remark',
        'is_active',
        'deleted_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function workRecords()
    {
        return $this->hasMany(WorkRecord::class);
    }
}
