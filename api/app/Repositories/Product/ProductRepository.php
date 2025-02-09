<?php

namespace App\Repositories\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface {
    public function create(array $data) : ProductResource
    {
        $product = Product::create($data);

        if (isset($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        return new ProductResource($product);
    }

    public function list(array $filters = [], array $sort = []) 
    {
        $query = Product::query();
        // Filtering by category name
        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('id', $filters['category']);
            });
        }

<<<<<<< HEAD
        if (!empty($sort['price'])) {
            $query->orderBy('price', $sort['price']);
        }
        return ProductResource::collection($query->get());
=======
        if (!empty($sort['price'])) { 
            $query->orderBy('price', $sort['price']);
        }
        return ProductResource::collection($query->take(10)->get());
>>>>>>> c2c58b7 (Feat : seed a large data)
    }
}
