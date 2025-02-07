<?php

namespace App\Observers;

use App\Models\Category;
use Exception;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function creating(Category $category)
    {
        if (empty($category->id)) {
            $category->id = (string) Str::uuid();
        }
        if ($category->parent_id) {
            info($category->parent_id);
            $parentCategory = Category::find($category->parent_id);
            if ($parentCategory && $parentCategory->child()->exists()) {
                info('child exists');
                throw new Exception('The selected parent category already has a child.');
            }
        }
    }

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        //
    }

    public function updating(Category $category)
    {
        if ($category->parent_id) {
            $parentCategory = Category::find($category->parent_id);
            if ($parentCategory && $parentCategory->child()->exists() && $parentCategory->child->id !== $category->id) {
                throw new Exception('The selected parent category already has a child.');
            }
        }
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
