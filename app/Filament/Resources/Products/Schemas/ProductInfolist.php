<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryImageEntry::make('thumbnail')
                    ->label('Thumbnail')
                    ->collection('thumbnail')
                    ->imageSize(128)
                    ->square()
                    ->columnSpanFull(),
                SpatieMediaLibraryImageEntry::make('uploads')
                    ->label('Gallery images')
                    ->collection('uploads')
                    ->imageSize(96)
                    ->square()
                    ->columnSpanFull(),
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('summary')->columnSpanFull(),
                TextEntry::make('description')->columnSpanFull()->html(),
                TextEntry::make('brand.name')->label('Brand'),
                TextEntry::make('categories_csv')
                    ->label('Categories')
                    ->state(fn (Product $record): string => $record->categories->pluck('name')->join(', ')),
                TextEntry::make('sku'),
                TextEntry::make('stock_quantity')->label('Stock'),
                IconEntry::make('allow_backorder')->boolean(),
                IconEntry::make('is_visible')->boolean(),
                TextEntry::make('price_minor')
                    ->label('Price (Nu.)')
                    ->formatStateUsing(fn ($state): string => 'Nu. '.number_format(((int) $state) / 100)),
                TextEntry::make('currency_code'),
            ]);
    }
}
