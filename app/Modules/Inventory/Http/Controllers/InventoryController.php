<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\DTOs\AdjustStockDTO;
use App\Modules\Inventory\Http\Requests\AdjustStockRequest;
use App\Modules\Inventory\Http\Resources\StockMovementResource;
use App\Modules\Inventory\Models\Stock;
use App\Modules\Inventory\Services\InventoryService;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function adjust(AdjustStockRequest $request, $productId): JsonResponse
    {
        $dto = AdjustStockDTO::fromRequest($request, $productId);

        try {
            $stock = $this->inventoryService->adjustStock(
                $dto->productId,
                $dto->quantityChange,
                $dto->reason
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
