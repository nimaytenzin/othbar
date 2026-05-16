<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case Unsent = 'unsent';
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unsent => __('Unsent'),
            self::Pending => __('Pending'),
            self::Shipped => __('Shipped'),
            self::Delivered => __('Delivered'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Unsent => 'gray',
            self::Pending => 'warning',
            self::Shipped => 'info',
            self::Delivered => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Unsent => 'heroicon-o-inbox',
            self::Pending => 'heroicon-o-clock',
            self::Shipped => 'heroicon-o-truck',
            self::Delivered => 'heroicon-o-check',
        };
    }
}
