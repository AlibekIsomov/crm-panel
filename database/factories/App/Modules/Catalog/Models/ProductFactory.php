<?php

namespace Database\Factories\App\Modules\Catalog\Models;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sku' => $this->faker->unique()->ean13(),
            'is_published' => $this->faker->boolean(80),
            'category_id' => Category::factory(),
        ];
    }
}
