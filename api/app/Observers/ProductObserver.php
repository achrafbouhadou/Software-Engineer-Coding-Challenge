<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductObserver
{

    public function creating(Product $product): void
    {
        if (empty($product->id)) {
            $product->id = (string) Str::uuid();
        }
    }
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        Cache::increment('product_cache_version');
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        Cache::increment('product_cache_version');
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        Cache::increment('product_cache_version');
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        Cache::increment('product_cache_version');
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        Cache::increment('product_cache_version');
    }
}
