<?php

namespace App\Repositories\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface {
    public function create(array $data) : ProductResource
    {
        $product = Product::create($data);

        return new ProductResource($product);
    }

    public function list(array $filters = [], array $sort = []) 
    {
        $query = Product::query();

        // Filtering by category name
        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('name', $filters['category']);
            });
        }

        if (!empty($sort['price'])) {
            $query->orderBy('price', $sort['price']);
        }
        return ProductResource::collection($query->get());
    }
}
