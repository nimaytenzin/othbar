<?php

namespace App\Enums;

enum BusinessType: string
{
    case Retail = 'retail';
    case Manufacturing = 'manufacturing';
    case Services = 'services';
    case Wholesale = 'wholesale';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Retail => 'Retail',
            self::Manufacturing => 'Manufacturing',
            self::Services => 'Services',
            self::Wholesale => 'Wholesale',
            self::Other => 'Other',
        };
    }
}
