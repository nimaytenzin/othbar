<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Schemas\InvoiceLineForm;
use App\Filament\Resources\Invoices\Support\InvoiceLinePreview;
use App\Services\InvoiceService;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected static ?string $title = 'Issue invoice';

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('invoices.manage') ?? false;
    }

    public function addLineItemAction(): Action
    {
        return Action::make('addLineItem')
            ->label('Add line item')
            ->modalHeading('Add line item')
            ->modalSubmitActionLabel('Add')
            ->modalWidth('2xl')
            ->fillForm(fn (): array => InvoiceLineForm::defaultFormState())
            ->schema(InvoiceLineForm::fields())
            ->action(function (array $data): void {
                $lines = $this->data['lines'] ?? [];
                $lines[] = InvoiceLinePreview::normalizeFromForm($data);
                $this->data['lines'] = array_values($lines);
            });
    }

    public function editLineItemAction(): Action
    {
        return Action::make('editLineItem')
            ->modalHeading('Edit line item')
            ->modalSubmitActionLabel('Save')
            ->modalWidth('2xl')
            ->fillForm(function (array $arguments): array {
                $lines = $this->data['lines'] ?? [];
                $index = (int) ($arguments['index'] ?? 0);
                $line = $lines[$index] ?? [];

                return array_merge(InvoiceLineForm::defaultFormState(), $line);
            })
            ->schema(InvoiceLineForm::fields())
            ->action(function (array $data, array $arguments): void {
                $lines = $this->data['lines'] ?? [];
                $index = (int) ($arguments['index'] ?? 0);

                if (! isset($lines[$index])) {
                    return;
                }

                $lines[$index] = InvoiceLinePreview::normalizeFromForm($data);
                $this->data['lines'] = array_values($lines);
            });
    }

    public function removeLine(int $index): void
    {
        $lines = $this->data['lines'] ?? [];
        unset($lines[$index]);
        $this->data['lines'] = array_values($lines);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['lines'])) {
            throw ValidationException::withMessages([
                'lines' => 'Add at least one line item before issuing the invoice.',
            ]);
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $lines = collect($data['lines'] ?? [])->map(fn (array $line): array => [
            'product_id' => $line['product_id'] ?? null,
            'description' => $line['description'],
            'quantity' => (int) $line['quantity'],
            'unit_price_minor' => (int) $line['unit_price_minor'],
            'discount_minor' => (int) ($line['discount_minor'] ?? 0),
            'tax_classification_id' => $line['tax_classification_id'] ?? null,
        ])->all();

        return app(InvoiceService::class)->createManual(
            (int) $data['customer_id'],
            $lines,
            auth()->user(),
            $data['notes'] ?? null,
            $data['issue_date'] ?? null,
            $data['due_date'] ?? null,
            (int) ($data['invoice_discount_minor'] ?? 0),
        );
    }
}
