<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    const TTL = 7 * 24 * 60 * 60;  // TODO : make it configurable

    public function remember(string $key, callable $callback)
    {
        return Cache::remember($key, self::TTL, $callback);
    }

    public function forget(string $key)
    {
        return Cache::forget($key);
    }

    public function getCacheVersion($key)
    {
        return Cache::get($key , 1);
    }

    public function incrementCacheVersion($key)
    {
        if (! Cache::has($key)) {
            info('Key doesn\'t exist, set initial value to 1');
            // Key doesn't exist, set initial value to 1
            Cache::put($key, 1, now()->addDays(7));
            Cache::increment($key);
        } else {
            info('Key exists, increment by 1');
            // Key exists, increment by 1
            Cache::increment($key);
        }
    }
    
}