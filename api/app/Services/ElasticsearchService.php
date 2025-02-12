<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])
            ->setRetries(2)
            ->build();
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     * Create/Update Product Index Mapping
     */
    public function createProductIndex()
    {
        $params = [
            'index' => 'products',
            'body'  => [
                'mappings' => [
                    'properties' => [
                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        'description' => [
                            'type' => 'text'
                        ],
                        'price' => [
                            'type' => 'float'
                        ],
                        'categories' => [
                            'type' => 'keyword'
                        ],
                        'name_suggest' => [
                            'type' => 'completion',
                            'analyzer' => 'simple',
                            'search_analyzer' => 'simple'
                        ]
                    ]
                ]
            ]
        ];

        $this->client->indices()->create($params);
    }


    /**
     * Index a product with autocomplete suggestions
     */
    public function indexProduct($product)
    {
        $name = (string) $product->name;
        $body = [
            'name' => $name,
            'description' => $product->description,
            'price' => $product->price,
            'categories' => $product->categories->pluck('name')->toArray()
        ];

        if (str_word_count($name) > 1) {
            $body['name_suggest'] = [
                'input' => $this->generateSuggestions($name)
            ];
        }

        $params = [
            'index' => 'products',
            'id' => $product->id,
            'body' => $body
        ];
        
        return $this->client->index($params);
    }

    /**
     * Generate suggestions for autocomplete
     */
    public function generateSuggestions($text)
    {
        $words = explode(' ', $text);
        $suggestions = [$text];
        
        for ($i = 0; $i < count($words); $i++) {
            $suggestion = '';
            for ($j = $i; $j < count($words); $j++) {
                $suggestion .= ' ' . $words[$j];
                $suggestions[] = trim($suggestion);
            }
        }
        
        return array_values(array_unique($suggestions));
    }

    /**
     * Search products with autocomplete
     */
    public function searchProducts($query, $size = 10)
    {

        $params = [
            'index' => 'products',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match' => [
                                    'name' => [
                                        'query' => $query,
                                        'boost' => 3
                                    ]
                                ]
                            ],
                            [
                                'match' => [
                                    'description' => [
                                        'query' => $query,
                                        'boost' => 1
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'suggest' => [
                    'name_suggestion' => [
                        'prefix' => $query,
                        'completion' => [
                            'field' => 'name_suggest',
                            'size' => 5,
                            'fuzzy' => [
                                'fuzziness' => 1
                            ]
                        ]
                    ]
                ],
                'size' => $size
            ]
        ];


        return $this->client->search($params);
    }

    public function createCategoryIndex()
    {
        $params = [
            'index' => 'categories',
            'body' => [
                'mappings' => [
                    'properties' => [
                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        // Explicitly define name_suggest as a completion field
                        'name_suggest' => [
                            'type' => 'completion',
                            'analyzer' => 'simple',
                            'search_analyzer' => 'simple'
                        ],
                    ]
                ]
            ]
        ];
    
        $this->client->indices()->create($params);
    }
    

    public function indexCategory($category)
    {
        $name = (string) $category->name;
        $body = [
            'name' => $name,
        ];

        if (str_word_count($category->name) > 1) {
            $body['name_suggest'] = [
                'input' => $this->generateSuggestions($name)
            ];
        }

        $params = [
            'index' => 'categories',
            'id'    => $category->id,
            'body'  => $body,
        ];

        return $this->client->index($params);
    }

    public function searchCategories($query, $size = 10)
    {
        $params = [
            'index' => 'categories',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match' => [
                                    'name' => [
                                        'query' => $query,
                                        'boost' => 3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'suggest' => [
                    'name_suggestion' => [
                        'prefix' => $query,
                        'completion' => [
                            'field' => 'name_suggest',
                            'size' => 5,
                            'fuzzy' => [
                                'fuzziness' => 1
                            ]
                        ]
                    ]
                ],
                'size' => $size
            ]
        ];

        return $this->client->search($params);
    }



}
