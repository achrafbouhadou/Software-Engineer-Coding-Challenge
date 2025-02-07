<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductService {


    public function __construct(protected ProductRepositoryInterface $repository)
    {
    }

    public function create(array $data) : ProductResource
    {

        return $this->repository->create($data);
    }

    public function list(array $filters = [], array $sort = [])
    {
        return $this->repository->list($filters, $sort);
    }
}
