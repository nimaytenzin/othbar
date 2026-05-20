<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Partial = 'partial';
    case Paid = 'paid';
    case Void = 'void';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Sent => __('Sent'),
            self::Partial => __('Partially paid'),
            self::Paid => __('Paid'),
            self::Void => __('Void'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Sent => 'info',
            self::Partial => 'warning',
            self::Paid => 'success',
            self::Void => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil-square',
            self::Sent => 'heroicon-o-paper-airplane',
            self::Partial => 'heroicon-o-banknotes',
            self::Paid => 'heroicon-o-check-circle',
            self::Void => 'heroicon-o-x-circle',
        };
    }
}
