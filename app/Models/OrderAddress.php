<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderAddress extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'street_address',
        'city',
        'postal_code',
        'phone',
        'country_name',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
