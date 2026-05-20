<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            SchemaView::make('filament.invoices.view')
                ->viewData(fn (Invoice $record): array => app(InvoicePdfService::class)->viewData($record)),
        ]);
    }
}
