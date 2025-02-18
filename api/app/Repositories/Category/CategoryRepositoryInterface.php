<?php

namespace App\Repositories\Category;

interface CategoryRepositoryInterface
{
    public function create(array $data);

    public function list(?string $name = null);

    public function findOrCreateByName(string $name);
}
