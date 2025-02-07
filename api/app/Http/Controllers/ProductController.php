<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\ProductRequest;
use Illuminate\Routing\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductController extends Controller
{
    use ResponseTrait;


    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request)
    {
      try {
         $validator = Validator::make($request->query(), [
            'filterCategory' => 'nullable|string|exists:categories,id',
            'sort' => 'nullable|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
               return $this->generateResponse(false, $validator->errors()->first(), [], 422);
         }

         $filters = [
            'category' => $request->query('filterCategory'),
         ];
         $sort = [
            'price' => $request->query('sortBy', 'asc')
         ];

         $products = $this->productService->list($filters, $sort);

         return $this->generateResponse(true,'', $products , 200);
      } catch (Throwable $e) {
         return $this->generateResponse(false, $e->getMessage(), null, 400);
      }
    }

    public function store(ProductRequest $request)
    {
         try {
            DB::beginTransaction(); 
            $product = $this->productService->create($request->validated());
            DB::commit();
            return $this->generateResponse(true, 'Product created successfully', $product, 200);
         } catch (Throwable $e) {
            DB::rollBack();
            return $this->generateResponse(false, $e->getMessage(), null, 400);
         }
    }
}
