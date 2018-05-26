<?php

return [
    'backend_host' => env('BACKEND_HOST', ''),
    'logging' =>[
        'enabled' => (bool)env('LOGGING_ENABLED', false)
    ]
];