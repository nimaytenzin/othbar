<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function resolveRecord(int|string $key): Product
    {
        /** @var Product $record */
        $record = parent::resolveRecord($key);

        return $record->load(['media', 'brand', 'categories', 'collections']);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaView::make('filament.products.view-product')
                    ->viewData(fn (): array => [
                        'product' => $this->getRecord(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('storefront')
                ->label('View on storefront')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (Product $record): string => route('product', $record->slug))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
