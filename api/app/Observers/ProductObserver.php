<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\CacheService;
use App\Services\ElasticsearchService;
use Illuminate\Support\Str;

class ProductObserver
{
    public function __construct(protected ElasticsearchService $elasticsearchService, protected CacheService $cacheService)
    {
        //
    }

    public function creating(Product $product): void
    {
        if (empty($product->id)) {
            $product->id = (string) Str::uuid();
        }
        $this->cacheService->incrementCacheVersion('product_cache_version');

    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {

        $this->elasticsearchService->indexProduct($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->cacheService->incrementCacheVersion('product_cache_version');
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $params = [
            'index' => 'products',
            'id' => $product->id,
        ];
        $this->elasticsearchService->getClient()->delete($params);
        $this->cacheService->incrementCacheVersion('product_cache_version');
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->cacheService->incrementCacheVersion('product_cache_version');
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        $this->cacheService->incrementCacheVersion('product_cache_version');
    }
}
