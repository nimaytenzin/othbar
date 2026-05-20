<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Widgets\Inventory\InventoryMovementsTableWidget;
use App\Filament\Widgets\Inventory\InventoryStatsWidget;
use App\Filament\Widgets\Inventory\InventoryStockTableWidget;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class InventoryOverview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Inventory';

    protected static ?string $title = 'Inventory management';

    protected static ?string $slug = 'inventory';

    protected static string|UnitEnum|null $navigationGroup = 'Products & Inventory';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can('inventory.view');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Track stock levels and review inventory movements for products with tracking enabled.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('products')
                ->label('Manage products')
                ->icon(Heroicon::OutlinedCube)
                ->url(ProductResource::getUrl('index')),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            InventoryStatsWidget::class,
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        return [
            InventoryStockTableWidget::class,
            InventoryMovementsTableWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
