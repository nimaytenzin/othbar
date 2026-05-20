<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\DocumentSequence;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    public function next(DocumentType $type, ?\DateTimeInterface $date = null): string
    {
        $date ??= now();
        $year = (int) $date->format('Y');
        $settings = SiteSetting::current();
        $prefix = $this->prefixFor($type, $settings);

        return DB::transaction(function () use ($type, $year, $prefix): string {
            $sequence = DocumentSequence::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    [
                        'document_type' => $type->value,
                        'year' => $year,
                    ],
                    ['last_sequence' => 0],
                );

            $sequence->increment('last_sequence');
            $sequence->refresh();

            return sprintf('%s-%d-%04d', $prefix, $year, $sequence->last_sequence);
        });
    }

    private function prefixFor(DocumentType $type, SiteSetting $settings): string
    {
        return match ($type) {
            DocumentType::Invoice => $settings->prefix_invoice ?? 'INV',
            DocumentType::CustomerPayment => $settings->prefix_customer_payment ?? 'RCP',
            DocumentType::Bill => $settings->prefix_bill ?? 'BILL',
            DocumentType::SupplierPayment => $settings->prefix_supplier_payment ?? 'SPR',
            DocumentType::Quotation => $settings->prefix_quotation ?? 'QT',
            DocumentType::Contract => $settings->prefix_contract ?? 'CTR',
            DocumentType::CreditNote => $settings->prefix_credit_note ?? 'CN',
            DocumentType::DebitNote => $settings->prefix_debit_note ?? 'DN',
        };
    }
}
