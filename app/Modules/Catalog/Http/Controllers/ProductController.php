<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Http\Requests\StoreProductRequest;
use App\Modules\Catalog\Http\Resources\ProductResource;
use App\Modules\Catalog\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::with(['category', 'stock'])
            ->when($request->category_id, fn($q) => $q->inCategory($request->category_id))
            ->when($request->price_min || $request->price_max, fn($q) => $q->priceRange($request->price_min, $request->price_max))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate(15);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $productData = $request->safe()->except(['attributes']);
            $product = Product::create($productData);

            if ($request->has('attributes')) {
                $product->attributes()->createMany($request->validated('attributes'));
            }

            // Also initialize stock if needed? Requirements imply stock is separate but maybe init at 0?
            // Stock table has product_id unique. Without a record, it's effectively 0 or null.
            // Let's create a default stock record of 0.
            $product->stock()->create(['quantity' => 0, 'reserved_quantity' => 0]);

            return response()->json([
                'data' => new ProductResource($product->load('attributes', 'stock')),
                'message' => 'Product created successfully',
            ], 201);
        });
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['category', 'attributes', 'stock'])
            ->firstOrFail();

        return new ProductResource($product);
    }


    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return new ProductResource($product);
    }
}
