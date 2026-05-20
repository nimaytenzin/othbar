<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvoicePrintController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly InvoicePdfService $invoicePdf,
    ) {}

    public function show(Invoice $invoice): View
    {
        Gate::authorize('invoices.view');

        if ($invoice->status === InvoiceStatus::Void) {
            abort(404);
        }

        return view('admin.invoices.print', [
            ...$this->invoicePdf->viewData($invoice),
            'closeUrl' => InvoiceResource::getUrl('view', ['record' => $invoice]),
        ]);
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        Gate::authorize('invoices.view');

        if ($invoice->status === InvoiceStatus::Void) {
            abort(404);
        }

        return $this->invoicePdf->download($invoice);
    }
}
