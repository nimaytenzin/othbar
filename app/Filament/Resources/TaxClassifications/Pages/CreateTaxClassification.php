<?php

namespace App\Filament\Resources\TaxClassifications\Pages;

use App\Filament\Resources\TaxClassifications\Schemas\TaxClassificationForm;
use App\Filament\Resources\TaxClassifications\TaxClassificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxClassification extends CreateRecord
{
    protected static string $resource = TaxClassificationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = TaxClassificationForm::normalizeCode($data['code'] ?? '');

        if (($data['code'] ?? '') === 'EXEMPT') {
            $data['input_credits_claimable'] = false;
            $data['rate_percent'] = 0;
        }

        return $data;
    }
}
