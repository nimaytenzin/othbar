<?php

namespace App\Enums;

enum CouponType: string
{
    case Percent = 'percent';
    case FixedMinor = 'fixed_minor';

    public function getLabel(): string
    {
        return match ($this) {
            self::Percent => 'Percentage (%)',
            self::FixedMinor => 'Fixed amount (Nu.)',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $labels = [];

        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->getLabel();
        }

        return $labels;
    }
}
