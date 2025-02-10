<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryObserver
{

    public function __construct(protected ElasticsearchService $elasticsearchService)
    {
        //
    }

    public function creating(Category $category)
    {
        if (empty($category->id)) {
            $category->id = (string) Str::uuid();
        }
        if ($category->parent_id) {
            $parentCategory = Category::find($category->parent_id);
            if ($parentCategory && $parentCategory->parent_id) {
                throw new Exception('The selected parent category already has a parent.');
            }
        }
    }

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        info("Category created: " , [$category]);
        $this->elasticsearchService->indexCategory($category);
        Cache::increment('category_cache_version');
    }

    public function updating(Category $category)
    {
        if ($category->parent_id) {
            $parentCategory = Category::find($category->parent_id);
            if ($parentCategory && $parentCategory->parent_id) {
                throw new Exception('The selected parent category already has a parent.');
            }
        }
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        Cache::increment('category_cache_version');
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $params = [
            'index' => 'categories',
            'id' => $category->id
        ];
        $this->elasticsearchService->getClient()->delete($params);
        Cache::increment('category_cache_version');
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        Cache::increment('category_cache_version');
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        Cache::increment('category_cache_version');
    }
}
