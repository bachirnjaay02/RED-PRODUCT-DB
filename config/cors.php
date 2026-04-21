<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [ 'https://red-product-front-1.vercel.app',
        'https://red-product-front-1-f4dz8ohpo-bachirnjaay02s-projects.vercel.app', // Ajoutez cette URL
    ],
    'allowed_origins_patterns' => ['#^https://red-product-.*\.vercel\.app$#'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];