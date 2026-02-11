<?php

namespace Tests\Feature\Inventory;

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_adjust_stock_and_create_movement()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        // Initial stock 0 created by factory or manually? 
        // Factory logic: Product model doesn't auto-create stock in factory unless defined.
        // Controller `adjust` creates if missing.

        $payload = [
            'quantity_change' => 50,
            'reason' => 'receipt'
        ];

        $response = $this->postJson("/api/inventory/{$product->id}/adjust", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('current_quantity', 50);

        $this->assertDatabaseHas('stocks', ['product_id' => $product->id, 'quantity' => 50]);
        $this->assertDatabaseHas('stock_movements', ['quantity_change' => 50, 'reason' => 'receipt']);
    }

    /** @test */
    public function it_fails_when_stock_becomes_negative()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Setup initial stock
        $product->stock()->create(['quantity' => 10]);

        $payload = [
            'quantity_change' => -20,
            'reason' => 'sale'
        ];

        $response = $this->postJson("/api/inventory/{$product->id}/adjust", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity_change']);
    }

    /** @test */
    public function it_shows_stock_history()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $stock = $product->stock()->create(['quantity' => 10]);

        $stock->movements()->create(['quantity_change' => 10, 'reason' => 'receipt']);
        $stock->movements()->create(['quantity_change' => -5, 'reason' => 'sale']);

        $response = $this->getJson("/api/inventory/{$product->id}/history");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
