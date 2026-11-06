<?php

$origins = array_filter(array_map('trim', explode(',', env(
    'FRONTEND_ORIGINS',
    'http://localhost:4200,http://127.0.0.1:4200,http://localhost:8100,http://127.0.0.1:8100,http://localhost,https://localhost,capacitor://localhost,ionic://localhost'
))));

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => $origins,
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
