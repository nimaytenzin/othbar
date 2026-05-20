<?php

namespace App\Filament\Resources\TaxClassifications\Pages;

use App\Filament\Resources\TaxClassifications\Schemas\TaxClassificationForm;
use App\Filament\Resources\TaxClassifications\TaxClassificationResource;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\TaxClassification;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTaxClassification extends EditRecord
{
    protected static string $resource = TaxClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (TaxClassification $record): bool => ! $record->isSystemCode())
                ->before(function (DeleteAction $action, TaxClassification $record): void {
                    if ($record->isSystemCode()) {
                        $action->halt();
                    }

                    $inUse = Product::query()->where('tax_classification_id', $record->id)->exists()
                        || InvoiceItem::query()->where('tax_classification_id', $record->id)->exists();

                    if ($inUse) {
                        $action->halt();
                        Notification::make()
                            ->title('Cannot delete classification')
                            ->body('This classification is assigned to products or invoice line items.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var TaxClassification $record */
        $record = $this->getRecord();

        if ($record->isSystemCode()) {
            unset($data['code']);
        } else {
            $data['code'] = TaxClassificationForm::normalizeCode($data['code'] ?? '');
        }

        if ($record->code === 'EXEMPT' || ($data['code'] ?? '') === 'EXEMPT') {
            $data['input_credits_claimable'] = false;
            $data['rate_percent'] = 0;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }
}
