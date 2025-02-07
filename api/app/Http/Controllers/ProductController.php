<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\ProductRequest;
use Illuminate\Routing\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use ResponseTrait;


    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request)
    {
         $filters = [
            'category' => $request->query('category'),
         ];
         $sort = [
            'price' => $request->query('sort', 'asc')
         ];

         $products = $this->productService->list($filters, $sort);



         return $this->generateResponse(true,'', $products , 200);
    }

    public function store(ProductRequest $request)
    {
         try {
            DB::beginTransaction(); 
            $product = $this->productService->create($request->validated());
            DB::commit();
            return $this->generateResponse(true, 'Product created successfully', $product, 200);
         } catch (ValidationException $e) {
            DB::rollBack();
            return $this->generateResponse(false, $e->errors(), null, 400);
         }
    }
}
