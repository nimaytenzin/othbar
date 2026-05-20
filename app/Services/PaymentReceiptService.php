<?php

namespace App\Services;

use App\Models\CustomerPayment;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class PaymentReceiptService
{
    /**
     * @return array{
     *     payment: CustomerPayment,
     *     site: SiteSetting,
     *     logoPath: string|null,
     *     logoUrl: string|null
     * }
     */
    public function viewData(CustomerPayment $payment): array
    {
        $payment->load(['customer', 'allocations.invoice', 'bankAccount', 'createdBy']);
        $site = SiteSetting::current();

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
            'payment' => $payment,
            'site' => $site,
            'logoPath' => $logoPath,
            'logoUrl' => $logoUrl,
        ];
    }

    public static function paymentMethodLabel(string $method): string
    {
        return match ($method) {
            'bank-transfer' => 'Bank transfer',
            'cash' => 'Cash',
            default => ucfirst(str_replace('-', ' ', $method)),
        };
    }
}
