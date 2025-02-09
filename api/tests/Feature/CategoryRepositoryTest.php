<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Repositories\Category\CategoryRepository;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryRepository $categoryRepository;

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

        $this->categoryRepository = new CategoryRepository($cacheServiceMock);
    }

    public function test_create_category(): void
    {
        $data = ['name' => 'New Category' , 'id' => (string) Str::uuid()];
        $resource = $this->categoryRepository->create($data);

        $this->assertEquals('New Category', $resource->resource->name);
    }

    public function test_list_categories(): void
    {
        Category::create(['name' => 'Category A' , 'id' => (string) Str::uuid()]);
        Category::create(['name' => 'Category B' , 'id' => (string) Str::uuid()]);

        $resources = $this->categoryRepository->list('Category');

        $categories = $resources->collection;

        $this->assertNotEmpty($categories);
        $this->assertTrue(
            $categories->contains(fn($cat) => str_contains($cat->name, 'Category'))
        );
    }

 
}
