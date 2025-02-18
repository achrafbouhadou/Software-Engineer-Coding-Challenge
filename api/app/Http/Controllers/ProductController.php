<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ElasticsearchService;
use App\Services\ProductService;
use App\Traits\Loggable;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProductController extends Controller
{
    use Loggable;
    use ResponseTrait;

    public function __construct(protected ProductService $productService, protected ElasticsearchService $elasticsearchService) {}

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->query(), [
                'filterCategory' => 'nullable|string|exists:categories,id',
                'sort' => 'nullable|string|in:asc,desc',
            ]);

            if ($validator->fails()) {
                $this->logError($validator->errors()->first());

                return $this->generateResponse(false, $validator->errors()->first(), [], 422);
            }

            $filters = [
                'category' => $request->query('filterCategory'),
            ];
            $sort = [
                'price' => $request->query('sortBy', 'asc'),
            ];

            $products = $this->productService->list($filters, $sort);

            return $this->generateResponse(true, '', $products, 200);
        } catch (Throwable $e) {
            $this->logError($e->getMessage());

            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = $this->productService->create($request->validated());
            DB::commit();
            $this->logInfo('Product created successfully', ['productId' => $product->id]);

            return $this->generateResponse(true, 'Product created successfully', $product, 200);
        } catch (Throwable $e) {
            $this->logError($e->getMessage());
            DB::rollBack();

            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('q');
            $validator = Validator::make($request->query(), [
                'q' => 'required|string',
            ]);
            if ($validator->fails()) {
                $this->logError($validator->errors()->first());

                return $this->generateResponse(false, $validator->errors()->first(), [], 422);
            }
            info('Searching products for: '.$query);
            $results = $this->elasticsearchService->searchProducts($query);
            $data = [
                'hits' => $results['hits']['hits'],
                'suggestions' => $results['suggest']['name_suggestion'][0]['options'],
            ];

            return $this->generateResponse(true, '', $data, 200);
        } catch (Throwable $e) {
            $this->logError($e->getMessage());

            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }

    }

    public function autocomplete(Request $request)
    {
        try {
            $query = $request->input('q');
            $results = $this->elasticsearchService->searchProducts($query, 5);

            return $this->generateResponse(true, '', $results['suggest']['name_suggestion'][0]['options'], 200);
        } catch (Throwable $e) {
            $this->logError($e->getMessage());

            return $this->generateResponse(false, $e->getMessage(), null, 400);
        }
    }
}
