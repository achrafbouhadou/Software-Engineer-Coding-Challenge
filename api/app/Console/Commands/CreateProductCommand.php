<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Validation\ValidationException;

class CreateProductCommand extends Command
{
    protected $signature = 'product:create 
                            {name : The product name} 
                            {price : The product price} 
                            {--description= : (Optional) The product description}
                            {--image= : (Optional) The product image URL}
                            {--categories=* : (Optional) List of category names}';

    protected $description = 'Create a new product via the CLI';


    public function __construct(protected ProductService $productService,protected CategoryService $categoryService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $data = [
            'name'        => $this->argument('name'),
            'price'       => $this->argument('price'),
            'description' => $this->option('description') ?? '',
            'image'       => $this->option('image') ?? '',
        ];

        $categoryNames = $this->option('categories') ?? [];
        $categoryIds = [];
        foreach ($categoryNames as $categoryName) {
            $categoryName = trim($categoryName);

            $category = $this->categoryService->findOrCreateByName($categoryName);
            $categoryIds[] = $category->id;
        }
        $data['categories'] = $categoryIds;

        try {
            $product = $this->productService->create($data);
            $this->info("Product created successfully with ID: {$product->id}");
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[] = "$field: " . implode(', ', $messages);
            }
            $this->error("Validation failed: " . implode(' | ', $errors));
        }
    }
}
