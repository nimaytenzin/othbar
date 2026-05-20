<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Product images')
                    ->description('Thumbnail appears on the shop and home page. Gallery images show on the product detail page.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->collection('thumbnail')
                            ->disk('public')
                            ->image()
                            ->maxFiles(1)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('uploads')
                            ->label('Gallery')
                            ->collection('uploads')
                            ->disk('public')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Basic information')
                    ->icon(Heroicon::OutlinedCube)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Textarea::make('summary')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('description')->columnSpanFull(),
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),

                Section::make('Catalog & visibility')
                    ->icon(Heroicon::OutlinedTag)
                    ->columns(2)
                    ->schema([
                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Select::make('collections')
                            ->relationship('collections', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Toggle::make('is_visible')
                            ->label('Visible on storefront')
                            ->default(true),
                        Toggle::make('allow_backorder')
                            ->default(false),
                    ]),

                Section::make('Inventory & pricing')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columns(2)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('sku')
                                    ->maxLength(255)
                                    ->nullable(),
                                Toggle::make('track_inventory')
                                    ->label('Track inventory')
                                    ->default(false),
                                TextInput::make('stock_quantity')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->label('Stock quantity'),
                                TextInput::make('reorder_level')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('Reorder level')
                                    ->nullable(),
                            ]),
                        Select::make('tax_classification_id')
                            ->label('GST classification')
                            ->relationship('taxClassification', 'name', fn ($query) => $query->where('is_active', true)->orderBy('sort_order'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Uses the site default classification when empty.')
                            ->columnSpanFull(),
                        TextInput::make('price_minor')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->label('Price (Nu.)')
                            ->helperText('Price in Ngultrum. Example: 280 = Nu. 280.')
                            ->suffix('Nu.')
                            ->formatStateUsing(fn ($state) => $state !== null && $state !== '' ? ((int) $state) / 100 : null)
                            ->dehydrateStateUsing(fn ($state) => (int) round((float) $state * 100)),
                        TextInput::make('currency_code')
                            ->default('BTN')
                            ->maxLength(8),
                    ]),
            ]);
    }
}
