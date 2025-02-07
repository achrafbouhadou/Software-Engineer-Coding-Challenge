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
        })->get();

        return CategoryResource::collection($categories);
    }

}
