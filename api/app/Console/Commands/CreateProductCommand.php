<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;
use Illuminate\Validation\ValidationException;

class CreateProductCommand extends Command {
    protected $signature = 'product:create 
                            {name : The product name} 
                            {price : The product price} 
                            {--description= : (Optional) The product description}
                            {--categories=* : (Optional) List of category IDs}';
    protected $description = 'Create a new product via the CLI';

    protected $productService;

    public function __construct(ProductService $productService) {
        parent::__construct();
        $this->productService = $productService;
    }

    public function handle()
    {
         $data = [
             'name'        => $this->argument('name'),
             'price'       => $this->argument('price'),
             'description' => $this->option('description') ?? '',
             'categories'  => $this->option('categories') ?? [],
         ];

         try {
             $product = $this->productService->create($data);
             $this->info("Product created successfully with ID: {$product->id}");
         } catch (ValidationException $e) {
             $this->error("Validation failed: " . implode(', ', $e->errors()['name'] ?? []));
         }
    }
}
