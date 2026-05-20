<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function __construct(
        private readonly InventoryMovementService $movements,
    ) {}

    /**
     * @param  list<array{product_id: int, quantity: int}>  $lineItems
     */
    public function assertLineItemsAvailable(array $lineItems): void
    {
        foreach ($lineItems as $lineItem) {
            if (! filled($lineItem['product_id'] ?? null)) {
                continue;
            }

            $product = Product::query()->find($lineItem['product_id']);

            if ($product === null) {
                throw ValidationException::withMessages([
                    'items' => 'One or more selected products are no longer available.',
                ]);
            }

            $quantity = (int) $lineItem['quantity'];

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'items' => 'Each line item must have a quantity of at least 1.',
                ]);
            }

            if (! $product->inStock($quantity)) {
                throw ValidationException::withMessages([
                    'items' => "{$product->name} only has {$product->stock_quantity} unit(s) in stock.",
                ]);
            }
        }
    }

    public function hasBeenDecremented(Order $order): bool
    {
        $metadata = $order->metadata ?? [];

        return filled($metadata['stock_decremented_at'] ?? null);
    }

    public function decrementForOrder(Order $order): void
    {
        if ($this->hasBeenDecremented($order)) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $order->loadMissing('items');
            $user = Auth::user();

            foreach ($order->items as $item) {
                if ($item->product_id === null) {
                    continue;
                }

                $product = Product::query()->lockForUpdate()->find($item->product_id);

                if ($product === null) {
                    continue;
                }

                $product->decrement('stock_quantity', $item->quantity);
                $product->refresh();

                if ($product->track_inventory) {
                    $this->movements->recordForOrderLine(
                        $product,
                        (int) $item->quantity,
                        $order,
                        InventoryMovementType::Sale,
                        $user,
                    );
                }
            }

            $metadata = $order->metadata ?? [];
            $metadata['stock_decremented_at'] = now()->toIso8601String();
            $order->metadata = $metadata;
            $order->saveQuietly();
        });
    }

    public function restockForOrder(Order $order): void
    {
        if (! $this->hasBeenDecremented($order)) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $order->loadMissing('items');
            $user = Auth::user();

            foreach ($order->items as $item) {
                if ($item->product_id === null) {
                    continue;
                }

                $product = Product::query()->lockForUpdate()->find($item->product_id);

                if ($product === null) {
                    continue;
                }

                $product->increment('stock_quantity', $item->quantity);
                $product->refresh();

                if ($product->track_inventory) {
                    $this->movements->recordForOrderLine(
                        $product,
                        (int) $item->quantity,
                        $order,
                        InventoryMovementType::Restock,
                        $user,
                    );
                }
            }

            $metadata = $order->metadata ?? [];
            unset($metadata['stock_decremented_at']);
            $order->metadata = $metadata;
            $order->saveQuietly();
        });
    }
}
