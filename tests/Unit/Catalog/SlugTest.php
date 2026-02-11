<?php

namespace Tests\Unit\Catalog;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_slug_from_name()
    {
        $product = Product::create([
            'name' => 'Unique Product Name',
            'price' => 100,
            'sku' => 'SKU-001',
            'category_id' => Category::factory()->create()->id,
        ]);

        $this->assertEquals('unique-product-name', $product->slug);
    }

    /** @test */
    public function it_handles_slug_conflicts_by_appending_counter()
    {
        $category = Category::factory()->create();

        $product1 = Product::create([
            'name' => 'Conflict Name',
            'price' => 100,
            'sku' => 'SKU-001',
            'category_id' => $category->id,
        ]);

        $product2 = Product::create([
            'name' => 'Conflict Name',
            'price' => 200,
            'sku' => 'SKU-002',
            'category_id' => $category->id,
        ]);

        $product3 = Product::create([
            'name' => 'Conflict Name',
            'price' => 300,
            'sku' => 'SKU-003',
            'category_id' => $category->id,
        ]);

        $this->assertEquals('conflict-name', $product1->slug);
        $this->assertEquals('conflict-name-1', $product2->slug);
        $this->assertEquals('conflict-name-2', $product3->slug);
    }
}
