<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LargeDataSeeder extends Seeder
{
    public function run()
    {
        DB::connection()->disableQueryLog();

        $now = Carbon::now();

    
        $this->command->info('Seeding 1K Categories...');
        $categoriesData = [];
        $generatedCategoryIds = [];

        for ($i = 1; $i <= 1000; $i++) {
            $uuid = (string) Str::uuid();
            $generatedCategoryIds[] = $uuid;
            $categoriesData[] = [
                'id'         => $uuid,
                'name'       => "Category $i",
                'parent_id'  => null, 
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('categories')->insert($categoriesData);

       
        $this->command->info('Assigning children to categories ...');

        $categories = DB::table('categories')->select('id', 'parent_id')->get();
        $rootCategoryIds = $categories->pluck('id')->toArray();

        foreach ($categories as $category) {
            if (!in_array($category->id, $rootCategoryIds)) {
                continue;
            }
            $numChildren = rand(0, 4);

            $availableChildren = array_filter($rootCategoryIds, function ($id) use ($category) {
                return $id !== $category->id;
            });
            $availableChildren = array_values($availableChildren);
            if (empty($availableChildren)) {
                continue;
            }
            $numChildren = min($numChildren, count($availableChildren));
            if ($numChildren > 0) {
                $selectedKeys = array_rand($availableChildren, $numChildren);
                if (!is_array($selectedKeys)) {
                    $selectedKeys = [$selectedKeys];
                }
                $childrenIds = [];
                foreach ($selectedKeys as $key) {
                    $childrenIds[] = $availableChildren[$key];
                }
                foreach ($childrenIds as $childId) {
                    DB::table('categories')
                        ->where('id', $childId)
                        ->update(['parent_id' => $category->id]);
                    if (($key = array_search($childId, $rootCategoryIds)) !== false) {
                        unset($rootCategoryIds[$key]);
                    }
                }
            }
        }

        $this->command->info('Seeding 1M Products...');
        $totalProducts = 1_000_000;
        $chunkSize     = 10_000;
        $iterations    = (int) ceil($totalProducts / $chunkSize);

        $allCategoryIds = DB::table('categories')->pluck('id')->toArray();

        for ($i = 0; $i < $iterations; $i++) {
            $this->command->info("Inserting products chunk " . ($i + 1) . " of {$iterations}");

            $currentChunkSize = ($i === $iterations - 1)
                ? $totalProducts - ($chunkSize * $i)
                : $chunkSize;

            $productsData = [];
            $currentProductIds = [];

            for ($j = 0; $j < $currentChunkSize; $j++) {
                $productUuid = (string) Str::uuid();
                $currentProductIds[] = $productUuid;
                $productsData[] = [
                    'id'          => $productUuid,
                    'name'        => "Product " . $productUuid,
                    'price'       => rand(100, 1000),
                    'description' => 'Description for product ' . $productUuid,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            DB::table('products')->insert($productsData);

            $pivotData = [];
            foreach ($currentProductIds as $productId) {
                $numCategories = rand(1, 3);
                if ($numCategories === 1) {
                    $selectedCategoryIds = [ $allCategoryIds[array_rand($allCategoryIds)] ];
                } else {
                    $selectedKeys = array_rand($allCategoryIds, $numCategories);
                    if (!is_array($selectedKeys)) {
                        $selectedKeys = [$selectedKeys];
                    }
                    $selectedCategoryIds = [];
                    foreach ($selectedKeys as $key) {
                        $selectedCategoryIds[] = $allCategoryIds[$key];
                    }
                }
                foreach ($selectedCategoryIds as $catId) {
                    $pivotData[] = [
                        'product_id'  => $productId,
                        'category_id' => $catId,
                    ];
                }
            }

            foreach (array_chunk($pivotData, 1000) as $pivotChunk) {
                DB::table('category_product')->insert($pivotChunk);
            }
        }
    }
}
