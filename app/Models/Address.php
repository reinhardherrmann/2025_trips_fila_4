<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;

    protected $fillable = [
        'remark_id','street','postal_code','city','district','country','image', 'remark'
    ];

    public function store():HasMany
    {
        return $this->hasMany(Store::class, 'address_id');
    }

    public function navStore(): HasMany
    {
        return $this->hasMany(Store::class, 'nav_id');
    }
}
