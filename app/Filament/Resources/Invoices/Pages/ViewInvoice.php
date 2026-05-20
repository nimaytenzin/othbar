<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Pages\ReceivePayment;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Services\InvoicePdfService;
use App\Services\InvoiceService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->record->load(['items', 'customer', 'order']);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            SchemaView::make('filament.invoices.view')
                ->viewData(fn (): array => app(InvoicePdfService::class)->viewData($this->getRecord())),
        ]);
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('primary')
                ->url(fn () => route('filament.admin.invoices.pdf', $record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $record->status !== InvoiceStatus::Void),
            Action::make('print')
                ->label('Print')
                ->icon(Heroicon::OutlinedPrinter)
                ->url(fn () => route('filament.admin.invoices.print', $record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $record->status !== InvoiceStatus::Void),
            Action::make('receivePayment')
                ->label('Record payment')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->color('success')
                ->url(fn () => ReceivePayment::getUrl().'?'.http_build_query([
                    'customer_id' => $record->customer_id,
                    'invoice_id' => $record->id,
                ]))
                ->visible(fn (): bool => $record->balanceDueMinor() > 0
                    && $record->status !== InvoiceStatus::Void
                    && (Auth::user()?->can('payments.receive') ?? false)),
            Action::make('void')
                ->label('Void invoice')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription(fn (): string => 'This permanently voids the invoice. It cannot be undone. '
                    .'If the customer has already paid, use a credit note instead (coming soon).')
                ->visible(fn (): bool => $record->canVoid()
                    && (Auth::user()?->can('invoices.manage') ?? false))
                ->action(function (): void {
                    app(InvoiceService::class)->void($this->getRecord());

                    Notification::make()
                        ->title('Invoice voided')
                        ->success()
                        ->send();

                    $this->record->refresh();
                    $this->record->load(['items', 'customer', 'order']);
                }),
        ];
    }
}
