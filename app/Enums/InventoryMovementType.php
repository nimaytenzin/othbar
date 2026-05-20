<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case Sale = 'sale';
    case Restock = 'restock';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'Sale',
            self::Restock => 'Restock',
            self::Adjustment => 'Adjustment',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Sale => 'danger',
            self::Restock => 'success',
            self::Adjustment => 'warning',
        };
    }
}
