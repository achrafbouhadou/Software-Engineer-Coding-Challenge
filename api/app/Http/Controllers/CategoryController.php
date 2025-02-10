<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use App\Services\ElasticsearchService;
use App\Traits\Loggable;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CategoryController
{
    use ResponseTrait;
    use Loggable;


    public function __construct(protected CategoryService $categoryService , protected ElasticsearchService  $elasticsearchService)
    {
    }

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->query(), [
                'name' => 'sometimes|required|string|max:255',
            ]);

            if ($validator->fails()) {
                $this->logError($validator->errors()->first());
                return $this->generateResponse(false, $validator->errors()->first(), [], 422);
            }

            $categories = $this->categoryService->list($request->query('name'));
            return $this->generateResponse(true,'', $categories , 200);
        } catch (Throwable $e) {
            $this->logError($e->getMessage());
            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }
    
    public function store(CategoryRequest $request)
    {
        try {
            $category = $this->categoryService->create($request->validated());
            $this->logInfo('Category created successfully', ['categoryId' => $category->id]);
            return $this->generateResponse(true,'Category Created Successfully', $category , 200);
        } catch (Throwable $e) {
            $this->logError($e->getMessage());
            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }

    public function search(Request $request)
    {
        try{
            $query = $request->input('q');
            $results = $this->elasticsearchService->searchCategories($query);
            $data = [
               'hits' => $results['hits']['hits'],
               'suggestions' => $results['suggest']['name_suggestion'][0]['options']
            ];
            return $this->generateResponse(true, '', $data, 200);
        }catch(Throwable $e){
            $this->logError($e->getMessage());
            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
        
    }

    public function autocomplete(Request $request)
    {
      try {
            $query = $request->input('q');
            $results = $this->elasticsearchService->searchCategories($query, 5);
            return $this->generateResponse(true, '', $results['suggest']['name_suggestion'][0]['options'], 200);
        } catch (Throwable $e) {
            $this->logError($e->getMessage());
            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }

    
}
