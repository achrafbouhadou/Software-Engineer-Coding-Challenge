<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticReindexCommand extends Command
{
    protected $signature = 'elastic:reindex';

    protected $description = 'Reindex all products and categories in Elasticsearch';

    protected ElasticsearchService $elasticService;

    public function __construct(ElasticsearchService $elasticService)
    {
        parent::__construct();
        $this->elasticService = $elasticService;
    }

    public function handle()
    {
        $this->info('Starting reindexing of Products and Categories in Elasticsearch...');

        $this->info('Bulk indexing Products...');
        Product::chunk(10000, function ($products) {
            $bulkParams = ['body' => []];

            foreach ($products as $product) {
                $bulkParams['body'][] = [
                    'index' => [
                        '_index' => 'products',
                        '_id' => $product->id,
                    ],
                ];

                $bulkParams['body'][] = [
                    'name' => $product->name,
                    'name_suggest' => ['input' => $this->elasticService->generateSuggestions($product->name)],
                    'description' => $product->description,
                    'price' => $product->price,
                    'categories' => $product->categories->pluck('name')->toArray(),
                ];
            }

            try {
                $response = $this->elasticService->getClient()->bulk($bulkParams);
                $this->output->writeln('Indexed a batch of '.count($products).' products.');
            } catch (\Exception $e) {
                $this->error('Bulk indexing error for products: '.$e->getMessage());
            }
        });

        $this->info('Bulk indexing Categories...');
        Category::chunk(1000, function ($categories) {
            $bulkParams = ['body' => []];

            foreach ($categories as $category) {
                $bulkParams['body'][] = [
                    'index' => [
                        '_index' => 'categories',
                        '_id' => $category->id,
                    ],
                ];

                $bulkParams['body'][] = [
                    'name' => $category->name,
                    'name_suggest' => ['input' => $this->elasticService->generateSuggestions($category->name)],
                    'description' => $category->description,
                ];
            }

            try {
                $response = $this->elasticService->getClient()->bulk($bulkParams);
                $this->output->writeln('Indexed a batch of '.count($categories).' categories.');
            } catch (\Exception $e) {
                $this->error('Bulk indexing error for categories: '.$e->getMessage());
            }
        });

        try {
            $this->elasticService->getClient()->indices()->refresh(['index' => 'products']);
            $this->elasticService->getClient()->indices()->refresh(['index' => 'categories']);
            $this->info('Indices refreshed.');
        } catch (\Exception $e) {
            $this->error('Error refreshing indices: '.$e->getMessage());
        }

        $this->info('Reindexing completed successfully.');
    }
}
