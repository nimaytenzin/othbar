<?php

namespace App\Enums;

enum CouponType: string
{
    case Percent = 'percent';
    case FixedMinor = 'fixed_minor';
}
