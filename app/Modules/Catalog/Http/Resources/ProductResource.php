<?php

namespace App\Modules\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'sku' => $this->sku,
            'is_published' => $this->is_published,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'attributes' => ProductAttributeResource::collection($this->whenLoaded('attributes')),
            'stock' => $this->whenLoaded('stock', function () {
                return [
                    'quantity' => $this->stock->quantity,
                    'reserved' => $this->stock->reserved_quantity,
                ];
            }),
        ];
    }
}
