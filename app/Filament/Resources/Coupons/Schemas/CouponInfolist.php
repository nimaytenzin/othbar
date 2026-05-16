<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code'),
                TextEntry::make('type'),
                TextEntry::make('value'),
                TextEntry::make('starts_at')->dateTime(),
                TextEntry::make('ends_at')->dateTime(),
                TextEntry::make('max_uses'),
                TextEntry::make('uses_count'),
                IconEntry::make('is_active')->boolean(),
            ]);
    }
}
