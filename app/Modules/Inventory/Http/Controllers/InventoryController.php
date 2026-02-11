<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Http\Requests\AdjustStockRequest;
use App\Modules\Inventory\Http\Resources\StockMovementResource;
use App\Modules\Inventory\Models\Stock;
use App\Modules\Inventory\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Modules\Catalog\Models\Product; // Need to find Product to find Stock? Or Stock by Product ID
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Modules\Inventory\Services\InventoryService;

class InventoryController extends Controller
{

    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Adjust stock for a product.
     */
    public function adjust(AdjustStockRequest $request, $productId): JsonResponse
    {
        try {
            $stock = $this->inventoryService->adjustStock(
                $productId,
                $request->input('quantity_change'),
                $request->input('reason')
            );

            return response()->json([
                'message' => 'Stock adjusted successfully',
                'current_quantity' => $stock->quantity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Недостаточно товара',
                'errors' => [
                    'quantity_change' => [$e->getMessage()]
                ]
            ], 422);
        }
    }


    public function history($productId)
    {
        $stock = Stock::where('product_id', $productId)->firstOrFail();

        $movements = $stock->movements()
            ->latest()
            ->paginate(20);

        return StockMovementResource::collection($movements);
    }
}
