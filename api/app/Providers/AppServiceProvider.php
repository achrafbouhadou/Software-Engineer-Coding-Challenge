<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
<<<<<<< HEAD
=======
<<<<<<< Updated upstream
        //
=======
        
>>>>>>> c2c58b7 (Feat : seed a large data)
        $this->app->bind(
            \App\Repositories\Product\ProductRepositoryInterface::class,
            \App\Repositories\Product\ProductRepository::class
        );

        $this->app->bind(
            \App\Repositories\Category\CategoryRepositoryInterface::class,
            \App\Repositories\Category\CategoryRepository::class
        );
<<<<<<< HEAD
=======
>>>>>>> Stashed changes
>>>>>>> c2c58b7 (Feat : seed a large data)
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
        Category::observe(CategoryObserver::class);
        Product::observe(ProductObserver::class);
=======
<<<<<<< Updated upstream
        //
=======
        if (app()->runningInConsole()) {
            return;
        }
        Category::observe(CategoryObserver::class);
        Product::observe(ProductObserver::class);
>>>>>>> Stashed changes
>>>>>>> c2c58b7 (Feat : seed a large data)
    }
}
