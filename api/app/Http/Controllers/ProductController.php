<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\ProductRequest;
use Illuminate\Routing\Controller;
use App\Traits\ResponseTrait;

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
         $this->productService->create($request->validated());
    }
}
