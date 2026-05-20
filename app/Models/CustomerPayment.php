<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPayment extends Model
{
    protected $fillable = [
        'number',
        'customer_id',
        'payment_date',
        'amount_minor',
        'currency_code',
        'payment_method',
        'bank_account_id',
        'reference',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function allocatedMinor(): int
    {
        return (int) $this->allocations()->sum('amount_minor');
    }

    public function unallocatedMinor(): int
    {
        return max(0, (int) $this->amount_minor - $this->allocatedMinor());
    }
}
