<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $cacheServiceMock = $this->createMock(CacheService::class);

        $cacheServiceMock->method('getCacheVersion')
            ->willReturn(1);

        $cacheServiceMock->method('remember')
            ->willReturnCallback(function ($key, $closure) {
                return $closure();
            });

        $this->productRepository = new ProductRepository($cacheServiceMock);
    }

    public function test_create_product_without_categories(): void
    {
        $data = [
            'id' => (string) Str::uuid(),
            'name' => 'Test Product',
            'description' => 'Description of test product ',
            'price' => 100.00,
            'image' => 'https://banner2.cleanpng.com/20180417/xve/avfo64zl4.webp',
        ];

        $resource = $this->productRepository->create($data);

        $product = $resource->resource;

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
    }

    public function test_create_product_with_categories(): void
    {
        // Create a category to be attached.
        $category = Category::create(['name' => 'Test Category', 'id' => (string) Str::uuid()]);

        $data = [
            'id' => (string) Str::uuid(),
            'name' => 'Test Product 2',
            'description' => 'Another test product',
            'price' => 200.00,
            'image' => 'https://banner2.cleanpng.com/20180417/xve/avfo64zl4.webp',
            'categories' => [$category->id],
        ];

        $resource = $this->productRepository->create($data);
        $product = $resource->resource;

        $this->assertCount(1, $product->categories);
        $this->assertEquals($category->id, $product->categories->first()->id);
    }

    public function test_list_products_filters_by_category(): void
    {
        $category = Category::create(['name' => 'Electronics', 'id' => (string) Str::uuid()]);
        $product = Product::create([
            'id' => (string) Str::uuid(),
            'name' => 'Smartphone',
            'description' => 'Latest smartphone',
            'price' => 500.00,
            'image' => 'https://banner2.cleanpng.com/20180417/xve/avfo64zl4.webp',
        ]);
        $product->categories()->attach($category->id);

        $results = $this->productRepository->list(
            ['category' => $category->id],
            ['price' => 'asc']
        );

        $products = $results->collection;

        $this->assertNotEmpty($products);
        $this->assertEquals('Smartphone', $products->first()->name);
    }
}
