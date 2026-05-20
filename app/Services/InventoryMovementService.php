<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class InventoryMovementService
{
    public function record(
        Product $product,
        InventoryMovementType $type,
        int $quantityDelta,
        ?Model $reference = null,
        ?string $notes = null,
        ?User $user = null,
    ): InventoryMovement {
        $quantityAfter = max(0, (int) $product->stock_quantity);

        return InventoryMovement::query()->create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity_delta' => $quantityDelta,
            'quantity_after' => $quantityAfter,
            'reference_type' => $reference !== null ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
            'notes' => $notes,
            'user_id' => $user?->id,
        ]);
    }

    public function recordForOrderLine(
        Product $product,
        int $quantity,
        Order $order,
        InventoryMovementType $type,
        ?User $user = null,
    ): ?InventoryMovement {
        if (! $product->track_inventory) {
            return null;
        }

        $delta = $type === InventoryMovementType::Sale ? -$quantity : $quantity;

        return $this->record(
            $product,
            $type,
            $delta,
            $order,
            "{$type->value} for order {$order->number}",
            $user,
        );
    }
}
