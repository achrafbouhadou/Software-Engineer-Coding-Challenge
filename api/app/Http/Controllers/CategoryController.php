<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController
{
    use ResponseTrait;


    public function __construct(protected CategoryService $categoryService)
    {
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->generateResponse(false, $validator->errors()->first(), [], 422);
        }

        $categories = $this->categoryService->list($request->query('name'));
        return $this->generateResponse(true,'', $categories , 200);
    }
    
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return $this->generateResponse(true,'Category Created Successfully', $category , 200);
    }

    
}
