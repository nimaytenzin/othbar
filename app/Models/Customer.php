<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'display_name',
        'email',
        'phone',
        'gst_tpn',
        'billing_address_line1',
        'billing_address_line2',
        'billing_city',
        'billing_district',
        'billing_postal_code',
        'notes',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function billingAddressLines(): string
    {
        return collect([
            $this->billing_address_line1,
            $this->billing_address_line2,
            collect([$this->billing_city, $this->billing_district, $this->billing_postal_code])
                ->filter()
                ->implode(', '),
        ])->filter()->implode("\n");
    }
}
