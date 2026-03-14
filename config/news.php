<?php

return [
    'providers' => [
        'newsapi' => [
            'base_url' => env('NEWSAPI_BASE_URL'),
            'api_key' => env('NEWSAPI_KEY'),
            'page_size' => 100,
            'total_page' => 1,
            'rotation_minutes' => 30
        ],
        'guardian' => [
            'base_url' => env('GUARDIAN_BASE_URL'),
            'api_key' => env('GUARDIAN_KEY'),
            'page_size' => 200,
            'total_page' => 3,
            'rotation_minutes' => 15
        ],
        'nytimes' => [
            'base_url' => env('NYTIMES_BASE_URL'),
            'api_key' => env('NYTIMES_KEY'),
            'total_page' => 6,
            'rotation_minutes' => 30,
            'rate_limit_delay' => 12,
        ]
    ]
];