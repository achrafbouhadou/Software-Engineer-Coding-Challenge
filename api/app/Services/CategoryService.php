<?php

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CategoryService {


    public function __construct(protected CategoryRepositoryInterface $repository)
    {
    }

    public function create(array $data) : CategoryResource
    {
        return $this->repository->create($data);
    }
    public function findOrCreateByName(string $name) 
    {
        return $this->repository->findOrCreateByName($name);
    }

    public function list(string $name = null)
    {
        return $this->repository->list($name);
    }
}
