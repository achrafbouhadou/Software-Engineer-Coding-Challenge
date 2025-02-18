<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{
    public function create(array $data);

    public function list(array $filters = [], array $sort = []);
}
