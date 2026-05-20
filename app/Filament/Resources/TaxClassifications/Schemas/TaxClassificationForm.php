<?php

namespace App\Filament\Resources\TaxClassifications\Schemas;

use App\Models\TaxClassification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TaxClassificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->maxLength(32)
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (?TaxClassification $record): bool => $record?->isSystemCode() ?? false)
                    ->dehydrated()
                    ->helperText(fn (?TaxClassification $record): ?string => $record?->isSystemCode()
                        ? 'System classification codes cannot be changed.'
                        : 'Uppercase letters, numbers, and underscores.'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(150),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('rate_percent')
                    ->label('GST rate')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->suffix('%'),
                Toggle::make('input_credits_claimable')
                    ->label('Input tax credits claimable')
                    ->default(true),
                Toggle::make('is_active')
                    ->default(true),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ]),
        ]);
    }

    public static function normalizeCode(?string $code): string
    {
        return Str::upper(Str::slug((string) $code, '_'));
    }
}
