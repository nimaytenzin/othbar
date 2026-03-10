<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Shopper\Core\Enum\GenderType;
use Shopper\Core\Models\Address;
use Shopper\Core\Models\Order;
use Shopper\Models\Contracts\ShopperUser;
use Shopper\Traits\InteractsWithShopper;

class User extends Authenticatable implements ShopperUser
{
    use HasFactory, InteractsWithShopper, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'gender',
        'phone_number',
        'birth_date',
        'avatar_type',
        'avatar_location',
        'timezone',
        'opt_in',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'opt_in' => 'boolean',
            'gender' => GenderType::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('administrator');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isVerified(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'customer_id');
    }

    public function scopeAdministrators(Builder $query): Builder
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'administrator'));
    }

    public function scopeCustomers(Builder $query): Builder
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'user'));
    }
}
