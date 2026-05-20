<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Filament\Resources\CustomerPayments\CustomerPaymentResource;
use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use App\Services\PaymentReceiptService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentReceiptController extends Controller
{
    public function __construct(
        private readonly PaymentReceiptService $receipts,
    ) {}

    public function show(CustomerPayment $payment): View
    {
        Gate::authorize('payments.receive');

        return view('admin.payments.receipt', [
            ...$this->receipts->viewData($payment),
            'closeUrl' => CustomerPaymentResource::getUrl('index'),
        ]);
    }
}
