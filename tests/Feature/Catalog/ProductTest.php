<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_product_with_attributes_in_transaction()
    {
        $category = Category::factory()->create();

        $payload = [
            'name' => 'New Product',
            'price' => 150.00,
            'sku' => 'NP-001',
            'category_id' => $category->id,
            'is_published' => true,
            'attributes' => [
                ['name' => 'color', 'value' => 'red'],
                ['name' => 'size', 'value' => 'M'],
            ]
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Product')
            ->assertJsonPath('data.attributes.0.name', 'color')
            ->assertJsonPath('data.stock.quantity', 0);

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
        $this->assertDatabaseHas('product_attributes', ['name' => 'color', 'value' => 'red']);
        $this->assertDatabaseHas('stocks', ['quantity' => 0]);
    }

    /** @test */
    public function it_can_filter_products_by_category_and_price()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $p1 = Product::factory()->create(['category_id' => $category1->id, 'price' => 100]);
        $p2 = Product::factory()->create(['category_id' => $category1->id, 'price' => 200]);
        $p3 = Product::factory()->create(['category_id' => $category2->id, 'price' => 100]);

        // Filter by Category 1
        $response = $this->getJson("/api/products?category_id={$category1->id}");
        $response->assertJsonCount(2, 'data');

        // Filter by Price min 150
        $response = $this->getJson("/api/products?price_min=150");
        $response->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $p2->id);

        // Filter by Price max 150
        $response = $this->getJson("/api/products?price_max=150");
        $response->assertJsonCount(2, 'data'); // p1 and p3
    }
}
