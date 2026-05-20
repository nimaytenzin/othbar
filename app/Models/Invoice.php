<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'number',
        'customer_id',
        'order_id',
        'status',
        'issue_date',
        'due_date',
        'subtotal_minor',
        'discount_minor',
        'tax_minor',
        'total_minor',
        'amount_paid_minor',
        'currency_code',
        'notes',
        'terms_snapshot',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function balanceDueMinor(): int
    {
        return max(0, (int) $this->total_minor - (int) $this->amount_paid_minor);
    }

    public function isOverdue(): bool
    {
        return $this->balanceDueMinor() > 0
            && $this->status !== InvoiceStatus::Void
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    public function daysOverdue(): ?int
    {
        if (! $this->isOverdue()) {
            return null;
        }

        return (int) $this->due_date->diffInDays(today());
    }

    public function scopeOutstanding(Builder $query): Builder
    {
        return $query
            ->where('status', '!=', InvoiceStatus::Void)
            ->whereColumn('amount_paid_minor', '<', 'total_minor');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->outstanding()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString());
    }

    public function scopePartiallyPaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Partial);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Paid);
    }

    public function scopeVoid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Void);
    }

    public function scopeFromOrder(Builder $query): Builder
    {
        return $query->whereNotNull('order_id');
    }

    public function scopeManual(Builder $query): Builder
    {
        return $query->whereNull('order_id');
    }

    public function scopeIssuedBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        if (filled($from)) {
            $query->whereDate('issue_date', '>=', $from);
        }

        if (filled($to)) {
            $query->whereDate('issue_date', '<=', $to);
        }

        return $query;
    }

    public static function outstandingBalanceMinor(): int
    {
        return (int) static::query()
            ->outstanding()
            ->selectRaw('COALESCE(SUM(total_minor - amount_paid_minor), 0) as balance')
            ->value('balance');
    }

    public static function overdueBalanceMinor(): int
    {
        return (int) static::query()
            ->overdue()
            ->selectRaw('COALESCE(SUM(total_minor - amount_paid_minor), 0) as balance')
            ->value('balance');
    }

    public static function formatMinor(int $minor): string
    {
        return 'Nu. '.number_format($minor / 100, 2);
    }

    public function canVoid(): bool
    {
        if ($this->status === InvoiceStatus::Void || $this->status === InvoiceStatus::Paid) {
            return false;
        }

        return (int) $this->amount_paid_minor === 0
            && ! $this->allocations()->exists();
    }

    public function voidBlockReason(): ?string
    {
        if ($this->status === InvoiceStatus::Void) {
            return 'This invoice is already voided.';
        }

        if ($this->status === InvoiceStatus::Paid) {
            return 'Paid invoices cannot be voided. Issue a credit note to correct amounts after payment.';
        }

        if ((int) $this->amount_paid_minor > 0 || $this->allocations()->exists()) {
            return 'Invoices with recorded payments cannot be voided. Reverse allocations or issue a credit note instead.';
        }

        return null;
    }

    public function recalculatePaidStatus(): void
    {
        $paid = (int) $this->amount_paid_minor;
        $total = (int) $this->total_minor;

        if ($this->status === InvoiceStatus::Void) {
            return;
        }

        if ($paid <= 0) {
            $this->status = InvoiceStatus::Sent;
        } elseif ($paid >= $total) {
            $this->status = InvoiceStatus::Paid;
        } else {
            $this->status = InvoiceStatus::Partial;
        }
    }
}
