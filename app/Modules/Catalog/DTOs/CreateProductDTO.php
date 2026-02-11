<?php

namespace App\Modules\Catalog\DTOs;

class CreateProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly string $sku,
        public readonly int $categoryId,
        public readonly ?string $description = null,
        public readonly ?string $slug = null,
        public readonly bool $isPublished = false,
        public readonly array $attributes = [],
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            name: $request->input('name'),
            price: $request->input('price'),
            sku: $request->input('sku'),
            categoryId: $request->input('category_id'),
            description: $request->input('description'),
            slug: $request->input('slug'),
            isPublished: $request->boolean('is_published', false),
            attributes: $request->input('attributes', []),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'sku' => $this->sku,
            'category_id' => $this->categoryId,
            'description' => $this->description,
            'slug' => $this->slug,
            'is_published' => $this->isPublished,
        ];
    }
}
