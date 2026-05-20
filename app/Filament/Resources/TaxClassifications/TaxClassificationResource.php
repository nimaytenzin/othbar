<?php

namespace App\Filament\Resources\TaxClassifications;

use App\Filament\Resources\TaxClassifications\Pages\CreateTaxClassification;
use App\Filament\Resources\TaxClassifications\Pages\EditTaxClassification;
use App\Filament\Resources\TaxClassifications\Pages\ListTaxClassifications;
use App\Filament\Resources\TaxClassifications\Schemas\TaxClassificationForm;
use App\Filament\Resources\TaxClassifications\Tables\TaxClassificationsTable;
use App\Models\TaxClassification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class TaxClassificationResource extends Resource
{
    protected static ?string $model = TaxClassification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $navigationLabel = 'Tax classifications';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'tax-classifications';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('settings.tax') || $user->can('settings.manage'));
    }

    public static function form(Schema $schema): Schema
    {
        return TaxClassificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxClassificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxClassifications::route('/'),
            'create' => CreateTaxClassification::route('/create'),
            'edit' => EditTaxClassification::route('/{record}/edit'),
        ];
    }
}
