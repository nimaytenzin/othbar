<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Support\PaymentChannels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * @return array{
     *     invoice: Invoice,
     *     site: SiteSetting,
     *     bankAccount: BankAccount|null,
     *     logoPath: string|null,
     *     logoUrl: string|null
     * }
     */
    public function viewData(Invoice $invoice): array
    {
        $invoice->load(['items.taxClassification', 'customer', 'order']);
        $site = SiteSetting::current();
        $site->loadMissing('defaultBankAccount');

        $bankAccount = PaymentChannels::defaultBankAccount();

        $logoPath = null;
        $logoUrl = null;

        if (filled($site->business_logo_path)) {
            $path = Storage::disk('public')->path($site->business_logo_path);
            if (is_file($path)) {
                $logoPath = $path;
                $logoUrl = Storage::disk('public')->url($site->business_logo_path);
            }
        }

        return [
            'invoice' => $invoice,
            'site' => $site,
            'bankAccount' => $bankAccount,
            'logoPath' => $logoPath,
            'logoUrl' => $logoUrl,
        ];
    }

    public function render(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('admin.invoices.pdf', $this->viewData($invoice));
    }

    public function download(Invoice $invoice): Response
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', $invoice->number).'.pdf';

        return $this->render($invoice)->download($filename);
    }
}
