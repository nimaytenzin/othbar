<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderPaymentProofController extends Controller
{
    use AuthorizesRequests;

    public function download(Order $order): StreamedResponse
    {
        $this->authorize('view', $order);

        if (! $order->payment_proof_path || ! Storage::disk('public')->exists($order->payment_proof_path)) {
            abort(404, 'Payment proof file not found.');
        }

        return Storage::disk('public')->download(
            $order->payment_proof_path,
            basename($order->payment_proof_path),
        );
    }
}
