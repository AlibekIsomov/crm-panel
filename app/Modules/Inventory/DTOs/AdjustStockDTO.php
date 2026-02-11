<?php

namespace App\Modules\Inventory\DTOs;

use App\Modules\Inventory\Enums\StockMovementReason;

class AdjustStockDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantityChange,
        public readonly string $reason,
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request, int $productId): self
    {
        return new self(
            productId: $productId,
            quantityChange: $request->input('quantity_change'),
            reason: $request->input('reason'),
        );
    }
}
