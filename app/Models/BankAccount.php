<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'label',
        'bank_name',
        'account_name',
        'account_number',
        'branch',
        'swift_or_code',
        'qr_path',
        'is_default',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (BankAccount $account): void {
            if ($account->is_default) {
                static::query()
                    ->where('id', '!=', $account->id)
                    ->update(['is_default' => false]);

                SiteSetting::query()->where('id', 1)->update([
                    'default_bank_account_id' => $account->id,
                ]);
                SiteSetting::clearCache();
            }
        });
    }

    public function displayLabel(): string
    {
        return $this->label ?: "{$this->bank_name} — {$this->account_number}";
    }
}
