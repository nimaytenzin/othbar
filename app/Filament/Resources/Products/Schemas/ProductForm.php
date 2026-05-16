<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('summary')
                    ->rows(3)
                    ->columnSpanFull(),
                RichEditor::make('description')->columnSpanFull(),
                Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('collections')
                    ->relationship('collections', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                TextInput::make('sku')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('stock_quantity')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Toggle::make('allow_backorder')
                    ->default(false),
                Toggle::make('is_visible')
                    ->default(true),
                TextInput::make('price_minor')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->label('Price (minor units)')
                    ->helperText('28000 = Nu. 280 when using two decimal subdivisions.'),
                TextInput::make('currency_code')
                    ->default('BTN')
                    ->maxLength(8),
                SpatieMediaLibraryFileUpload::make('thumbnail')
                    ->collection('thumbnail')
                    ->image()
                    ->maxFiles(1)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('uploads')
                    ->collection('uploads')
                    ->image()
                    ->multiple()
                    ->columnSpanFull(),
            ]);
    }
}
