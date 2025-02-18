<?php

namespace App\Repositories\Category;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(protected CacheService $cacheService) {}

    public function create(array $data): CategoryResource
    {
        $category = Category::create($data);
        Cache::increment('category_cache_version');

        return new CategoryResource($category);
    }

    public function list(?string $name = null)
    {
        info($this->cacheService->getCacheVersion('category_cache_version'));

        $version = $this->cacheService->getCacheVersion('category_cache_version');
        $cacheKey = $this->generateCacheKey($name, $version);

        return $this->cacheService->remember($cacheKey, function () use ($name) {
            $categories = Category::with('children')->whereNull('parent_id')->when($name, function ($query) use ($name) {
                $query->where('name', 'like', "%$name%");
            })->take(5)->get();

            return CategoryResource::collection($categories);
        });

    }

    public function findOrCreateByName(string $name): CategoryResource
    {
        $category = Category::firstOrCreate(['name' => $name]);

        return new CategoryResource($category);
    }

    protected function generateCacheKey(?string $name, int $version): string
    {
        $prefix = "categories:v{$version}:";
        info($prefix.$name);

        return $prefix.$name;
    }
}
