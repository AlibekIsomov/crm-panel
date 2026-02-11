<?php

namespace Tests\Unit\Inventory;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Category;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Inventory\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InventoryService();
    }

    /** @test */
    public function it_increases_stock_on_receipt()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Ensure stock is 0
        $this->assertEquals(0, $product->stock->quantity ?? 0);

        $stock = $this->service->adjustStock($product->id, 10, 'receipt');

        $this->assertEquals(10, $stock->quantity);
        $this->assertDatabaseHas('stock_movements', [
            'stock_id' => $stock->id,
            'quantity_change' => 10,
            'reason' => 'receipt',
        ]);
    }

    /** @test */
    public function it_decreases_stock_on_sale()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Initial stock 20
        $product->stock()->create(['quantity' => 20]);

        $stock = $this->service->adjustStock($product->id, -5, 'sale');

        $this->assertEquals(15, $stock->quantity);
        $this->assertDatabaseHas('stock_movements', [
            'stock_id' => $stock->id,
            'quantity_change' => -5,
            'reason' => 'sale',
        ]);
    }

    /** @test */
    public function it_prevents_negative_stock()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Initial stock 5
        $product->stock()->create(['quantity' => 5]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('На складе 5 ед., списание 10 невозможно');

        $this->service->adjustStock($product->id, -10, 'sale');
    }
}
