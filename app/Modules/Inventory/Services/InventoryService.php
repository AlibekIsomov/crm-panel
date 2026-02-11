<?php

namespace App\Modules\Inventory\Services;

use App\Modules\Catalog\Models\Product;
use App\Modules\Inventory\Models\Stock;
use App\Modules\Inventory\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Adjust stock for a product.
     *
     * @param int $productId
     * @param int $quantityChange
     * @param string $reason
     * @return Stock
     * @throws Exception
     */
    public function adjustStock(int $productId, int $quantityChange, string $reason): Stock
    {
        return DB::transaction(function () use ($productId, $quantityChange, $reason) {
            $stock = Stock::where('product_id', $productId)->lockForUpdate()->first();

            if (!$stock) {
                Product::findOrFail($productId);
                $stock = Stock::create(['product_id' => $productId, 'quantity' => 0]);
            }

            $newQuantity = $stock->quantity + $quantityChange;

            if ($newQuantity < 0) {
                throw new Exception("На складе {$stock->quantity} ед., списание " . abs($quantityChange) . " невозможно");
            }

            $stock->quantity = $newQuantity;
            $stock->save();

            $stock->movements()->create([
                'quantity_change' => $quantityChange,
                'reason' => $reason,
            ]);

            return $stock;
        });
    }
}
