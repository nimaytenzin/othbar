<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class OrderReceiptController extends Controller
{
    use AuthorizesRequests;

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items', 'shippingAddress']);

        return view('admin.orders.receipt', [
            'order' => $order,
        ]);
    }
}
