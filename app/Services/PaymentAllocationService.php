<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\PaymentAllocation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentAllocationService
{
    public function __construct(
        private readonly DocumentNumberService $documentNumbers,
    ) {}

    /**
     * @param  list<array{invoice_id: int, amount_minor: int}>  $allocations
     */
    public function receivePayment(
        int $customerId,
        int $amountMinor,
        string $paymentMethod,
        array $allocations,
        ?User $user = null,
        ?string $paymentDate = null,
        ?int $bankAccountId = null,
        ?string $reference = null,
        ?string $notes = null,
    ): CustomerPayment {
        $totalAllocated = array_sum(array_column($allocations, 'amount_minor'));

        if ($totalAllocated > $amountMinor) {
            throw ValidationException::withMessages([
                'allocations' => 'Allocated amounts cannot exceed the payment amount.',
            ]);
        }

        return DB::transaction(function () use (
            $customerId,
            $amountMinor,
            $paymentMethod,
            $allocations,
            $user,
            $paymentDate,
            $bankAccountId,
            $reference,
            $notes,
        ): CustomerPayment {
            $payment = CustomerPayment::query()->create([
                'number' => $this->documentNumbers->next(DocumentType::CustomerPayment),
                'customer_id' => $customerId,
                'payment_date' => $paymentDate ?? now()->toDateString(),
                'amount_minor' => $amountMinor,
                'currency_code' => 'BTN',
                'payment_method' => $paymentMethod,
                'bank_account_id' => $bankAccountId,
                'reference' => $reference,
                'notes' => $notes,
                'created_by_user_id' => $user?->id,
            ]);

            foreach ($allocations as $row) {
                $this->allocate($payment, (int) $row['invoice_id'], (int) $row['amount_minor']);
            }

            return $payment->fresh(['allocations.invoice', 'customer']);
        });
    }

    /**
     * Apply payment to outstanding invoices, oldest due date first.
     */
    public function receivePaymentAutoAllocate(
        int $customerId,
        int $amountMinor,
        string $paymentMethod,
        ?User $user = null,
        ?string $paymentDate = null,
        ?int $bankAccountId = null,
        ?string $reference = null,
        ?string $notes = null,
    ): CustomerPayment {
        if ($amountMinor < 1) {
            throw ValidationException::withMessages([
                'amount_minor' => 'Enter a payment amount greater than zero.',
            ]);
        }

        $allocations = $this->buildFifoAllocations($customerId, $amountMinor);

        if ($allocations === []) {
            throw ValidationException::withMessages([
                'customer_id' => 'This customer has no outstanding invoices to pay.',
            ]);
        }

        return $this->receivePayment(
            $customerId,
            $amountMinor,
            $paymentMethod,
            $allocations,
            $user,
            $paymentDate,
            $bankAccountId,
            $reference,
            $notes,
        );
    }

    /**
     * @return list<array{invoice_id: int, amount_minor: int}>
     */
    public function buildFifoAllocations(int $customerId, int $amountMinor): array
    {
        $invoices = Invoice::query()
            ->where('customer_id', $customerId)
            ->where('status', '!=', InvoiceStatus::Void)
            ->whereColumn('amount_paid_minor', '<', 'total_minor')
            ->orderBy('due_date')
            ->orderBy('issue_date')
            ->get();

        $remaining = $amountMinor;
        $allocations = [];

        foreach ($invoices as $invoice) {
            if ($remaining <= 0) {
                break;
            }

            $balance = $invoice->balanceDueMinor();
            if ($balance <= 0) {
                continue;
            }

            $apply = min($remaining, $balance);
            $allocations[] = [
                'invoice_id' => $invoice->id,
                'amount_minor' => $apply,
            ];
            $remaining -= $apply;
        }

        return $allocations;
    }

    public function customerOutstandingMinor(int $customerId): int
    {
        return (int) Invoice::query()
            ->where('customer_id', $customerId)
            ->where('status', '!=', InvoiceStatus::Void)
            ->whereColumn('amount_paid_minor', '<', 'total_minor')
            ->get()
            ->sum(fn (Invoice $invoice): int => $invoice->balanceDueMinor());
    }

    public function allocate(CustomerPayment $payment, int $invoiceId, int $amountMinor): PaymentAllocation
    {
        if ($amountMinor < 1) {
            throw ValidationException::withMessages([
                'amount_minor' => 'Allocation amount must be at least 1.',
            ]);
        }

        $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoiceId);

        if ($invoice->customer_id !== $payment->customer_id) {
            throw ValidationException::withMessages([
                'invoice_id' => 'Invoice does not belong to this customer.',
            ]);
        }

        $balance = $invoice->balanceDueMinor();

        if ($amountMinor > $balance) {
            throw ValidationException::withMessages([
                'amount_minor' => 'Allocation exceeds invoice balance due (Nu. '.number_format($balance / 100, 2).').',
            ]);
        }

        $alreadyAllocated = $payment->allocatedMinor();

        if ($alreadyAllocated + $amountMinor > (int) $payment->amount_minor) {
            throw ValidationException::withMessages([
                'amount_minor' => 'Allocation exceeds unallocated payment amount.',
            ]);
        }

        $allocation = PaymentAllocation::query()->create([
            'customer_payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'amount_minor' => $amountMinor,
        ]);

        $invoice->amount_paid_minor = (int) $invoice->amount_paid_minor + $amountMinor;
        $invoice->recalculatePaidStatus();
        $invoice->save();

        $this->syncOrderPaymentStatus($invoice);

        return $allocation;
    }

    private function syncOrderPaymentStatus(Invoice $invoice): void
    {
        $invoice->loadMissing('order');

        if ($invoice->order === null) {
            return;
        }

        $order = $invoice->order;

        if ($invoice->balanceDueMinor() <= 0) {
            $order->update(['payment_status' => PaymentStatus::Paid]);
        } elseif ((int) $invoice->amount_paid_minor > 0) {
            $order->update(['payment_status' => PaymentStatus::Pending]);
        }
    }
}
