<?php

namespace App\Repositories\Category;


use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Http\Resources\CategoryResource;

class CategoryRepository implements CategoryRepositoryInterface {

    public function create(array $data) : CategoryResource
    {
        $category = Category::create($data);

        return new CategoryResource($category);
    }

    public function list(string $name = null)
    {
        $categories = Category::with('children')->whereNull('parent_id')->when($name, function ($query) use ($name) {
            $query->where('name', 'like', "%$name%");
<<<<<<< HEAD
        })->get();
=======
        })->take(5)->get();
>>>>>>> c2c58b7 (Feat : seed a large data)

        return CategoryResource::collection($categories);
    }

    public function findOrCreateByName(string $name) : CategoryResource
    {
        $category = Category::firstOrCreate(['name' => $name]);
        return new CategoryResource($category);
    }

}
