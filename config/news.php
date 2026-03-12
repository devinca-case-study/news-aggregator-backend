<?php

return [
    'providers' => [
        'newsapi' => [
            'base_url' => env('NEWSAPI_BASE_URL'),
            'api_key' => env('NEWSAPI_KEY'),
            'page_size' => env('NEWSAPI_PAGE_SIZE', 100),
            'total_page' => env('NEWSAPI_TOTAL_PAGE', 1),
            'rotation_minutes' => env('NEWSAPI_ROTATION_MINUTES', 30)
        ],
        'guardian' => [
            'base_url' => env('GUARDIAN_BASE_URL'),
            'api_key' => env('GUARDIAN_KEY'),
            'page_size' => env('GUARDIAN_PAGE_SIZE', 200),
            'total_page' => env('GUARDIAN_TOTAL_PAGE', 3),
            'rotation_minutes' => env('GUARDIAN_ROTATION_MINUTES', 15)
        ],
        'nytimes' => [
            'base_url' => env('NYTIMES_BASE_URL'),
            'api_key' => env('NYTIMES_KEY'),
            'total_page' => env('NYTIMES_TOTAL_PAGE', 6),
            'rotation_minutes' => env('NYTIMES_ROTATION_MINUTES', 30)
        ]
    ]
];