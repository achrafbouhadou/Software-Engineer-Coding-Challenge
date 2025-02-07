<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CategoryController
{
    use ResponseTrait;


    public function __construct(protected CategoryService $categoryService)
    {
    }

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->query(), [
                'name' => 'sometimes|required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->generateResponse(false, $validator->errors()->first(), [], 422);
            }

            $categories = $this->categoryService->list($request->query('name'));
            return $this->generateResponse(true,'', $categories , 200);
        } catch (Throwable $e) {
            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }
    
    public function store(CategoryRequest $request)
    {
        try {
            $category = $this->categoryService->create($request->validated());
            return $this->generateResponse(true,'Category Created Successfully', $category , 200);
        } catch (Throwable $e) {
            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }

    
}
