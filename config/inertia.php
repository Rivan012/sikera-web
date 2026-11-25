<?php

return [
    'ssr' => [
        'enabled' => (bool) env('INERTIA_SSR_ENABLED', false),
        'url' => env('INERTIA_SSR_URL', 'http://127.0.0.1:13714'),
    ],

    'pages' => [
        'ensure_pages_exist' => false,
        'paths' => [
            resource_path('js/Pages'),
            resource_path('js/pages'),
        ],
        'extensions' => [
            'js',
            'jsx',
            'ts',
            'tsx',
        ],
    ],

    'testing' => [
        'ensure_pages_exist' => true,
    ],
];
