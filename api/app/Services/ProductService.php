<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProductService {


    public function __construct(protected ProductRepositoryInterface $repository)
    {
    }

    public function create(array $data) : ProductResource
    {

        return $this->repository->create($this->prepareData($data));
    }

    public function list(array $filters = [], array $sort = [])
    {
        return $this->repository->list($filters, $sort);
    }

    protected function prepareData(array $data) : array
    {
        if (!empty($data['image_file'])) {
            $imagePath = $data['image_file']->store('products', 'public');
            $data['image'] = Storage::url($imagePath);
        } elseif (!empty($data['image'])) {
            $data['image'] = $data['image'];
        }

        return $data;
    }
}
