<?php

namespace App\Repositories\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\CacheService;

class ProductRepository implements ProductRepositoryInterface {

    public function __construct(protected CacheService $cacheService) {}


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
        $version = $this->cacheService->getCacheVersion('product_cache_version');
        $cacheKey = $this->generateCacheKey($filters, $sort , $version);
        return $this->cacheService->remember($cacheKey,  function () use ($filters, $sort) {
            $query = Product::query();
            // Filtering by category name
            if (!empty($filters['category'])) {
                $query->whereHas('categories', function ($q) use ($filters) {
                    $q->where('id', $filters['category']);
                });
            }
    
            if (!empty($sort['price'])) { 
                $query->orderBy('price', $sort['price']);
            }
            return ProductResource::collection($query->latest()->take(10)->get());
        });
    }

    protected function generateCacheKey(array $filters, array $sort, int $version): string
    {
        $rawData = json_encode(['filters' => $filters, 'sort' => $sort]);
        return "products:v{$version}:" . md5($rawData);
    }
}
