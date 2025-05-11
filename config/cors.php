<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000', // или порт vite (обычно 5173)
        'http://127.0.0.1:5173',  // обязательно указать Vite
        'https://your-production-site.com', // твой реальный прод-домен
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // <-- ВАЖНО
];

