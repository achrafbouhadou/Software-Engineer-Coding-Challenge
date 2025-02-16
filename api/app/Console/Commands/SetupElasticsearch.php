<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticsearchService;

class SetupElasticsearch extends Command
{
    protected $signature = 'elasticsearch:setup';
    protected $description = 'Create Elasticsearch indices for products and categories';


    public function __construct(protected ElasticsearchService $elasticsearchService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Deleting existing Elasticsearch indices...');
        $this->elasticsearchService->deleteProductIndex();
        $this->info('Product index deleted successfully.');
        $this->elasticsearchService->deleteCategoryIndex();
        $this->info('Category index deleted successfully.');
        $this->info('Creating Elasticsearch indices...');

        $this->elasticsearchService->createProductIndex();
        $this->info('Product index created.');

        $this->elasticsearchService->createCategoryIndex();
        $this->info('Category index created.');

        $this->info('Elasticsearch setup completed successfully.');
    }
}
