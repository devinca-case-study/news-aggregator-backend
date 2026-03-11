<?php

return [
    'providers' => [
        'newsapi' => [
            'base_url' => env('NEWSAPI_BASE_URL'),
            'api_key' => env('NEWSAPI_KEY'),
            'page_size' => env('NEWSAPI_PAGE_SIZE', 100),
            'categories' => [
                'business',
                'entertainment',
                'general',
                'health',
                'science',
                'sports',
                'technology',
            ]
        ],
        'guardian' => [
            'base_url' => env('GUARDIAN_BASE_URL'),
            'api_key' => env('GUARDIAN_KEY')
        ],
        'nytimes' => [
            'base_url' => env('NYTIMES_BASE_URL'),
            'api_key' => env('NYTIMES_KEY')
        ]
    ]
];