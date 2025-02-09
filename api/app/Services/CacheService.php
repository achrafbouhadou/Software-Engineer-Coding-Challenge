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
    
}