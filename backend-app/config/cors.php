<?php

return [

    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // عنوان واجهة React (Vite) — عدّله حسب بيئتك
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // نستخدم توكن Bearer وليس كوكيز، لذا لا حاجة لـ credentials
    'supports_credentials' => false,
];
